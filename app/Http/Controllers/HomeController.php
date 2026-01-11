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
        $isSuperAdmin = $user->isSuperAdmin();
        $isAdmin = $user->isAdmin();

        // Helper function to add branch scope to queries
        $addBranchScope = function ($query) use ($user, $isSuperAdmin) {
            if ($isSuperAdmin) {
                return $query; // Super admin sees all
            }
            // Regular admin sees only their branch via user
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        };

        // 1. Key Metrics
        $todaySalesQuery = Sale::whereDate('created_at', Carbon::today());
        if (!$isAdmin) {
            $todaySalesQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('shift', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        } elseif (!$isSuperAdmin) {
            // Regular admin - filter by branch
            $todaySalesQuery->whereHas('user', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }
        $todaySales = $todaySalesQuery->sum('total_amount');

        $settings = \App\Models\Setting::first();
        $days = $settings->alert_expiry_days ?? 90;

        $expiredBatches = InventoryBatch::where('expiry_date', '<=', now()->addDays($days))
            ->where('quantity', '>', 0)
            ->count();

        // Calculate low stock: products where current stock <= reorder level (exclude services)
        $lowStockCount = Product::where('product_type', '!=', 'service')->get()->filter(function ($product) {
            return $product->stock <= $product->reorder_level;
        })->count();

        $totalPatients = \App\Models\Patient::count();

        // 2. Chart Data (Dynamic Range)
        $days = request('days', 7); // Default to 7 days
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        $dates = collect();
        // Generate dates from start to today
        for ($i = 0; $i < $days; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        $salesDataQuery = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->where('created_at', '>=', $startDate);

        if (!$isAdmin) {
            $salesDataQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('shift', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        } elseif (!$isSuperAdmin) {
            $salesDataQuery->whereHas('user', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
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
            return Carbon::parse($date)->format('M d'); // Changed to Month Day for longer ranges
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
        } elseif (!$isSuperAdmin) {
            $recentSalesQuery->whereHas('user', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
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

