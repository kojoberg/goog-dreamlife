<?php

use Illuminate\Http\Request;
use App\Http\Controllers\PosController;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\DrugInteraction;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Ensure at least one category exists
$category = Category::withoutEvents(function () {
    return Category::firstOrCreate(['name' => 'Test Category']);
});

// Ensure products exist
$p1 = Product::withoutEvents(function () use ($category) {
    return Product::firstOrCreate(['name' => 'Drug A'], [
        'category_id' => $category->id,
        'unit_price' => 10,
    ]);
});

$p2 = Product::withoutEvents(function () use ($category) {
    return Product::firstOrCreate(['name' => 'Drug B'], [
        'category_id' => $category->id,
        'unit_price' => 20,
    ]);
});

echo "Testing interactions between {$p1->name} ({$p1->id}) and {$p2->name} ({$p2->id})\n";

// Ensure interaction exists
$interaction = DrugInteraction::withoutEvents(function () use ($p1, $p2) {
    $interaction = DrugInteraction::where(function ($q) use ($p1, $p2) {
        $q->where('drug_a_id', $p1->id)->where('drug_b_id', $p2->id);
    })->orWhere(function ($q) use ($p1, $p2) {
        $q->where('drug_a_id', $p2->id)->where('drug_b_id', $p1->id);
    })->first();

    if (!$interaction) {
        $interaction = DrugInteraction::create([
            'drug_a_id' => $p1->id,
            'drug_b_id' => $p2->id,
            'severity' => 'severe',
            'description' => 'Do not mix A and B'
        ]);
    }
    return $interaction;
});

echo "Interaction created/found: {$interaction->description}\n";

$controller = app(PosController::class);
$request = Request::create('/pos/check-interactions', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json'
], json_encode([
        'cart_ids' => [$p1->id],
        'new_product_id' => $p2->id
    ]));

try {
    $response = $controller->checkInteractions($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
