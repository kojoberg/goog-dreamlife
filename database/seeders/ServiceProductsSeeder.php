<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ServiceProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Category exists
        $category = Category::firstOrCreate(
            ['name' => 'Medical Services'],
            ['description' => 'Clinical services offered at the pharmacy']
        );

        $services = [
            [
                'name' => 'BP Check',
                'description' => 'Blood Pressure Monitoring',
                'unit_price' => 10.00,
            ],
            [
                'name' => 'Sugar Check',
                'description' => 'Blood Glucose (RBS/FBS) Test',
                'unit_price' => 20.00,
            ],
            [
                'name' => 'BMI Check',
                'description' => 'Body Mass Index Calculation',
                'unit_price' => 5.00,
            ],
        ];

        foreach ($services as $service) {
            Product::firstOrCreate(
                ['name' => $service['name']],
                [
                    'category_id' => $category->id,
                    'product_type' => 'service',
                    'unit_price' => $service['unit_price'],
                    'cost_price' => 0,
                    'reorder_level' => 0,
                    'description' => $service['description']
                ]
            );
        }
    }
}
