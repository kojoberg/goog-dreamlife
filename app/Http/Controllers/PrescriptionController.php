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

        Prescription::create($validated);

        return redirect()->route('prescriptions.index')->with('success', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription)
    {
        return view('prescriptions.show', compact('prescription'));
    }

    public function dispense(Request $request, Prescription $prescription)
    {
        if ($prescription->status === 'dispensed') {
            return back()->with('error', 'Prescription already dispensed.');
        }

        DB::beginTransaction();
        try {
            $medications = $prescription->medications; // Array from JSON cast
            $totalAmount = 0;
            $itemsToProcess = [];

            // 1. Calculate Total and Prepare Items
            foreach ($medications as $med) {
                if (!empty($med['product_id']) && !empty($med['quantity'])) {
                    $product = \App\Models\Product::find($med['product_id']);
                    if (!$product)
                        continue;

                    $qtyNeeded = (int) $med['quantity'];
                    $price = $product->unit_price; // Or branch price logic if needed
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

            // 2. Create Sale Record
            $sale = \App\Models\Sale::create([
                'user_id' => Auth::id(), // Pharmacist dispensing
                'patient_id' => $prescription->patient_id,
                'prescription_id' => $prescription->id,
                'total_amount' => $totalAmount,
                'subtotal' => $totalAmount, // Assuming no tax/discount logic for simple dispense yet
                'tax_amount' => 0,
                'payment_method' => $request->payment_method ?? 'cash', // From form
            ]);

            // 3. Process Items & Inventory
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
            // return redirect()->route('pos.receipt', $sale)->with('success', 'Dispensed & Sale Created.');
            return back()->with('success', 'Prescription dispensed. Sale #' . $sale->id . ' created.');

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
