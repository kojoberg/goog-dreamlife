<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shift;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::create([
            'business_name' => 'UAT Pharma',
            'currency_symbol' => 'GHS'
        ]);
    }

    public function test_user_can_open_shift()
    {
        $user = User::factory()->create(['role' => 'cashier']);

        $response = $this->actingAs($user)->get(route('shifts.create'));
        $response->assertStatus(200);
        $response->assertSee('Start Your Shift');

        $response = $this->actingAs($user)->post(route('shifts.store'), [
            'starting_cash' => 500
        ]);

        $response->assertRedirect(route('pos.index'));
        $this->assertDatabaseHas('shifts', [
            'user_id' => $user->id,
            'starting_cash' => 500,
            'end_time' => null
        ]);
    }

    public function test_pos_access_requires_shift()
    {
        $user = User::factory()->create(['role' => 'cashier']);

        // Try accessing POS without shift
        $response = $this->actingAs($user)->get(route('pos.index'));
        $response->assertRedirect(route('shifts.create'));

        // Open shift
        Shift::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'starting_cash' => 200,
            'start_time' => now()
        ]);

        // Try again
        $response = $this->actingAs($user)->get(route('pos.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_close_shift()
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $shift = Shift::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'starting_cash' => 200,
            'start_time' => now()
        ]);

        // View close page
        $response = $this->actingAs($user)->get(route('shifts.create'));
        $response->assertStatus(200);
        $response->assertSee('Close Shift');
        $response->assertSee('Actual Cash Count');

        // Close it
        $response = $this->actingAs($user)->put(route('shifts.update', $shift), [
            'actual_cash' => 250, // Made some money maybe, or just float
            'notes' => 'Closing time'
        ]);

        $response->assertRedirect(route('dashboard'));

        $shift->refresh();
        $this->assertNotNull($shift->end_time);
        $this->assertEquals(250, $shift->actual_cash);
        $this->assertEquals(200, $shift->expected_cash); // No sales made in test
    }
}
