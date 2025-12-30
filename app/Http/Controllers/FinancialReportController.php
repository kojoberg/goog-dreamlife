<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function index()
    {
        return view('admin.financials.index');
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $sales = Sale::with(['user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $totalSales = $sales->sum('total_amount');

        return view('admin.financials.sales', compact('sales', 'totalSales', 'startDate', 'endDate'));
    }

    public function inventory()
    {
        // Summary of current inventory value
        $products = Product::with('batches')->get();

        $totalValue = $products->sum(function ($product) {
            return $product->stock * $product->unit_price;
        });

        $totalCost = $products->sum(function ($product) {
            return $product->stock * $product->cost_price;
        });

        return view('admin.financials.inventory', compact('products', 'totalValue', 'totalCost'));
    }

    public function profit(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $sales = Sale::with(['items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $revenue = $sales->sum('total_amount');

        // Calculate Cost of Goods Sold (COGS) for these sales
        $cogs = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                // Fallback to current cost if historical cost not tracked on item
                return $item->quantity * ($item->product->cost_price ?? 0);
            });
        });

        $grossProfit = $revenue - $cogs;

        return view('admin.financials.profit', compact('revenue', 'cogs', 'grossProfit', 'startDate', 'endDate'));
    }
}