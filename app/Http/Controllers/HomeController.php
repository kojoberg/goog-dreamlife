<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\InventoryBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Key Metrics
        $todaySales = Sale::whereDate('created_at', Carbon::today())->sum('total_amount');

        $settings = \App\Models\Setting::first();
        $days = $settings->alert_expiry_days ?? 90;

        $expiredBatches = InventoryBatch::where('expiry_date', '<=', now()->addDays($days))
            ->where('quantity', '>', 0)
            ->count();

        // Calculate low stock: products where current stock <= reorder level
        // optimizing this query would be better for scale, but using the attribute is safer for logic consistency
        $lowStockCount = Product::all()->filter(function ($product) {
            return $product->stock <= $product->reorder_level;
        })->count();

        // 2. Chart Data (Last 7 Days)
        // Generate last 7 days array
        $dates = collect();
        foreach (range(6, 0) as $i) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        $salesData = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('total', 'date');

        // Map totals to dates, filling 0 if missing
        $totals = $dates->map(function ($date) use ($salesData) {
            return $salesData->get($date) ?? 0;
        });

        // Format dates for display (e.g., "Mon 27")
        $displayDates = $dates->map(function ($date) {
            return Carbon::parse($date)->format('D d');
        });

        // 3. Recent Transactions
        $recentSales = Sale::with('user')->latest()->take(5)->get();

        return view('dashboard', compact(
            'todaySales',
            'expiredBatches',
            'lowStockCount',
            'displayDates',
            'totals',
            'recentSales'
        ));
    }
}
