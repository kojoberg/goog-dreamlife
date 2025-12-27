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
        $sales = Sale::with(['user', 'patient'])
            ->latest()
            ->paginate(15);

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
