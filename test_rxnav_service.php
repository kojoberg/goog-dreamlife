<?php

use App\Services\RxNavService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new RxNavService();

echo "Searching Aspirin...\n";
$rxcuiA = $service->searchRxCui('Aspirin');
echo "Aspirin RxCUI: " . var_export($rxcuiA, true) . "\n";

echo "Searching Warfarin...\n";
$rxcuiB = $service->searchRxCui('Warfarin');
echo "Warfarin RxCUI: " . var_export($rxcuiB, true) . "\n";

if ($rxcuiA && $rxcuiB) {
    echo "Fetching Interactions...\n";
    $interactions = $service->getInteractions([$rxcuiA, $rxcuiB]);
    echo "Interactions Count: " . count($interactions) . "\n";
    print_r($interactions);
} else {
    echo "Skipping interactions fetch due to missing RxCUI.\n";
}
