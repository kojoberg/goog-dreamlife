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
        $lowStockCount = \App\Models\Product::where('stock', '<', 5)->count(); // Simple threshold
        $todaySales = \App\Models\Sale::whereDate('created_at', today())->sum('total_amount');

        return view('admin.dashboard', compact('usersCount', 'branchesCount', 'lowStockCount', 'todaySales'));
    }
}
