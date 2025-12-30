<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\DrugInteraction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafetyCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_interaction_api_detects_conflict()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        \App\Models\Shift::create(['user_id' => $user->id, 'branch_id' => $user->branch_id, 'start_time' => now(), 'starting_cash' => 0]);

        // Create 2 Drugs
        $aspirin = Product::create([
            'name' => 'Aspirin',
            'product_type' => 'goods', // or 'medicine'
            'unit_price' => 5
        ]);

        $warfarin = Product::create([
            'name' => 'Warfarin',
            'product_type' => 'goods',
            'unit_price' => 50
        ]);

        // Create Interaction Rule
        DrugInteraction::create([
            'drug_a_id' => $aspirin->id,
            'drug_b_id' => $warfarin->id,
            'severity' => 'severe',
            'description' => 'Increased bleeding risk.'
        ]);

        // Mock Cart having Aspirin, adding Warfarin
        $response = $this->actingAs($user)->postJson(route('pos.check-interactions'), [
            'cart_ids' => [$aspirin->id],
            'new_product_id' => $warfarin->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'drug' => 'Aspirin',
            'severity' => 'Severe',
            'description' => 'Increased bleeding risk.'
        ]);
    }

    public function test_check_interaction_api_reverse_order()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        \App\Models\Shift::create(['user_id' => $user->id, 'branch_id' => $user->branch_id, 'start_time' => now(), 'starting_cash' => 0]);

        $aspirin = Product::create(['name' => 'Aspirin', 'unit_price' => 5]);
        $warfarin = Product::create(['name' => 'Warfarin', 'unit_price' => 50]);

        DrugInteraction::create([
            'drug_a_id' => $aspirin->id,
            'drug_b_id' => $warfarin->id,
            'severity' => 'severe',
            'description' => 'Reverse check.'
        ]);

        // Mock Cart having Warfarin, adding Aspirin
        $response = $this->actingAs($user)->postJson(route('pos.check-interactions'), [
            'cart_ids' => [$warfarin->id],
            'new_product_id' => $aspirin->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'drug' => 'Warfarin',
            'severity' => 'Severe'
        ]);
    }
}
