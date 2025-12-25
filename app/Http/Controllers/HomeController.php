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

        $expiredBatches = InventoryBatch::where('expiry_date', '<', now())
            ->where('quantity', '>', 0)
            ->count();

        // Calculate low stock: products where current stock <= reorder level
        // optimizing this query would be better for scale, but using the attribute is safer for logic consistency
        $lowStockCount = Product::all()->filter(function ($product) {
            return $product->stock <= $product->reorder_level;
        })->count();

        // 2. Chart Data (Last 7 Days)
        $salesLast7Days = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Format for Chart.js
        $dates = $salesLast7Days->pluck('date');
        $totals = $salesLast7Days->pluck('total');

        // 3. Recent Transactions
        $recentSales = Sale::with('user')->latest()->take(5)->get();

        return view('dashboard', compact(
            'todaySales',
            'expiredBatches',
            'lowStockCount',
            'dates',
            'totals',
            'recentSales'
        ));
    }
}
