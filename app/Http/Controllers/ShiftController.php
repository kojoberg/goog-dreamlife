<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    // Show open/close shift page
    public function create()
    {
        $openShift = Shift::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        return view('shifts.create', compact('openShift'));
    }

    public function store(Request $request)
    {
        // Open a shift
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        Shift::create([
            'user_id' => Auth::id(),
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift opened successfully.');
    }

    public function update(Request $request, Shift $shift)
    {
        // Close a shift
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
        ]);

        // Calculate expected cash 
        // Expected = Starting + (Cash Sales - Cash Refunds) ... 
        // For simplicity: Starting + Sum of Sales (Total Amount) for this shift
        // Ideally we differentiate payment methods (Cash, Card, Momo). 
        // Assuming all sales are cash for now or we just sum total sales.

        $totalSales = $shift->sales()->sum('total_amount'); // Add payment method filter later if needed
        $expected = $shift->starting_cash + $totalSales;

        $shift->update([
            'end_time' => now(),
            'actual_cash' => $request->actual_cash,
            'expected_cash' => $expected,
            'notes' => $request->notes,
        ]);

        return redirect()->route('dashboard')->with('success', 'Shift closed.');
    }
}
