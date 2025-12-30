<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Patient;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UatBatch3Test extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed basic settings
        Setting::create([
            'business_name' => 'UAT Pharma',
            'phone' => '055-555-5555',
            'email' => 'contact@uatpharma.com',
            'address' => '123 UAT Street, Accra',
            'currency_symbol' => 'GHS',
            'tin_number' => 'P000000000'
        ]);
    }

    public function test_uat_4_po_branding_verification()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create(['name' => 'Test Supplier', 'email' => 'supplier@test.com', 'phone' => '1234567890']);

        $order = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'total_amount' => 500,
            'expected_date' => now()->addDays(7)->format('Y-m-d')
        ]);

        $response = $this->actingAs($user)->get(route('procurement.orders.print', $order));

        $response->assertStatus(200);
        // Verify Branding Elements from Settings
        $response->assertSee('UAT Pharma');
        $response->assertSee('123 UAT Street, Accra');
        $response->assertSee('055-555-5555');
        $response->assertSee('contact@uatpharma.com');
        $response->assertSee('TIN: P000000000');

        // Verify Supplier Details
        $response->assertSee('Test Supplier');

        // View uses ID padding
        $expectedPoNumber = 'PO #' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $response->assertSee($expectedPoNumber);
    }

    public function test_uat_8_pos_receipt_loyalty_and_tendered_details()
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $patient = Patient::create([
            'name' => 'John Doe',
            'loyalty_points' => 150
        ]);

        $sale = Sale::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'total_amount' => 100,
            'subtotal' => 100,
            'tax_amount' => 0,
            'payment_method' => 'cash',
            'amount_tendered' => 120,
            'change_amount' => 20,
            'points_earned' => 10,
            'points_redeemed' => 0
        ]);

        $response = $this->actingAs($user)->get(route('pos.receipt', $sale));

        $response->assertStatus(200);

        // Verify Tendered and Change
        // View shows "Tendered (Method)" e.g. "Tendered (Cash)"
        $response->assertSee('Tendered (Cash)');
        $response->assertSee('120.00');
        $response->assertSee('Change');
        $response->assertSee('20.00');

        // Verify Loyalty
        if (!str_contains($response->getContent(), 'Loyalty Points Earned: 10')) {
            dump($response->getContent());
        }
        $response->assertSee('Loyalty Points Earned: 10');
        $response->assertSee('Current Balance: 150');
    }

    public function test_uat_9_loyalty_history_page()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $patient = Patient::create([
            'name' => 'Jane Loyalty',
            'loyalty_points' => 50
        ]);

        $sale1 = Sale::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'total_amount' => 200,
            'points_earned' => 20,
            'created_at' => now()->subDays(2)
        ]);

        $sale2 = Sale::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'total_amount' => 50,
            'points_redeemed' => 10,
            'discount_amount' => 5,
            'created_at' => now()->subDay()
        ]);

        // Link Verification (on Profile Page)
        $profileResponse = $this->actingAs($user)->get(route('patients.show', $patient));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Loyalty Points History');
        $profileResponse->assertSee(route('patients.loyalty', $patient), false);

        // Page Verification (Loyalty History Page)
        $historyResponse = $this->actingAs($user)->get(route('patients.loyalty', $patient));
        $historyResponse->assertStatus(200);

        // Check Title and Balance
        $historyResponse->assertSee('Loyalty History: ' . $patient->name);
        $historyResponse->assertSee('Current Balance');
        $historyResponse->assertSee('50 pts');

        // Check Transactions (Points)
        $historyResponse->assertSee('+20'); // Sale 1 earned
        $historyResponse->assertSee('-10'); // Sale 2 redeemed

        // Check Order IDs
        $historyResponse->assertSee('#' . str_pad($sale1->id, 6, '0', STR_PAD_LEFT));
        $historyResponse->assertSee('#' . str_pad($sale2->id, 6, '0', STR_PAD_LEFT));
    }
}
