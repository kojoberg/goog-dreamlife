<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        // Summary Stats
        if ($isSuperAdmin || is_single_branch()) {
            // Super admin sees global stats
            $usersCount = \App\Models\User::count();
            $branchesCount = \App\Models\Branch::count();
            $todaySales = \App\Models\Sale::whereDate('created_at', today())->sum('total_amount');
        } else {
            // Regular admin sees only their branch stats
            $usersCount = \App\Models\User::where('branch_id', $user->branch_id)->count();
            $branchesCount = 1; // They only manage their branch
            $todaySales = \App\Models\Sale::whereDate('created_at', today())
                ->whereHas('user', fn($q) => $q->where('branch_id', $user->branch_id))
                ->sum('total_amount');
        }

        // Low stock count (exclude services)
        $lowStockCount = \App\Models\Product::where('product_type', '!=', 'service')->get()->filter(function ($product) {
            return $product->stock < 5;
        })->count();

        return view('admin.dashboard', compact('usersCount', 'branchesCount', 'lowStockCount', 'todaySales'));
    }
}
