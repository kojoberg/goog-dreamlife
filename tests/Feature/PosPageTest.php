<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shift;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_page_loads_with_default_settings()
    {
        // 1. Arrange: Create User & Open Shift
        $user = User::factory()->create();

        Shift::create([
            'user_id' => $user->id,
            'start_time' => now(),
            'starting_cash' => 100,
        ]);

        // Ensure Settings table is EMPTY to test the fix
        Setting::truncate();

        // 2. Act: Access POS Page
        $response = $this->actingAs($user)->get(route('pos.index'));

        // 3. Assert: 
        // - status 200 (OK) - NOT 500
        // - View has 'settings' variable
        $response->assertStatus(200);
        $response->assertViewHas('settings');

        // Settings should have been created
        $this->assertDatabaseCount('settings', 1);
    }
}
