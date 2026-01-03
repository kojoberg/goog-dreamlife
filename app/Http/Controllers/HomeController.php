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
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // 1. Key Metrics
        $todaySalesQuery = Sale::whereDate('created_at', Carbon::today());
        if (!$isAdmin) {
            $todaySalesQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('shift', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        }
        $todaySales = $todaySalesQuery->sum('total_amount');

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

        $totalPatients = \App\Models\Patient::count();

        // 2. Chart Data (Last 7 Days)
        // Generate last 7 days array
        $dates = collect();
        foreach (range(6, 0) as $i) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        $salesDataQuery = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay());

        if (!$isAdmin) {
            $salesDataQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('shift', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        }

        $salesData = $salesDataQuery->groupBy('date')
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
        $recentSalesQuery = Sale::with('user')->latest();
        if (!$isAdmin) {
            $recentSalesQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('shift', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        }
        $recentSales = $recentSalesQuery->take(5)->get();

        return view('dashboard', compact(
            'todaySales',
            'expiredBatches',
            'lowStockCount',
            'displayDates',
            'totals',
            'totals',
            'recentSales',
            'totalPatients'
        ));
    }
}
