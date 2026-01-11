<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Summary Stats
        $usersCount = \App\Models\User::count();
        $branchesCount = \App\Models\Branch::count();
        // Calculate low stock using the getStockAttribute accessor logic (exclude services)
        // We fetch all products and filter because 'stock' is not a database column but a computed sum of batches
        $lowStockCount = \App\Models\Product::where('product_type', '!=', 'service')->get()->filter(function ($product) {
            return $product->stock < 5;
        })->count();
        $todaySales = \App\Models\Sale::whereDate('created_at', today())->sum('total_amount');

        return view('admin.dashboard', compact('usersCount', 'branchesCount', 'lowStockCount', 'todaySales'));
    }
}
