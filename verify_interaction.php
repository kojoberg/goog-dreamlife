<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\DrugInteraction;
use App\Services\DrugInteractionService;
use Illuminate\Support\Facades\Log;

echo "1. Cleaning up old test data...\n";
DrugInteraction::truncate();
Product::whereIn('name', ['Metronidazole', 'Disulfiram'])->delete();

echo "2. creating products...\n";
$metric = Product::create(['name' => 'Metronidazole', 'description' => 'Antibiotic', 'unit_price' => 10.00]);
$disulf = Product::create(['name' => 'Disulfiram', 'description' => 'Alcohol antagonist', 'unit_price' => 15.00]);

echo "3. Syncing via OpenFDA...\n";
$service = new DrugInteractionService();
// Process both to ensure we catch it either way
$service->processProduct($metric);
$service->processProduct($disulf);

echo "4. Checking interactions table...\n";
$interaction = DrugInteraction::where(function ($q) use ($metric, $disulf) {
    $q->where('drug_a_id', $metric->id)->where('drug_b_id', $disulf->id);
})->orWhere(function ($q) use ($metric, $disulf) {
    $q->where('drug_a_id', $disulf->id)->where('drug_b_id', $metric->id);
})->first();

if ($interaction) {
    echo "SUCCESS: Interaction found!\n";
    echo "Source: " . ($interaction->source ?? 'NULL') . "\n";
    echo "Description: {$interaction->description}\n";
} else {
    echo "FAILURE: No interaction found between Metronidazole and Disulfiram via OpenFDA.\n";

    // Debug Trace
    echo "Debugging OpenFDA Search for 'Metronidazole'...\n";
    try {
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('fetchInteractionsFromOpenFDA');
        $method->setAccessible(true);
        $labels = $method->invoke($service, 'Metronidazole');

        echo "Found " . count($labels) . " labels mentioning Metronidazole.\n";
        foreach ($labels as $l) {
            $brand = $l['openfda']['brand_name'][0] ?? 'N/A';
            $generic = $l['openfda']['generic_name'][0] ?? 'N/A';
            echo " - Label: Brand={$brand}, Generic={$generic}\n";
        }

        echo "Debugging OpenFDA Search for 'Disulfiram'...\n";
        $labels = $method->invoke($service, 'Disulfiram');
        echo "Found " . count($labels) . " labels mentioning Disulfiram.\n";
        foreach ($labels as $l) {
            $brand = $l['openfda']['brand_name'][0] ?? 'N/A';
            $generic = $l['openfda']['generic_name'][0] ?? 'N/A';
            echo " - Label: Brand={$brand}, Generic={$generic}\n";
        }

    } catch (\Exception $e) {
        echo "Debug Error: " . $e->getMessage() . "\n";
    }
}
