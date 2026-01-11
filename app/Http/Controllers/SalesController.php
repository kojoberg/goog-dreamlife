<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Sale::with(['user', 'patient', 'shift.user', 'refund']);

        // Super admins see all sales
        if ($user->isSuperAdmin()) {
            // No branch filter needed
        } elseif ($user->isAdmin() || $user->hasPermission('view_all_sales')) {
            // Regular admins or authorized staff see only their branch's sales
            $query->whereHas('user', function ($q) use ($user) {
                // If user has no branch (unlikely for staff), show all? Or none? Assumes branch_id exists.
                // If branch_id is null, this might show nothing or all.
                // Let's assume branch_id is set.
                if ($user->branch_id) {
                    $q->where('branch_id', $user->branch_id);
                }
            });
        } else {
            // Non-admins see only their own sales
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('shift', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        }

        $sales = $query->latest()->paginate(15);

        return view('sales.index', compact('sales'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'user', 'patient']);
        return view('sales.show', compact('sale'));
    }
}
