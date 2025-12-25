<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('prescriptions.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'nullable|string',
            'medications' => 'required|array',
            'medications.*.name' => 'required|string',
            'medications.*.dosage' => 'required|string',
            'medications.*.frequency' => 'required|string',
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
        $prescription->update(['status' => 'dispensed']);
        return back()->with('success', 'Prescription marked as dispensed.');
    }
}
