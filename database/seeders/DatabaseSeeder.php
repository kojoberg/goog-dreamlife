<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;
use App\Models\InventoryBatch;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Users Removed for Fresh Install Workflow
        // The Setup Wizard will handle the creation of the first Admin and Branch.

        /*
        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@dreamlife.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Pharmacist User
        User::create([
            'name' => 'John Pharmacist',
            'email' => 'pharm@dreamlife.com',
            'password' => Hash::make('password'),
            'role' => 'pharmacist',
        ]);

        // Cashier User
        User::create([
            'name' => 'Jane Cashier',
            'email' => 'cashier@dreamlife.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);
        */

        // Sample Data Removed for Clean Install
        // Users will create their own Suppliers, Categories, and Products.

        $this->call([
            \Database\Seeders\GlobalDrugInteractionSeeder::class,
        ]);
    }
}
