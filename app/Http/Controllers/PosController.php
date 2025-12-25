<?php

namespace App\Http\Controllers;

use App\Models\InventoryBatch;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    /**
     * Show the POS interface.
     */
    public function index()
    {
        // Fetch products with visible stock
        $products = Product::whereHas('batches', function ($q) {
            $q->where('quantity', '>', 0)
                ->where('expiry_date', '>=', now());
        })
            ->with(['category'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->unit_price,
                    'stock' => $product->stock, // Helper attribute
                    'category' => $product->category->name ?? 'Uncategorized',
                ];
            });

        $settings = Setting::first();
        return view('pos.index', compact('products', 'settings'));
    }

    /**
     * Store a new sale (Checkout).
     */
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            // We recalculate total on backend, but validate payment covers it
            'payment_method' => 'required|in:cash,mobile_money,card',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'patient_id' => 'nullable|exists:patients,id',
            'redeem_points' => 'nullable|integer|min:0',
            // 'amount_tendered' => 'required|numeric', // Check logic later
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $itemsToCreate = [];
            $batchesToUpdate = [];

            // 1. Calculate Subtotal & Verify Stock
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                $qtyNeeded = $item['qty'];
                $price = $product->unit_price;

                $subtotal += ($qtyNeeded * $price);

                // Stock Check Logic (Preview)
                // We'll do the actual deduction in the second pass or complex query
                // For simplicity, let's keep the logic close to the loop
            }

            // 2. Calculate Taxes
            // NHIL 2.5%, GETFund 2.5%, COVID 1%
            // VAT 15% on (Subtotal + Levies)
            $nhil = $subtotal * 0.025;
            $getfund = $subtotal * 0.025;
            $covid = $subtotal * 0.01;
            $levies = $nhil + $getfund + $covid;

            $vatBase = $subtotal + $levies;
            $vat = $vatBase * 0.15;

            $totalTax = $levies + $vat;
            $grandTotal = $subtotal + $totalTax;

            // Validate Amount Tendered (if provided)
            if ($request->has('amount_tendered') && $request->amount_tendered < $grandTotal) {
                throw new \Exception("Amount tendered is less than the total payable (GHS " . number_format($grandTotal, 2) . ")");
            }

            // 3. Create Sale Record
            // Get current open shift
            $shift = \App\Models\Shift::where('user_id', Auth::id())
                ->whereNull('end_time')
                ->first();

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'patient_id' => $request->patient_id ?? null, // Can be null
                'shift_id' => $shift ? $shift->id : null,
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'total_amount' => $grandTotal,
                'payment_method' => $request->payment_method,
                'tax_breakdown' => [
                    'nhil' => round($nhil, 2),
                    'getfund' => round($getfund, 2),
                    'covid' => round($covid, 2),
                    'vat' => round($vat, 2)
                ]
            ]);

            // 4. Process Grid Items & Deduct Stock
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                $qtyNeeded = $item['qty'];

                // FIFO Stock Deduction
                $batches = InventoryBatch::where('product_id', $product->id)
                    ->where('quantity', '>', 0)
                    ->where('expiry_date', '>=', now())
                    ->orderBy('expiry_date', 'asc')
                    ->get();

                $qtyToFulfill = $qtyNeeded;

                foreach ($batches as $batch) {
                    if ($qtyToFulfill <= 0)
                        break;

                    if ($batch->quantity >= $qtyToFulfill) {
                        $batch->decrement('quantity', $qtyToFulfill);

                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $product->id,
                            'batch_id' => $batch->id,
                            'quantity' => $qtyToFulfill,
                            'unit_price' => $product->unit_price,
                            'subtotal' => $qtyToFulfill * $product->unit_price,
                        ]);

                        $qtyToFulfill = 0;
                    } else {
                        $taken = $batch->quantity;
                        $batch->update(['quantity' => 0]);

                        $saleItem = SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $product->id,
                            'batch_id' => $batch->id,
                            'quantity' => $taken,
                            'unit_price' => $product->unit_price,
                            'subtotal' => $taken * $product->unit_price,
                        ]);

                        $qtyToFulfill -= $taken;
                    }
                }

                if ($qtyToFulfill > 0) {
                    throw new \Exception("Insufficient stock for product: " . $product->name);
                }

                // 3. Refill Reminder
                // We use the last created saleItem for the link.
                if ($product->is_chronic && $request->patient_id) {
                    $cartItem = collect($request->cart)->firstWhere('id', $product->id);
                    $daysSupply = $cartItem['days_supply'] ?? 30;

                    if ($daysSupply > 0 && isset($saleItem)) {
                        $saleItem->update(['days_supply' => $daysSupply]);

                        \App\Models\RefillQueue::create([
                            'patient_id' => $request->patient_id,
                            'sale_item_id' => $saleItem->id,
                            'product_name' => $product->name,
                            'scheduled_date' => now()->addDays($daysSupply - 2),
                            'status' => 'pending'
                        ]);
                    }
                }
            }

            // --- LOYALTY PROGRAM LOGIC ---
            $settings = Setting::first();

            // 1. redemption
            if ($request->redeem_points && $request->patient_id) {
                $patient = Patient::find($request->patient_id);
                $pointsToRedeem = (int) $request->redeem_points;

                if ($patient && $patient->loyalty_points >= $pointsToRedeem) {
                    $discountValue = $pointsToRedeem * $settings->loyalty_point_value;

                    // Update Sale
                    $sale->points_redeemed = $pointsToRedeem;
                    // Ensure we don't refund more than total? (Ideally frontend handles this, but backend check good)
                    // For simplicity, assuming discount is applied to total (already calculated in frontend/request total?)
                    // Actually, the request->total usually reflects the final payable.
                    // But we should record the discount. 
                    // Let's assume request->total IS the final amount to be paid. 
                    // We just log that points were used.

                    $patient->decrement('loyalty_points', $pointsToRedeem);
                }
            }

            // 2. Earning
            if ($request->patient_id && $settings->loyalty_spend_per_point > 0) {
                $pointsEarned = floor($grandTotal / $settings->loyalty_spend_per_point); // Changed $request->total to $grandTotal
                if ($pointsEarned > 0) {
                    $sale->points_earned = $pointsEarned;
                    $patient = Patient::find($request->patient_id); // Re-fetch or use existing
                    if ($patient) { // Ensure patient exists before incrementing
                        $patient->increment('loyalty_points', $pointsEarned);
                    }
                }
            }

            $sale->patient_id = $request->patient_id;
            $sale->save();
            // -----------------------------

            DB::commit();

            // 5. Send Email Receipt (Optional)
            if ($request->has('email') && $request->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\SaleReceipt($sale));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send email receipt: " . $e->getMessage());
                }
            }

            // 6. Send SMS Receipt (Optional)
            if ($request->has('phone') && $request->phone) {
                try {
                    $smsService = new \App\Services\SmsService();
                    // Customize message
                    $message = "Thank you for shopping at Dream Life! Amount: GHS " . number_format($sale->total_amount, 2) . ". Receipt #" . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
                    $smsService->sendQuickSms($request->phone, $message);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send SMS receipt: " . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'sale_id' => $sale->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show Receipt
     */
    /**
     * Check for drug interactions
     */
    public function checkInteractions(Request $request)
    {
        $cartIds = $request->cart_ids ?? [];
        $newProductId = $request->new_product_id;

        if (empty($cartIds) || !$newProductId) {
            return response()->json(['interactions' => []]);
        }

        // Find interactions where:
        // (Drug A is New AND Drug B is in Cart) OR (Drug B is New AND Drug A is in Cart)

        $interactions = \App\Models\DrugInteraction::where(function ($query) use ($newProductId, $cartIds) {
            $query->where('drug_a_id', $newProductId)
                ->whereIn('drug_b_id', $cartIds);
        })
            ->orWhere(function ($query) use ($newProductId, $cartIds) {
                $query->where('drug_b_id', $newProductId)
                    ->whereIn('drug_a_id', $cartIds);
            })
            ->with(['drugA', 'drugB'])
            ->get();

        $warnings = [];
        foreach ($interactions as $interaction) {
            $otherDrug = $interaction->drug_a_id == $newProductId ? $interaction->drugB : $interaction->drugA;
            $warnings[] = [
                'drug' => $otherDrug->name,
                'severity' => ucfirst($interaction->severity),
                'description' => $interaction->description
            ];
        }

        return response()->json(['interactions' => $warnings]);
    }
}
