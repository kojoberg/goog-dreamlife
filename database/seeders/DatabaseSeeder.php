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

        // Sample Data
        $supplier = Supplier::create([
            'name' => 'Global Pharma Distributors',
            'contact_person' => 'Mr. Kwame',
            'phone' => '0244123456',
            'email' => 'sales@globalpharma.com',
            'address' => 'Spintex Road, Accra',
        ]);

        $category = Category::create(['name' => 'Painkillers']);

        $product = Product::create([
            'name' => 'Paracetamol 500mg',
            'category_id' => $category->id,
            'description' => 'Standard pain reliance.',
            'unit_price' => 5.00,
            'reorder_level' => 20,
        ]);

        InventoryBatch::create([
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'batch_number' => 'BATCH-001',
            'quantity' => 100,
            'expiry_date' => now()->addYear(),
        ]);
    }
}
