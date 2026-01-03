<?php

use App\Models\Product;
use App\Models\Category;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ensure a category exists
$category = Category::firstOrCreate(['name' => 'Test Category']);

// Create or Update Drug A -> Aspirin
Product::updateOrCreate(
    ['name' => 'Aspirin'],
    [
        'category_id' => $category->id,
        'product_type' => 'goods', // matches filtering in SyncDrugInteractions
        'unit_price' => 10.00,
        'cost_price' => 5.00,
        'reorder_level' => 10,
        'rxcui' => null // Reset so sync triggers
    ]
);

// Create or Update Drug B -> Warfarin
Product::updateOrCreate(
    ['name' => 'Warfarin'],
    [
        'category_id' => $category->id,
        'product_type' => 'goods',
        'unit_price' => 15.00,
        'cost_price' => 7.00,
        'reorder_level' => 10,
        'rxcui' => null // Reset so sync triggers
    ]
);

echo "Test products created/updated.\n";
