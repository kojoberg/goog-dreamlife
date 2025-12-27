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
        // Fetch products: EITHER (Goods with stock > 0) OR (Services)
        $products = Product::where(function ($query) {
            $query->where('product_type', 'service')
                ->orWhereHas('batches', function ($q) {
                    $q->where('quantity', '>', 0)
                        ->where('expiry_date', '>=', now());
                });
        })
            ->with(['category'])
            ->get()
            ->map(function ($product) {
                // Determine price based on branch
                $branchId = Auth::user()->branch_id;
                $price = $product->getPriceForBranch($branchId);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $price,
                    // If service, stock is essentially infinite (or just hidden) for UI. Let's say 999
                    'stock' => $product->product_type === 'service' ? 9999 : $product->stock,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'type' => $product->product_type, // Useful for JS
                    'dosage' => $product->dosage,
                    'form' => $product->drug_form,
                    'route' => $product->drug_route,
                    'barcode' => $product->barcode, // For scanner Search
                ];
            });

        $settings = Setting::firstOrCreate(
            ['id' => 1],
            [
                'business_name' => 'Dream Life Healthcare',
                'address' => '123 Health Street, City',
                'phone' => '+233 00 000 0000',
                'email' => 'info@dreamlife.com',
                'currency_symbol' => 'GHS'
            ]
        );
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

                // Use Branch Price
                $branchId = Auth::user()->branch_id;
                $price = $product->getPriceForBranch($branchId);

                $subtotal += ($qtyNeeded * $price);

                // Stock Check Logic (Preview)
                // We'll do the actual deduction in the second pass or complex query
                // For simplicity, let's keep the logic close to the loop
            }

            // 2. Calculate Taxes (INCLUSIVE)
            $grandTotal = $subtotal; // Product Price includes everything if tax enabled
            $settings = Setting::first();
            $taxEnabled = $settings->enable_tax;

            if ($taxEnabled) {
                // Formula: Price = Cost + Levies + VAT
                // Total = 1.06 * 1.15 * Base = 1.219 * Base
                $baseAmount = $grandTotal / 1.219;

                $levies = $baseAmount * 0.06;
                $nhil = $baseAmount * 0.025;
                $getfund = $baseAmount * 0.025;
                $covid = $baseAmount * 0.01;

                $vatBase = $baseAmount + $nhil + $getfund + $covid;
                $vat = $vatBase * 0.15;

                // Re-assign for DB storage
                $subtotal = $baseAmount;
                $totalTax = $grandTotal - $baseAmount;

                $taxBreakdown = [
                    'nhil' => round($nhil, 2),
                    'getfund' => round($getfund, 2),
                    'covid' => round($covid, 2),
                    'vat' => round($vat, 2)
                ];
            } else {
                // No Tax
                $subtotal = $grandTotal;
                $totalTax = 0;
                $taxBreakdown = null;
            }

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
                'tax_breakdown' => $taxBreakdown
            ]);

            // 4. Process Grid Items & Deduct Stock
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                $qtyNeeded = $item['qty'];

                if ($product->product_type === 'service') {
                    // Just create SaleItem, no stock deduction
                    // Resolve price again to be safe
                    $branchId = Auth::user()->branch_id;
                    $unitPrice = $product->getPriceForBranch($branchId);

                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'batch_id' => null, // No batch for services
                        'quantity' => $qtyNeeded,
                        'unit_price' => $unitPrice,
                        'subtotal' => $qtyNeeded * $unitPrice,
                    ]);
                    continue; // Skip batch logic
                }

                // FIFO Stock Deduction for GOODS
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

                        $saleItem = SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $product->id,
                            'batch_id' => $batch->id,
                            'quantity' => $qtyToFulfill,
                            'unit_price' => $product->getPriceForBranch(Auth::user()->branch_id),
                            'subtotal' => $qtyToFulfill * $product->getPriceForBranch(Auth::user()->branch_id),
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
                            'unit_price' => $product->getPriceForBranch(Auth::user()->branch_id),
                            'subtotal' => $taken * $product->getPriceForBranch(Auth::user()->branch_id),
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
            $discountValue = 0;

            // 1. Redemption (Calculate Discount)
            if ($request->redeem_points && $request->patient_id) {
                $patient = Patient::find($request->patient_id);
                $pointsToRedeem = (int) $request->redeem_points;

                if ($patient && $patient->loyalty_points >= $pointsToRedeem) {
                    $discountValue = $pointsToRedeem * $settings->loyalty_point_value;

                    // Cap discount at grandTotal (Cannot go below zero)
                    if ($discountValue > $grandTotal) {
                        $discountValue = $grandTotal;
                    }

                    // Deduct Points
                    $patient->decrement('loyalty_points', $pointsToRedeem);

                    // Update Sale
                    $sale->points_redeemed = $pointsToRedeem;
                    $sale->discount_amount = $discountValue;

                    // Adjust Total Amount (Net Payable)
                    $sale->total_amount = $grandTotal - $discountValue;
                    // Note: Tax is still based on the original subtotal (statutory requirement usually).
                }
            }

            // 2. Earning (Based on Net Paybale Amount, or Gross? Usually Net)
            // Let's assume points are earned on the amount actually paid (Net).
            $netPayable = $grandTotal - $discountValue;

            if ($request->patient_id && $settings->loyalty_spend_per_point > 0 && $netPayable > 0) {
                $pointsEarned = floor($netPayable / $settings->loyalty_spend_per_point);
                if ($pointsEarned > 0) {
                    $sale->points_earned = $pointsEarned;
                    $patient = Patient::find($request->patient_id);
                    if ($patient) {
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

                    if ($sale->patient) {
                        $earned = $sale->points_earned ?? 0;
                        $balance = $sale->patient->loyalty_points; // Contains updated balance
                        $message .= "\nPts Earned: $earned. Total Pts: $balance";
                    }
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
    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'user', 'patient']);
        $settings = Setting::first(); // Should exist from index() or seed

        // Fallback for settings if null (prevent crash)
        if (!$settings) {
            $settings = new Setting([
                'business_name' => 'Dream Life Healthcare',
                'address' => 'Accra, Ghana',
                'phone' => '000-000-0000',
                'currency_symbol' => 'GHS'
            ]);
        }

        return view('pos.receipt', compact('sale', 'settings'));
    }
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
