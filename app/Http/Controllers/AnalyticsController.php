<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $branchId = null;

        // Branch scoping for non-super admins in multi-branch mode
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            $branchId = $user->branch_id;
        }

        // --- ABC ANALYSIS (Based on Revenue contribution in last 90 days) ---
        $startDate = now()->subDays(90);

        $productStatsQuery = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('sale', function ($q) use ($startDate, $branchId) {
                $q->where('created_at', '>=', $startDate);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            })
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->with('product');

        $productStats = $productStatsQuery->get();

        $totalRevenue = $productStats->sum('total_revenue');
        $cumulative = 0;
        $abcData = [];

        foreach ($productStats as $stat) {
            $cumulative += $stat->total_revenue;
            $percentage = $totalRevenue > 0 ? ($cumulative / $totalRevenue) * 100 : 0;

            if ($percentage <= 80) {
                $category = 'A'; // High Value
            } elseif ($percentage <= 95) {
                $category = 'B'; // Moderate Value
            } else {
                $category = 'C'; // Low Value
            }

            $abcData[] = [
                'product' => $stat->product,
                'revenue' => $stat->total_revenue,
                'quantity' => $stat->total_qty,
                'category' => $category,
                'cumulative_percentage' => $percentage
            ];
        }

        // --- SALES FORECASTING (Simple Moving Average - 3 Months) ---
        $forecasts = [];

        // Get products (with branch filter if applicable)
        $productsQuery = Product::query();
        if ($branchId) {
            $productsQuery->where('branch_id', $branchId);
        }
        $products = $productsQuery->get();

        foreach ($products as $product) {
            // Get stats for Month -1, -2, -3
            $salesQuery = SaleItem::where('product_id', $product->id)
                ->whereHas('sale', function ($q) use ($branchId) {
                    $q->where('created_at', '>=', now()->subMonths(3));
                    if ($branchId) {
                        $q->where('branch_id', $branchId);
                    }
                });

            $salesLast3Months = $salesQuery->sum('quantity');

            // Simple Average
            $averageMonthly = $salesLast3Months / 3;

            // Basic Status
            $status = 'Stable';
            if ($product->stock < $averageMonthly) {
                $status = 'Risk of Stockout';
            } elseif ($product->stock > ($averageMonthly * 3)) { // > 3 months supply
                $status = 'Overstocked';
            }

            if ($averageMonthly > 0) {
                $forecasts[] = [
                    'product' => $product,
                    'avg_monthly_sales' => round($averageMonthly, 1),
                    'predicted_next_month' => round($averageMonthly),
                    'current_stock' => $product->stock,
                    'status' => $status
                ];
            }
        }

        // Sort forecasts by predicted volume
        usort($forecasts, fn($a, $b) => $b['predicted_next_month'] <=> $a['predicted_next_month']);

        return view('analytics.index', compact('abcData', 'forecasts'));
    }
}

