<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shift;
use App\Models\User;
use App\Models\InventoryBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RefundTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $cashier;
    protected $sale;
    protected $product;
    protected $batch;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\Setting::create(['business_name' => 'Test', 'currency_symbol' => '$']);

        $branch = Branch::create(['name' => 'Test Branch', 'is_main' => true]);

        $this->admin = User::factory()->create(['role' => 'admin', 'branch_id' => $branch->id]);
        $this->cashier = User::factory()->create(['role' => 'cashier', 'branch_id' => $branch->id]);

        $this->product = Product::create([
            'name' => 'Test Product',
            'unit_price' => 50,
            'product_type' => 'goods',
        ]);

        $this->batch = InventoryBatch::create([
            'product_id' => $this->product->id,
            'quantity' => 10,
            'batch_number' => 'B1',
            'expiry_date' => now()->addMonth(),
            'branch_id' => $branch->id
        ]);

        // Create a Completed Sale
        $this->sale = Sale::create([
            'user_id' => $this->cashier->id,
            'status' => 'completed',
            'total_amount' => 100,
            'payment_method' => 'cash',
        ]);

        SaleItem::create([
            'sale_id' => $this->sale->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 50,
            'subtotal' => 100,
            'batch_id' => $this->batch->id
        ]);

        // Deduct initial stock (simulate sale effect)
        $this->batch->decrement('quantity', 2); // 10 -> 8
    }

    public function test_cashier_can_request_refund()
    {
        $response = $this->actingAs($this->cashier)
            ->post(route('refunds.store', $this->sale), [
                'reason' => 'Customer changed mind'
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('refunds', [
            'sale_id' => $this->sale->id,
            'status' => 'pending',
            'reason' => 'Customer changed mind'
        ]);
    }

    public function test_admin_can_approve_refund_and_restock_inventory()
    {
        // 1. Request
        $refund = \App\Models\Refund::create([
            'sale_id' => $this->sale->id,
            'user_id' => $this->cashier->id,
            'amount' => 100,
            'status' => 'pending',
            'reason' => 'Test'
        ]);

        // 2. Approve
        $response = $this->actingAs($this->admin)
            ->post(route('admin.refunds.approve', $refund), [
                'admin_note' => 'Approved'
            ]);

        $response->assertSessionHas('success');

        // 3. Verify Refund Status
        $this->assertDatabaseHas('refunds', [
            'id' => $refund->id,
            'status' => 'approved',
            'approved_by' => $this->admin->id
        ]);

        // 4. Verify Sale Status Negated
        $this->assertDatabaseHas('sales', [
            'id' => $this->sale->id,
            'status' => 'refunded'
        ]);

        // 5. Verify Inventory Restocked (8 + 2 = 10)
        $this->batch->refresh();
        $this->assertEquals(10, $this->batch->quantity);
    }

    public function test_admin_can_reject_refund()
    {
        $refund = \App\Models\Refund::create([
            'sale_id' => $this->sale->id,
            'user_id' => $this->cashier->id,
            'amount' => 100,
            'status' => 'pending',
            'reason' => 'Test'
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.refunds.reject', $refund), [
                'admin_note' => 'Denied'
            ]);

        $this->assertDatabaseHas('refunds', [
            'id' => $refund->id,
            'status' => 'rejected'
        ]);

        // Sale should still be completed
        $this->assertDatabaseHas('sales', [
            'id' => $this->sale->id,
            'status' => 'completed'
        ]);
    }
}
