<?php

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Sales Data...\n";

try {
    $salesLast7Days = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
        ->where('created_at', '>=', Carbon::now()->subDays(6))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    echo "Query Result Count: " . $salesLast7Days->count() . "\n";
    foreach ($salesLast7Days as $sale) {
        echo "Date: " . $sale->date . " | Total: " . $sale->total . "\n";
    }

    if ($salesLast7Days->isEmpty()) {
        echo "No sales found in last 7 days.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
