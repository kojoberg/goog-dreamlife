<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    protected $inventoryService;

    public function __construct(\App\Services\InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }
    public function index()
    {
        $prescriptions = Prescription::with(['doctor', 'patient'])->latest()->paginate(10);
        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        // Pass products for selection
        $products = \App\Models\Product::orderBy('name')->get();
        return view('prescriptions.create', compact('patients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'nullable|string',
            'medications' => 'required|array',
            'medications.*.name' => 'required|string',
            'medications.*.product_id' => 'nullable|exists:products,id', // Link to DB
            'medications.*.dosage' => 'required|string',
            'medications.*.frequency' => 'required|string',
            'medications.*.quantity' => 'nullable|integer|min:1', // Needed for deduction
            'medications.*.route' => 'nullable|string',
            'medications.*.form' => 'nullable|string',
            'medications.*.days_supply' => 'nullable|integer|min:1',
            'medications.*.refill_reminder' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();

        $prescription = Prescription::create($validated);

        return redirect()->route('prescriptions.show', $prescription)->with('success', 'Prescription created successfully. You can now dispense it.');
    }

    public function show(Prescription $prescription)
    {
        // Calculate estimated total and tax for display
        $estimatedSubtotal = 0;
        $taxableSubtotal = 0;
        $medications = $prescription->medications ?? [];

        foreach ($medications as $med) {
            if (!empty($med['product_id']) && !empty($med['quantity'])) {
                $product = \App\Models\Product::find($med['product_id']);
                if ($product) {
                    $lineTotal = $product->unit_price * $med['quantity'];
                    $estimatedSubtotal += $lineTotal;

                    // Track taxable items
                    if (!$product->tax_exempt) {
                        $taxableSubtotal += $lineTotal;
                    }
                }
            }
        }

        $settings = \App\Models\Setting::first();

        // Calculate estimated tax using inclusive pricing
        $estimatedTax = 0;
        $taxBreakdown = [];
        if ($settings && $settings->enable_tax && $taxableSubtotal > 0) {
            $taxData = \App\Models\TaxRate::calculateInclusiveBreakdown($taxableSubtotal);
            $estimatedTax = $taxData['total_tax'];
            $taxBreakdown = $taxData['breakdown'] ?? [];
        }

        // Total is same as subtotal (inclusive pricing)
        $estimatedTotal = $estimatedSubtotal;

        return view('prescriptions.show', compact(
            'prescription',
            'estimatedTotal',
            'estimatedSubtotal',
            'estimatedTax',
            'taxBreakdown',
            'settings'
        ));
    }

    public function dispense(Request $request, Prescription $prescription)
    {
        if (!auth()->user()->hasPermission('dispense_medication')) {
            return back()->with('error', 'Unauthorized. You do not have permission to dispense medication.');
        }

        // Enforce Shift Check - must have open shift to dispense
        if (!Auth::user()->hasOpenShift()) {
            return redirect()->route('shifts.create')
                ->with('error', 'You must open a shift before dispensing prescriptions.');
        }

        if ($prescription->status === 'dispensed') {
            return back()->with('error', 'Prescription already dispensed.');
        }

        // Check if cashier workflow is enabled
        $user = Auth::user();
        $hasCashierWorkflow = $user->branch && $user->branch->has_cashier;

        // Validate payment method only if no cashier workflow (pharmacist completes sale directly)
        if (!$hasCashierWorkflow) {
            $request->validate([
                'payment_method' => 'required|in:cash,mobile_money,card',
            ]);
        }

        DB::beginTransaction();
        try {
            $medications = $prescription->medications; // Array from JSON cast
            $subtotal = 0;
            $taxableSubtotal = 0;
            $itemsToProcess = [];

            // 1. Calculate Total and Prepare Items
            foreach ($medications as $med) {
                if (!empty($med['product_id']) && !empty($med['quantity'])) {
                    $product = \App\Models\Product::find($med['product_id']);
                    if (!$product)
                        continue;

                    $qtyNeeded = (int) $med['quantity'];
                    $price = $product->unit_price;
                    $lineTotal = $price * $qtyNeeded;
                    $subtotal += $lineTotal;

                    // Track taxable items (products not marked as tax exempt)
                    if (!$product->tax_exempt) {
                        $taxableSubtotal += $lineTotal;
                    }

                    $itemsToProcess[] = [
                        'product' => $product,
                        'quantity' => $qtyNeeded,
                        'price' => $price,
                        'line_total' => $lineTotal,
                        'days_supply' => $med['days_supply'] ?? 0,
                        'refill_reminder' => $med['refill_reminder'] ?? false,
                        'med_name' => $med['name'] // Preserve name from prescription
                    ];
                }
            }

            // 2. Calculate Taxes using INCLUSIVE pricing (like POS)
            $settings = \App\Models\Setting::first();
            $grandTotal = $subtotal; // Prices already include tax
            $totalTax = 0;
            $taxBreakdown = null;

            if ($settings && $settings->enable_tax && $taxableSubtotal > 0) {
                $taxData = \App\Models\TaxRate::calculateInclusiveBreakdown($taxableSubtotal);
                $totalTax = $taxData['total_tax'];
                $baseAmount = $taxData['base_amount'];

                // Store breakdown for receipt
                $taxBreakdown = [];
                foreach ($taxData['breakdown'] as $code => $data) {
                    $taxBreakdown[$code] = $data;
                }

                // Adjust subtotal to show base amount
                $exemptAmount = $subtotal - $taxableSubtotal;
                $subtotal = $baseAmount + $exemptAmount;
            }

            // --- LOYALTY REDEMPTION LOGIC ---
            $pointsRedeemed = (int) $request->input('points_redeemed', 0);
            $discountAmount = 0;
            $totalAmount = $grandTotal;

            if ($pointsRedeemed > 0 && $settings && $settings->loyalty_point_value > 0) {
                $patient = \App\Models\Patient::find($prescription->patient_id);
                // Validate balance
                if (!$patient || $patient->loyalty_points < $pointsRedeemed) {
                    throw new \Exception("Insufficient loyalty points balance.");
                }

                $maxDiscount = $pointsRedeemed * $settings->loyalty_point_value;

                // Cap discount at total amount (cannot pay negative)
                if ($maxDiscount > $totalAmount) {
                    $discountAmount = $totalAmount;
                } else {
                    $discountAmount = $maxDiscount;
                }

                $totalAmount = max(0, $totalAmount - $discountAmount);

                // Deduct points from patient
                $patient->decrement('loyalty_points', $pointsRedeemed);
            }
            // --------------------------------

            // 3. Create Sale Record
            // Get the current user's open shift (if any)
            $currentShift = \App\Models\Shift::where('user_id', Auth::id())
                ->whereNull('end_time')
                ->first();

            // Determine payment method: from form if no cashier workflow, null if cashier will complete
            $paymentMethod = $hasCashierWorkflow ? null : $request->input('payment_method', 'cash');

            $sale = \App\Models\Sale::create([
                'user_id' => Auth::id(), // Pharmacist dispensing
                'patient_id' => $prescription->patient_id,
                'prescription_id' => $prescription->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'points_redeemed' => $pointsRedeemed,
                'tax_amount' => $totalTax,
                'tax_breakdown' => $taxBreakdown,
                'status' => $hasCashierWorkflow ? 'pending_payment' : 'completed',
                'payment_method' => $paymentMethod,
                'shift_id' => $currentShift?->id, // Link to current shift for reporting
            ]);

            // If no cashier workflow, award loyalty points immediately
            // (Same logic as PosController)
            $pointsEarned = 0;
            if (!$hasCashierWorkflow && $settings && $settings->loyalty_spend_per_point > 0 && $totalAmount > 0) {
                $patient = \App\Models\Patient::find($prescription->patient_id);
                if ($patient) {
                    $pointsEarned = floor($totalAmount / $settings->loyalty_spend_per_point);
                    if ($pointsEarned > 0) {
                        $patient->increment('loyalty_points', $pointsEarned);
                        // Update sale with points earned
                        $sale->update(['points_earned' => $pointsEarned]);
                    }
                }
            }

            // NOTE: Loyalty points earning happens when cashier completes payment (if cashier workflow enabled)
            // This is handled in CashierController@update
            foreach ($itemsToProcess as $item) {
                $product = $item['product'];

                // Deduct via Service
                $deductions = $this->inventoryService->deductStock($product, $item['quantity']);

                foreach ($deductions as $deduction) {
                    $saleItem = \App\Models\SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'batch_id' => $deduction['batch_id'],
                        'quantity' => $deduction['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $deduction['quantity'] * $item['price'],
                    ]);

                    // Refill Reminder Logic (LINKED TO SALE ITEM)
                    if ($item['refill_reminder'] && $item['days_supply'] > 0) {
                        // Update sales item tracking if needed, or simply queue the reminder
                        \App\Models\RefillQueue::create([
                            'patient_id' => $prescription->patient_id,
                            'sale_item_id' => $saleItem->id, // Linked to actual sale
                            'product_name' => $item['med_name'],
                            'scheduled_date' => now()->addDays((int) $item['days_supply'] - 2),
                            'status' => 'pending'
                        ]);
                    }
                }
            }

            $prescription->update(['status' => 'dispensed']);
            DB::commit();

            // Redirect message depends on cashier workflow
            if ($hasCashierWorkflow) {
                return back()->with([
                    'success' => 'Prescription dispensed. Invoice #' . $sale->id . ' created. Patient can proceed to Cashier for payment.',
                    'success_sale_id' => $sale->id
                ]);
            } else {
                return back()->with([
                    'success' => 'Prescription dispensed and sale completed. Receipt #' . $sale->id,
                    'success_sale_id' => $sale->id
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Dispense Failed: ' . $e->getMessage());
        }
    }

    public function refill(Prescription $prescription)
    {
        // Clone the prescription
        $newPrescription = $prescription->replicate();

        // Reset specific fields
        $newPrescription->status = 'pending';
        $newPrescription->created_at = now();
        $newPrescription->updated_at = now();
        $newPrescription->user_id = Auth::id(); // Pharmacist requesting refill

        // Append note
        $newPrescription->notes = ($prescription->notes ? $prescription->notes . "\n" : "") . "(Refill of #" . $prescription->id . ")";

        $newPrescription->save();

        return redirect()->route('prescriptions.show', $newPrescription)
            ->with('success', 'Refill prescription created successfully. You can now dispense it.');
    }
}
