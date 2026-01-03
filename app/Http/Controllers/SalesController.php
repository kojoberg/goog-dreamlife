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
        $query = Sale::with(['user', 'patient', 'shift.user']);

        if (!auth()->user()->isAdmin()) {
            $user = auth()->user();
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
