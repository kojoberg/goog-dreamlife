<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Procurement
            ['name' => 'Create Purchase Orders', 'slug' => 'create_purchase_order'],
            ['name' => 'Receive Stock', 'slug' => 'receive_stock'],

            // Inventory
            ['name' => 'Manage Products (Add/Edit)', 'slug' => 'manage_products'],
            ['name' => 'Adjust Stock', 'slug' => 'adjust_stock'],
            ['name' => 'View Cost Price', 'slug' => 'view_cost_price'],

            // Sales & POS
            ['name' => 'Access POS System', 'slug' => 'access_pos'],
            ['name' => 'Process Refund (Cashier)', 'slug' => 'process_refund'],
            ['name' => 'Void Sales', 'slug' => 'void_sale'],
            ['name' => 'View All Sales History', 'slug' => 'view_all_sales'],

            // Clinical
            ['name' => 'Dispense Medication', 'slug' => 'dispense_medication'],
            ['name' => 'Prescribe Medication', 'slug' => 'prescribe_medication'],
            ['name' => 'Register Patients', 'slug' => 'register_patient'],

            // System & Financials
            ['name' => 'View Financial Reports', 'slug' => 'view_financial_reports'],
            ['name' => 'Manage Users', 'slug' => 'manage_users'],
            ['name' => 'Configure System', 'slug' => 'configure_system'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }
    }
}
