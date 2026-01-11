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

        $salesTotal = 0;
        if ($openShift) {
            // Calculate total sales for this shift
            // Assuming we only care about CASH sales for the drawer check, 
            // but for now, let's sum 'amount_tendered' or 'total' where payment is cash?
            // "Expected Cash" usually implies Cash Sales only.

            $salesTotal = $openShift->sales()
                ->where('payment_method', 'cash')
                ->sum('total_amount');
        }

        return view('shifts.create', compact('openShift', 'salesTotal'));
    }

    public function store(Request $request)
    {
        $isCashier = Auth::user()->role === 'cashier';

        // Pharmacists don't need to count cash if cashier enabled (implied by role check request)
        // If not cashier, we can default starting_cash to 0 or make it optional
        if ($isCashier) {
            $request->validate(['starting_cash' => 'required|numeric|min:0']);
        }

        Shift::create([
            'user_id' => Auth::id(),
            'start_time' => now(),
            'starting_cash' => $isCashier ? $request->starting_cash : 0,
        ]);

        // Redirect based on role: Cashiers go to queue, others to POS
        if ($isCashier) {
            return redirect()->route('cashier.index')->with('success', 'Shift opened successfully.');
        }
        return redirect()->route('pos.index')->with('success', 'Shift opened successfully.');
    }

    public function update(Request $request, Shift $shift)
    {
        $isCashier = Auth::user()->role === 'cashier';

        if ($isCashier) {
            $request->validate([
                'actual_cash' => 'required|numeric|min:0',
                'notes' => 'nullable|string'
            ]);
        }

        // Calculate expected from CASH SALES only (Drawer logic)
        $cashSales = $shift->sales()->where('payment_method', 'cash')->sum('total_amount');
        $expected = $shift->starting_cash + $cashSales;

        $shift->update([
            'end_time' => now(),
            'actual_cash' => $isCashier ? $request->actual_cash : 0,
            'expected_cash' => $expected,
            'notes' => $request->notes,
        ]);

        return redirect()->route('dashboard')->with('success', 'Shift closed.');
    }

    // Admin: List all shifts
    public function index()
    {
        $user = auth()->user();
        $query = Shift::with('user')->latest();

        // Branch scoping: Non-super admins in multi-branch mode see only their branch
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            $query->whereHas('user', fn($q) => $q->where('branch_id', $user->branch_id));
        }

        $shifts = $query->paginate(20);
        return view('admin.shifts.index', compact('shifts'));
    }

    // Admin: Show shift details
    public function show(Shift $shift)
    {
        $user = auth()->user();

        // Branch access check: Non-super admins can only view their branch's shifts
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            if ($shift->user->branch_id !== $user->branch_id) {
                abort(403, 'You can only view shifts from your branch.');
            }
        }

        // Load relationships: User, Sales (and their items for detail?)
        $shift->load(['user', 'sales.user', 'sales.customer']);

        // Calculate detailed breakdown
        $cashSales = $shift->sales()->where('payment_method', 'cash')->sum('total_amount');
        $cardSales = $shift->sales()->where('payment_method', 'card')->sum('total_amount');
        $momoSales = $shift->sales()->where('payment_method', 'momo')->sum('total_amount'); // Mobile Money
        $totalSales = $shift->sales()->sum('total_amount');

        $variance = 0;
        if ($shift->end_time) {
            $variance = $shift->actual_cash - $shift->expected_cash;
        }

        return view('admin.shifts.show', compact('shift', 'cashSales', 'cardSales', 'momoSales', 'totalSales', 'variance'));
    }


    public function print(Shift $shift)
    {
        // Authorization: Admin or Owner of the shift
        if (Auth::user()->role !== 'admin' && Auth::id() !== $shift->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $shift->load(['user', 'sales']);

        return view('reports.shift_print', compact('shift'));
    }

    public function myShifts()
    {
        $shifts = Shift::where('user_id', Auth::id())->latest()->paginate(10);
        return view('shifts.my_index', compact('shifts'));
    }
}
