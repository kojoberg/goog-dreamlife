<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    /**
     * Check if user can manage branches (super admin only in multi-branch mode).
     */
    protected function requireBranchManagement()
    {
        if (is_multi_branch() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admins can manage branches in multi-branch mode.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // All admins can view branches list
        $branches = Branch::withCount('users')->get();
        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!is_multi_branch()) {
            abort(403, 'Branch creation is disabled in single-location mode.');
        }

        $this->requireBranchManagement();

        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!is_multi_branch()) {
            abort(403, 'Branch creation is disabled in single-location mode.');
        }

        $this->requireBranchManagement();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'has_cashier' => 'boolean',
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        // In multi-branch, only super admin can edit branches
        // In single-branch, any admin can edit the only branch (settings)
        if (is_multi_branch()) {
            $this->requireBranchManagement();
        }

        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        // In multi-branch, only super admin can update branches
        // In single-branch, any admin can update (for settings like has_cashier)
        if (is_multi_branch() && !$branch->is_main) {
            $this->requireBranchManagement();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'has_cashier' => 'boolean',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $this->requireBranchManagement();

        if ($branch->is_main) {
            return back()->with('error', 'Cannot delete the Main Branch.');
        }

        if ($branch->users()->count() > 0) {
            return back()->with('error', 'Cannot delete branch with assigned users.');
        }

        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }
}
