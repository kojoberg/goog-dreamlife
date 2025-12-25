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
        // --- ABC ANALYSIS (Based on Revenue contribution in last 90 days) ---
        $startDate = now()->subDays(90);

        $productStats = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('sale', function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->with('product')
            ->get();

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
        // We really need monthly aggregates.
        // Let's get sales for the last 3 months grouped by month and product.

        $forecasts = [];
        // Only run for active products to save performance
        $products = Product::all();

        foreach ($products as $product) {
            // Get stats for Month -1, -2, -3
            $salesLast3Months = SaleItem::where('product_id', $product->id)
                ->whereHas('sale', function ($q) {
                    $q->where('created_at', '>=', now()->subMonths(3));
                })
                ->sum('quantity');

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
