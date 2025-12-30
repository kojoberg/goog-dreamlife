<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Prescription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefillTest extends TestCase
{
    use RefreshDatabase;

    public function test_pharmacist_can_refill_dispensed_prescription()
    {
        $pharmacist = User::factory()->create(['role' => 'pharmacist']);
        $patient = \App\Models\Patient::create(['name' => 'John Doe']);

        $oldPrescription = Prescription::create([
            'user_id' => $pharmacist->id,
            'patient_id' => $patient->id,
            'status' => 'dispensed', // Must be dispensed to see button
            'medications' => [
                ['name' => 'Amoxicillin', 'qty' => 20]
            ],
            'notes' => 'Old Note'
        ]);

        $response = $this->actingAs($pharmacist)->post(route('prescriptions.refill', $oldPrescription));

        $response->assertStatus(302);

        // Find New Prescription
        $newPrescription = Prescription::where('id', '!=', $oldPrescription->id)->first();

        $this->assertNotNull($newPrescription);
        $this->assertEquals('pending', $newPrescription->status);
        $this->assertEquals($pharmacist->id, $newPrescription->user_id);
        $this->assertStringContainsString('Old Note', $newPrescription->notes);
        $this->assertStringContainsString('Refill of #' . $oldPrescription->id, $newPrescription->notes);

        $response->assertRedirect(route('prescriptions.show', $newPrescription));
    }
}
