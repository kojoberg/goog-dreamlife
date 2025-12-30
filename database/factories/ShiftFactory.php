<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'start_time' => now(),
            'end_time' => now()->addHours(8),
            'starting_cash' => 100,
            'actual_cash' => 1500,
            'expected_cash' => 1500,
            'notes' => 'Auto-generated shift',
        ];
    }
}
