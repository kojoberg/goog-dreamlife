<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\Category;
use App\Models\InventoryBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CashierModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\Setting::create([
            'business_name' => 'Test Pharma',
            'currency_symbol' => 'GHS',
            'enable_tax' => false // Simplify by disabling tax
        ]);
    }

    protected function createProductWithStock($price = 100, $branchId = null)
    {
        $category = Category::create(['name' => 'Test Category']);
        $product = Product::create([
            'name' => 'Test Product',
            'category_id' => $category->id,
            'unit_price' => $price,
            'product_type' => 'goods',
            'reorder_level' => 5,

        ]);

        InventoryBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'B001',
            'quantity' => 100,
            'expiry_date' => now()->addYear(),
            'cost_price' => 50,
            'branch_id' => $branchId,
            'supplier_id' => null // Assuming nullable
        ]);

        return $product;
    }

    public function test_standard_checkout_creates_completed_sale_when_cashier_mode_disabled()
    {
        // 1. Setup: Branch without Cashier logic
        $branch = Branch::create(['name' => 'Main Branch', 'is_main' => true, 'has_cashier' => false]);
        $pharmacist = User::factory()->create(['role' => 'pharmacist', 'branch_id' => $branch->id]);
        $product = $this->createProductWithStock(100, $branch->id);

        // Open Shift
        Shift::create(['user_id' => $pharmacist->id, 'branch_id' => $branch->id, 'start_time' => now(), 'starting_cash' => 100]);

        // 2. Act: Checkout via POS
        $response = $this->actingAs($pharmacist)->postJson(route('pos.store'), [
            'cart' => [['id' => $product->id, 'qty' => 1]],
            'payment_method' => 'cash',
            'amount_tendered' => 100
        ]);

        // 3. Assert

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'type' => 'receipt']);

        $this->assertDatabaseHas('sales', [
            'status' => 'completed',
            'total_amount' => 100,
            'amount_tendered' => 100
        ]);
    }

    public function test_checkout_creates_pending_invoice_when_cashier_mode_enabled()
    {
        // 1. Setup: Branch WITH Cashier logic
        $branch = Branch::create(['name' => 'Split Branch', 'is_main' => false, 'has_cashier' => true]);
        $pharmacist = User::factory()->create(['role' => 'pharmacist', 'branch_id' => $branch->id]);
        $product = $this->createProductWithStock(100, $branch->id);

        // Open Shift
        Shift::create(['user_id' => $pharmacist->id, 'branch_id' => $branch->id, 'start_time' => now(), 'starting_cash' => 100]);

        // 2. Act: Checkout via POS
        $response = $this->actingAs($pharmacist)->postJson(route('pos.store'), [
            'cart' => [['id' => $product->id, 'qty' => 1]],
            'payment_method' => 'cash',
            // amount_tendered omitted or ignored
        ]);

        // 3. Assert
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'type' => 'invoice']);

        $this->assertDatabaseHas('sales', [
            'status' => 'pending_payment',
            'total_amount' => 100,
            'amount_tendered' => 0
        ]);
    }

    public function test_cashier_can_pay_pending_invoice()
    {
        // 1. Setup: Pending Sale
        $branch = Branch::create(['name' => 'Split Branch', 'is_main' => false, 'has_cashier' => true]);
        $pharmacist = User::factory()->create(['role' => 'pharmacist', 'branch_id' => $branch->id]);

        // Manual Sale Creation
        $sale = Sale::create([
            'user_id' => $pharmacist->id,
            'total_amount' => 100,
            'status' => 'pending_payment',
            'amount_tendered' => 0,
            'subtotal' => 100,
            'tax_amount' => 0,
            'payment_method' => 'cash'
        ]);

        // Reload and check
        $sale->refresh();


        $cashier = User::factory()->create(['role' => 'cashier', 'branch_id' => $branch->id]);

        // Open Shift for Cashier
        $shift = Shift::create(['user_id' => $cashier->id, 'branch_id' => $branch->id, 'start_time' => now(), 'starting_cash' => 500]);

        // 2. Act: Cashier processes payment
        $response = $this->actingAs($cashier)->put(route('cashier.update', $sale), [
            'payment_method' => 'cash',
            'amount_tendered' => 150
        ]);

        // 3. Assert
        $response->assertRedirect(route('pos.receipt', $sale->id));

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'completed',
            'amount_tendered' => 150,
            'change_amount' => 50,
            'shift_id' => $shift->id
        ]);
    }
}
