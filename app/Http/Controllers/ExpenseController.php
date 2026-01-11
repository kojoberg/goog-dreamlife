<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Expense::query();

        // Branch scoping for regular admins in multi-branch mode
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            $query->whereHas('user', fn($q) => $q->where('branch_id', $user->branch_id));
        }

        $expenses = $query->latest('date')->paginate(15);

        // Reporting - also scoped
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            $totalExpenses = Expense::whereHas('user', fn($q) => $q->where('branch_id', $user->branch_id))->sum('amount');
            $monthlyExpenses = Expense::whereHas('user', fn($q) => $q->where('branch_id', $user->branch_id))
                ->whereMonth('date', now()->month)->sum('amount');
        } else {
            $totalExpenses = Expense::sum('amount');
            $monthlyExpenses = Expense::whereMonth('date', now()->month)->sum('amount');
        }

        return view('expenses.index', compact('expenses', 'totalExpenses', 'monthlyExpenses'));
    }

    public function create()
    {
        // Super admins can select target branch
        $branches = null;
        if (auth()->user()->isSuperAdmin() && is_multi_branch()) {
            $branches = \App\Models\Branch::all();
        }

        return view('expenses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['user_id'] = Auth::id();

        // Super admin can specify branch, others use their own
        if (!auth()->user()->isSuperAdmin() || !isset($validated['branch_id'])) {
            $validated['branch_id'] = auth()->user()->branch_id;
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
