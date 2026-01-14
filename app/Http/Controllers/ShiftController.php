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
            // Calculate total cash sales for this shift
            // For cashiers: use cashierSales (tracks by cashier_shift_id)
            // For pharmacists/others: use sales (tracks by shift_id)
            $isCashier = Auth::user()->role === 'cashier';

            if ($isCashier) {
                // Cashier: sum sales where they collected payment
                $salesTotal = $openShift->cashierSales()
                    ->where('payment_method', 'cash')
                    ->sum('total_amount');
            } else {
                // Pharmacist: sum sales they created (when no cashier workflow)
                $salesTotal = $openShift->sales()
                    ->where('status', 'completed')
                    ->where('payment_method', 'cash')
                    ->sum('total_amount');
            }
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
        // User handles cash if they are a cashier OR if the branch has no dedicated cashier
        $userHandlesCash = Auth::user()->role === 'cashier'
            || (Auth::user()->branch && !Auth::user()->branch->has_cashier);

        if ($userHandlesCash) {
            $request->validate([
                'actual_cash' => 'required|numeric|min:0',
                'notes' => 'nullable|string'
            ]);
        }

        // Calculate expected from CASH SALES only (Drawer logic)
        // Cashiers: use cashierSales (tracks by cashier_shift_id)
        // Pharmacists: use sales (tracks by shift_id)
        $isCashierRole = $shift->user->role === 'cashier';
        if ($isCashierRole) {
            $cashSales = $shift->cashierSales()->where('payment_method', 'cash')->sum('total_amount');
        } else {
            $cashSales = $shift->sales()->where('status', 'completed')->where('payment_method', 'cash')->sum('total_amount');
        }
        $expected = $shift->starting_cash + $cashSales;

        $shift->update([
            'end_time' => now(),
            'actual_cash' => $userHandlesCash ? $request->actual_cash : 0,
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

        // Load relationships: User
        $shift->load(['user']);

        // Determine which sales to show based on shift user role
        $isCashierShift = $shift->user->role === 'cashier';

        // Load the appropriate sales relationship
        if ($isCashierShift) {
            // Cashier shift: show sales where they collected payment
            $shift->load(['cashierSales.user', 'cashierSales.patient']);
            $salesQuery = $shift->cashierSales();
            $shiftSales = $shift->cashierSales;
        } else {
            // Pharmacist shift: show sales they created
            $shift->load(['sales.user', 'sales.patient']);
            $salesQuery = $shift->sales()->where('status', 'completed');
            $shiftSales = $shift->sales->where('status', 'completed');
        }

        // Calculate detailed breakdown
        $cashSales = (clone $salesQuery)->where('payment_method', 'cash')->sum('total_amount');
        $cardSales = (clone $salesQuery)->where('payment_method', 'card')->sum('total_amount');
        $momoSales = (clone $salesQuery)->where('payment_method', 'mobile_money')->sum('total_amount');
        $totalSales = (clone $salesQuery)->sum('total_amount');

        $variance = 0;
        if ($shift->end_time) {
            $variance = $shift->actual_cash - $shift->expected_cash;
        }

        return view('admin.shifts.show', compact('shift', 'cashSales', 'cardSales', 'momoSales', 'totalSales', 'variance', 'shiftSales', 'isCashierShift'));
    }


    public function print(Shift $shift)
    {
        // Authorization: Admin or Owner of the shift
        if (Auth::user()->role !== 'admin' && Auth::id() !== $shift->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $shift->load(['user']);

        // Determine which sales to show based on shift user role
        $isCashierShift = $shift->user->role === 'cashier';

        if ($isCashierShift) {
            $shift->load(['cashierSales']);
            $shiftSales = $shift->cashierSales;
        } else {
            $shift->load(['sales']);
            $shiftSales = $shift->sales->where('status', 'completed');
        }

        return view('reports.shift_print', compact('shift', 'shiftSales', 'isCashierShift'));
    }

    public function myShifts()
    {
        $shifts = Shift::where('user_id', Auth::id())->latest()->paginate(10);
        return view('shifts.my_index', compact('shifts'));
    }
}
