<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
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

    public function dispense(Prescription $prescription)
    {
        if ($prescription->status === 'dispensed') {
            return back()->with('error', 'Prescription already dispensed.');
        }

        DB::beginTransaction();
        try {
            $medications = $prescription->medications; // Array from JSON cast

            foreach ($medications as $med) {
                if (!empty($med['product_id']) && !empty($med['quantity'])) {
                    $productId = $med['product_id'];
                    $qtyNeeded = (int) $med['quantity'];

                    // FIFO Deduction Logic
                    $batches = \App\Models\InventoryBatch::where('product_id', $productId)
                        ->where('quantity', '>', 0)
                        ->orderBy('expiry_date', 'asc')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($qtyNeeded <= 0)
                            break;

                        if ($batch->quantity >= $qtyNeeded) {
                            $batch->decrement('quantity', $qtyNeeded);
                            $qtyNeeded = 0;
                        } else {
                            $taken = $batch->quantity;
                            $batch->update(['quantity' => 0]);
                            $qtyNeeded -= $taken;
                        }
                    }

                    if ($qtyNeeded > 0) {
                        throw new \Exception("Insufficient stock for medication: " . $med['name']); // Rollback
                    }

                    // Refill Reminder Logic
                    if (!empty($med['refill_reminder']) && $med['refill_reminder'] == true && !empty($med['days_supply'])) {
                        \App\Models\RefillQueue::create([
                            'patient_id' => $prescription->patient_id,
                            'product_name' => $med['name'],
                            'scheduled_date' => now()->addDays((int) $med['days_supply'] - 2), // 2 days before
                            'status' => 'pending'
                        ]);
                    }
                }
            }

            $prescription->update(['status' => 'dispensed']);
            DB::commit();
            return back()->with('success', 'Prescription dispensed and inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Dispense Failed: ' . $e->getMessage());
        }
    }
}
