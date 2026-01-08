<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Branch;

class LabScientistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::first();
        $branchId = $branch ? $branch->id : null;

        if (!User::where('email', 'lab@dreamlife.com')->exists()) {
            User::create([
                'name' => 'Lab Scientist',
                'email' => 'lab@dreamlife.com',
                'password' => Hash::make('password'),
                'role' => 'lab_scientist',
                'branch_id' => $branchId,
            ]);
            $this->command->info('Lab Scientist user created: lab@dreamlife.com / password');
        } else {
            $this->command->info('Lab Scientist user already exists.');
        }
    }
}
