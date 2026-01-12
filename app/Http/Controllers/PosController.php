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
    protected $inventoryService;

    public function __construct(\App\Services\InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }
    /**
     * Show the POS interface.
     */
    public function index()
    {
        if (!auth()->user()->hasPermission('access_pos')) {
            abort(403, 'Unauthorized. Access to POS is restricted.');
        }

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
        if (!auth()->user()->hasPermission('access_pos')) {
            abort(403, 'Unauthorized. Access to POS is restricted.');
        }

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

        // Enforce Shift Check
        if (!Auth::user()->hasOpenShift()) {
            return response()->json([
                'success' => false, // or handle as error 
                'message' => 'You must open a shift before processing sales.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $taxableSubtotal = 0; // Track taxable items separately
            $itemsToCreate = [];
            $batchesToUpdate = [];

            // 1. Calculate Subtotal & Verify Stock
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                $qtyNeeded = $item['qty'];

                // Use Branch Price
                $branchId = Auth::user()->branch_id;
                $price = $product->getPriceForBranch($branchId);

                $itemTotal = $qtyNeeded * $price;
                $subtotal += $itemTotal;

                // Only add to taxable subtotal if product is not tax exempt
                if (!$product->tax_exempt) {
                    $taxableSubtotal += $itemTotal;
                }

                // Stock Check Logic (Preview)
                // We'll do the actual deduction in the second pass or complex query
                // For simplicity, let's keep the logic close to the loop
            }

            // 2. Calculate Taxes using INCLUSIVE pricing (tax is already in product prices)
            // Product prices include tax - we extract/back-calculate the tax component
            $grandTotal = $subtotal; // Grand total stays same - prices already include tax
            $settings = Setting::first();
            $taxEnabled = $settings->enable_tax;

            if ($taxEnabled && $taxableSubtotal > 0) {
                // Use INCLUSIVE tax calculation - extract tax from prices
                $taxData = \App\Models\TaxRate::calculateInclusiveBreakdown($taxableSubtotal);
                $totalTax = $taxData['total_tax'];
                $baseAmount = $taxData['base_amount'];

                // Convert breakdown to simple format for storage
                $taxBreakdown = [];
                foreach ($taxData['breakdown'] as $code => $data) {
                    $taxBreakdown[$code] = $data;
                }

                // For storage purposes, subtotal represents the base (pre-tax) amount of taxable items
                // Plus the full amount of exempt items
                $exemptAmount = $subtotal - $taxableSubtotal;
                $subtotal = $baseAmount + $exemptAmount;
                // grandTotal stays the same - it's what customer pays (prices include tax)
            } else {
                // No Tax
                $totalTax = 0;
                $taxBreakdown = null;
            }

            // --- LOYALTY PROGRAM LOGIC (MOVED UP) ---
            $settings = Setting::first();
            $discountValue = 0;
            $pointsToRedeem = 0;
            $pointsEarned = 0;

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
                }
            }

            // Calculate Final Payable Amount
            $netPayable = $grandTotal - $discountValue;

            // 3. Create Sale Record
            // Check for Cashier Mode (Split Workflow)
            $user = Auth::user();
            $hasCashier = $user->branch && $user->branch->has_cashier;

            // Validate Amount Tendered (if provided) - ONLY if direct completion (not Cashier Mode)
            if (!$hasCashier && $request->has('amount_tendered') && $request->amount_tendered < $netPayable) {
                throw new \Exception("Amount tendered is less than the total payable (GHS " . number_format($netPayable, 2) . ")");
            }

            $status = $hasCashier ? 'pending_payment' : 'completed';

            // Get current open shift
            $shift = \App\Models\Shift::where('user_id', Auth::id())
                ->whereNull('end_time')
                ->first();

            $amountTendered = $hasCashier ? 0 : ($request->amount_tendered ?? $netPayable);
            $changeAmount = $amountTendered - $netPayable;

            // 2. Earning (Based on Net Payable Amount) - Only award if sale is completed directly
            // If pending_payment, points are awarded when cashier processes the payment
            if (!$hasCashier && $request->patient_id && $settings->loyalty_spend_per_point > 0 && $netPayable > 0) {
                $pointsEarned = floor($netPayable / $settings->loyalty_spend_per_point);
                if ($pointsEarned > 0) {
                    $patient = Patient::find($request->patient_id);
                    if ($patient) {
                        $patient->increment('loyalty_points', $pointsEarned);
                    }
                }
            }

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'patient_id' => $request->patient_id ?? null,
                'shift_id' => $shift ? $shift->id : null,
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'discount_amount' => $discountValue, // Save discount
                'total_amount' => $netPayable, // Save discounted total
                'amount_tendered' => $amountTendered,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
                'tax_breakdown' => $taxBreakdown,
                'points_redeemed' => $pointsToRedeem,
                'points_earned' => $pointsEarned,
                'status' => $status
            ]);

            // 4. Process Items & Deduct Stock
            foreach ($request->cart as $cartItem) {
                $product = Product::find($cartItem['id']);
                $qtyRequested = $cartItem['qty'];

                // Re-fetch price to be safe/consistent
                $branchId = Auth::user()->branch_id;
                $unitPrice = $product->getPriceForBranch($branchId);

                // Try to deduct stock
                // This throws Exception if Refill failed or insufficient
                $deductions = $this->inventoryService->deductStock($product, $qtyRequested);

                if (empty($deductions) && $product->product_type === 'service') {
                    // Service Item (No Batch)
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'batch_id' => null,
                        'quantity' => $qtyRequested,
                        'unit_price' => $unitPrice,
                        'subtotal' => $qtyRequested * $unitPrice,
                    ]);
                } else {
                    // Physical Items (Batched)
                    foreach ($deductions as $deduction) {
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $product->id,
                            'batch_id' => $deduction['batch_id'],
                            'quantity' => $deduction['quantity'],
                            'unit_price' => $unitPrice,
                            'subtotal' => $deduction['quantity'] * $unitPrice,
                        ]);
                    }
                }
            }

            DB::commit();

            // 5. Send Email Receipt (Only if Completed)
            if ($status === 'completed' && $request->has('email') && $request->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\SaleReceipt($sale));
                    // Log email to communication log
                    \App\Models\CommunicationLog::create([
                        'type' => 'email',
                        'recipient' => $request->email,
                        'message' => 'Sale Receipt #' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                        'status' => 'sent',
                        'context' => 'pos_receipt',
                        'user_id' => auth()->id(),
                        'branch_id' => auth()->user()?->branch_id,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send email receipt: " . $e->getMessage());
                    // Log failed email
                    \App\Models\CommunicationLog::create([
                        'type' => 'email',
                        'recipient' => $request->email,
                        'message' => 'Sale Receipt #' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                        'status' => 'failed',
                        'response' => $e->getMessage(),
                        'context' => 'pos_receipt',
                        'user_id' => auth()->id(),
                        'branch_id' => auth()->user()?->branch_id,
                    ]);
                }
            }

            // 6. Send SMS Receipt (Only if Completed)
            if ($status === 'completed' && $request->has('phone') && $request->phone) {
                try {
                    $smsService = new \App\Services\SmsService();
                    // Customize message
                    $message = "Thank you for shopping at Dream Life! Amount: GHS " . number_format($sale->total_amount, 2) . ". Receipt #" . str_pad($sale->id, 6, '0', STR_PAD_LEFT);

                    if ($sale->patient) {
                        $earned = $sale->points_earned ?? 0;
                        $balance = $sale->patient->loyalty_points; // Contains updated balance
                        $message .= "\nPts Earned: $earned. Total Pts: $balance";
                    }
                    $smsService->sendQuickSms($request->phone, $message, 'pos_receipt');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send SMS receipt: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'type' => $status === 'pending_payment' ? 'invoice' : 'receipt'
            ]);

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
        $sale->load(['items.product', 'user', 'patient', 'shift.user', 'cashierShift.user']);
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

            // Skip if the other drug is deleted/missing
            if (!$otherDrug) {
                continue;
            }

            $warnings[] = [
                'drug' => $otherDrug->name,
                'severity' => ucfirst($interaction->severity),
                'description' => $interaction->description
            ];
        }

        return response()->json(['interactions' => $warnings]);
    }
}
