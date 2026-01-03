<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Prescription;
use App\Models\InventoryBatch;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $pharmacist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->pharmacist = User::create([
            'name' => 'Pharma User',
            'email' => 'pharma@test.com',
            'password' => bcrypt('password'),
            'role' => 'pharmacist',
        ]);

        // Seed settings
        Setting::create([
            'currency_symbol' => 'GHS',
        ]);
    }

    public function test_admin_can_view_create_prescription_page()
    {
        $response = $this->actingAs($this->admin)->get(route('prescriptions.create'));
        $response->assertStatus(200);
    }

    public function test_can_create_prescription_and_redirect_to_show()
    {
        $patient = Patient::create([
            'name' => 'Test Patient',
            'phone' => '0200000000'
        ]);

        $product = Product::create([
            'name' => 'Aspirin 75mg',
            'unit_price' => 10.00,
        ]);

        $data = [
            'patient_id' => $patient->id,
            'medications' => [
                [
                    'name' => 'Aspirin 75mg',
                    'product_id' => $product->id,
                    'dosage' => '1 tab',
                    'frequency' => 'OD',
                    'quantity' => 10,
                    'days_supply' => 10,
                    'refill_reminder' => false,
                ]
            ],
            'notes' => 'Test prescription',
        ];

        $response = $this->actingAs($this->pharmacist)->post(route('prescriptions.store'), $data);

        $prescription = Prescription::first();
        $this->assertNotNull($prescription, 'Prescription was not created');
        $response->assertRedirect(route('prescriptions.show', $prescription));

        $this->assertEquals('pending', $prescription->status);
    }

    public function test_dispense_flow_deducts_stock_and_creates_sale()
    {
        $patient = Patient::create([
            'name' => 'Test Patient Dispense',
            'phone' => '0500000000'
        ]);

        $product = Product::create([
            'name' => 'Aspirin 75mg',
            'unit_price' => 5.00
        ]);

        // Create a batch
        InventoryBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'BATCH-001',
            'quantity' => 50,
            'expiry_date' => now()->addYear(),
        ]);

        $prescription = Prescription::create([
            'user_id' => $this->pharmacist->id,
            'patient_id' => $patient->id,
            'status' => 'pending',
            'medications' => [
                [
                    'name' => 'Aspirin 75mg',
                    'product_id' => $product->id,
                    'dosage' => '1 tab',
                    'frequency' => 'OD',
                    'quantity' => 10,
                    'days_supply' => 5,
                ]
            ]
        ]);

        // Dispense
        $response = $this->actingAs($this->pharmacist)->post(route('prescriptions.dispense', $prescription));

        // Assert
        $response->assertSessionHas('success');

        $prescription->refresh();
        $this->assertEquals('dispensed', $prescription->status);

        // Check Stock
        $product->refresh();
        // 50 initial. 10 dispensed.
        $this->assertEquals(40, $product->stock);

        // Check Sale
        $this->assertDatabaseHas('sales', [
            'patient_id' => $patient->id,
            'prescription_id' => $prescription->id,
            'total_amount' => 50.00,
        ]);
    }
}
