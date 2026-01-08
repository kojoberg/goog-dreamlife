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
        // Calculate estimated total for display
        $estimatedTotal = 0;
        $medications = $prescription->medications ?? [];
        foreach ($medications as $med) {
            if (!empty($med['product_id']) && !empty($med['quantity'])) {
                $product = \App\Models\Product::find($med['product_id']);
                if ($product) {
                    $estimatedTotal += $product->unit_price * $med['quantity'];
                }
            }
        }

        $settings = \App\Models\Setting::first();

        return view('prescriptions.show', compact('prescription', 'estimatedTotal', 'settings'));
    }

    public function dispense(Request $request, Prescription $prescription)
    {
        if (!auth()->user()->hasPermission('dispense_medication')) {
            return back()->with('error', 'Unauthorized. You do not have permission to dispense medication.');
        }

        if ($prescription->status === 'dispensed') {
            return back()->with('error', 'Prescription already dispensed.');
        }

        DB::beginTransaction();
        try {
            $medications = $prescription->medications; // Array from JSON cast
            $totalAmount = 0;
            $itemsToProcess = [];

            // 1. Calculate Total and Prepare Items
            // 1. Calculate Total and Prepare Items
            foreach ($medications as $med) {
                if (!empty($med['product_id']) && !empty($med['quantity'])) {
                    $product = \App\Models\Product::find($med['product_id']);
                    if (!$product)
                        continue;

                    $qtyNeeded = (int) $med['quantity'];
                    $price = $product->unit_price;
                    $lineTotal = $price * $qtyNeeded;
                    $totalAmount += $lineTotal;

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

            // --- LOYALTY REDEMPTION LOGIC ---
            $settings = \App\Models\Setting::first();
            $pointsRedeemed = (int) $request->input('points_redeemed', 0);
            $discountAmount = 0;
            $subtotal = $totalAmount;

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
                    // Optional: Adjust points redeemed to match strict total? 
                    // For simplicity, we accept the points user explicitly chose, even if value exceeds bill slightly (unlikely UX).
                    // Better: Set discount to total, and maybe refund excess points? 
                    // Let's just clamp discount.
                } else {
                    $discountAmount = $maxDiscount;
                }

                $totalAmount = max(0, $totalAmount - $discountAmount);

                // Deduct points from patient
                $patient->decrement('loyalty_points', $pointsRedeemed);
            }
            // --------------------------------

            // 2. Create Sale Record with PENDING_PAYMENT status
            // Pharmacist dispenses, creates invoice - Cashier collects payment
            $sale = \App\Models\Sale::create([
                'user_id' => Auth::id(), // Pharmacist dispensing
                'patient_id' => $prescription->patient_id,
                'prescription_id' => $prescription->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'points_redeemed' => $pointsRedeemed,
                'tax_amount' => 0,
                'status' => 'pending_payment', // Cashier will complete payment
                'payment_method' => null, // Set when cashier processes payment
            ]);

            // NOTE: Loyalty points earning happens when cashier completes payment
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

            // Redirect to Sale Receipt or back with success
            return back()->with([
                'success' => 'Prescription dispensed. Invoice #' . $sale->id . ' created. Patient can proceed to Cashier for payment.',
                'success_sale_id' => $sale->id
            ]);

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
