<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\DrugInteraction;
use App\Services\DrugInteractionService;

// Define Test Sets
$testSets = [
    'Set 1' => ['Warfarin', 'Aspirin'],
    'Set 2' => ['Sildenafil', 'Nitroglycerin'] // Nitroglycerin often listed as Nitrostat or similar, but generic name should match
];

echo "== Starting Additional Interaction Tests ==\n\n";

$service = new DrugInteractionService();

foreach ($testSets as $setName => $drugs) {
    echo "Testing {$setName}: " . implode(' + ', $drugs) . "\n";

    // 1. Cleanup & Setup
    Product::whereIn('name', $drugs)->delete();

    $products = [];
    foreach ($drugs as $name) {
        $products[] = Product::create([
            'name' => $name,
            'description' => 'Test Drug',
            'unit_price' => 10
        ]);
    }

    // 2. Sync
    echo " - Syncing with OpenFDA...\n";
    foreach ($products as $p) {
        $service->processProduct($p);
    }

    // 3. Verify
    $p1 = $products[0];
    $p2 = $products[1];

    $interaction = DrugInteraction::where(function ($q) use ($p1, $p2) {
        $q->where('drug_a_id', $p1->id)->where('drug_b_id', $p2->id);
    })->orWhere(function ($q) use ($p1, $p2) {
        $q->where('drug_a_id', $p2->id)->where('drug_b_id', $p1->id);
    })->first();

    if ($interaction) {
        echo " [PASS] Interaction Found!\n";
        echo "   Source: {$interaction->source}\n";
        echo "   Description: " . substr($interaction->description, 0, 200) . "...\n\n";
    } else {
        echo " [FAIL] No interaction found for {$setName}.\n\n";
    }
}
