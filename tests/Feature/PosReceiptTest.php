<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_receipt()
    {
        $user = User::factory()->create();

        Setting::create([
            'business_name' => 'Test Pharmacy',
            'currency_symbol' => 'GHS'
        ]);

        $sale = Sale::create([
            'user_id' => $user->id,
            'total_amount' => 100,
            'subtotal' => 80,
            'tax_amount' => 20,
            'payment_method' => 'cash',
        ]);

        $response = $this->actingAs($user)->get(route('pos.receipt', $sale));

        $response->assertStatus(200);
        $response->assertSee('Test Pharmacy');
        $response->assertSee('Receipt #');
    }
}
