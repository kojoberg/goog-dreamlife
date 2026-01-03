<?php

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

// 1. Create Patient
echo "1. Creating Patient...\n";
$patient = Patient::create([
    'name' => 'Backend Verify Patient',
    'phone' => '9998887777',
    'email' => 'backend@test.com',
    'address' => '123 Backend St'
]);
echo "   Patient Created: ID {$patient->id}\n";

// 2. Find Product
$drug = Product::where('name', 'like', '%Aspirin%')->first();
if (!$drug) {
    echo "   Error: Aspirin not found. Creating dummy.\n";
    $drug = Product::create(['name' => 'Aspirin Dummy', 'cost_price' => 1, 'selling_price' => 2, 'product_type' => 'goods']);
}
echo "   Using Drug: {$drug->name} (ID {$drug->id})\n";

// 3. Create Prescription
echo "2. Creating Prescription...\n";
$doctor = User::first(); // Assume admin/doctor
$prescription = Prescription::create([
    'patient_id' => $patient->id,
    'user_id' => $doctor->id,
    'status' => 'pending',
    'notes' => 'Backend verification test',
    'medications' => [
        [
            'name' => $drug->name,
            'dosage' => '10mg',
            'frequency' => 'Daily',
            'duration' => '7 days',
            'quantity' => 7
        ]
    ]
]);

echo "   Prescription Created: ID {$prescription->id} with medications JSON.\n";

// 4. Verify
$verifyPrescription = Prescription::find($prescription->id);
if ($verifyPrescription && !empty($verifyPrescription->medications) && $verifyPrescription->patient_id === $patient->id) {
    echo "SUCCESS: Clinical Data Flow Verified Correctly.\n";
} else {
    echo "FAILURE: Data mismatch.\n";
}
