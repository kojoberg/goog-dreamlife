<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shift;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Models\InventoryBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure Settings exist
        Setting::create([
            'business_name' => 'Test Pharmacy',
            'currency_symbol' => 'GHS',
            'alert_expiry_days' => 90,
            'sms_api_key' => 'test_key',
            'sms_sender_id' => 'TEST'
        ]);

        // Create User
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_can_load_pos_page()
    {
        // Must open shift first
        Shift::create([
            'user_id' => $this->user->id,
            'start_time' => now(),
            'starting_cash' => 100,
        ]);

        $response = $this->actingAs($this->user)->get(route('pos.index'));
        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function test_can_receive_stock_with_cost_price()
    {
        $category = Category::create(['name' => 'Test Cat']);
        $product = Product::create([
            'name' => 'Test Drug',
            'category_id' => $category->id,
            'unit_price' => 10.00,
            'reorder_level' => 5
        ]);

        $response = $this->actingAs($this->user)->post(route('inventory.store'), [
            'product_id' => $product->id,
            'quantity' => 100,
            'cost_price' => 5.50, // New Field
            'expiry_date' => now()->addYear()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inventory_batches', [
            'product_id' => $product->id,
            'cost_price' => 5.50,
            'quantity' => 100
        ]);
    }

    public function test_dashboard_alerts_dont_crash()
    {
        // Seed some data that triggers alerts
        $category = Category::create(['name' => 'Alert Cat']);
        $product = Product::create([
            'name' => 'Low Stock Drug',
            'category_id' => $category->id,
            'unit_price' => 10.00,
            'reorder_level' => 20
        ]);

        // Stock 0 -> should trigger low stock alert logic

        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        // Dashboard should load without crashing even with low stock items
    }
}
