<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Category;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementReceiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_receive_purchase_order()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create(['name' => 'Supplier A', 'email' => 's@a.com', 'phone' => '123']);
        $category = Category::create(['name' => 'Medicine']);
        $product = Product::create([
            'name' => 'Test Drug',
            'category_id' => $category->id,
            'unit_price' => 10,
            'reorder_level' => 5
        ]);

        $order = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'expected_date' => now(),
            'total_amount' => 100
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $order->id,
            'product_id' => $product->id,
            'quantity_ordered' => 10,
            'unit_cost' => 5,
        ]);

        $response = $this->actingAs($user)->post(route('procurement.orders.receive', $order), [
            'received_by' => 'Test Receiver',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inventory_batches', [
            'product_id' => $product->id,
            'quantity' => 10,
            'cost_price' => 5
        ]);

        $order->refresh();
        $this->assertEquals('received', $order->status);
    }
}
