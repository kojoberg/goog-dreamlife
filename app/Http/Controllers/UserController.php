<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Check if current user can manage the target user.
     * Super admins can manage anyone, regular admins only their branch.
     */
    protected function canManageUser(User $targetUser): bool
    {
        $currentUser = auth()->user();

        // Super admins can manage anyone
        if ($currentUser->isSuperAdmin()) {
            return true;
        }

        // In single-branch mode, any admin can manage all users
        if (is_single_branch()) {
            return true;
        }

        // In multi-branch mode, admins can only manage users in their own branch
        return $targetUser->branch_id === $currentUser->branch_id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = User::with('branch');

        // Super admins see all users
        // Regular admins in multi-branch mode only see their branch's users
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            $query->where('branch_id', $user->branch_id);
        }

        $users = $query->latest()->get();
        return view('user_management.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // Super admins can assign to any branch, regular admins only their own
        if ($user->isSuperAdmin()) {
            $branches = Branch::all();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        return view('user_management.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,pharmacist,cashier,doctor,lab_scientist'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        // In multi-branch mode, regular admins can only create users in their branch
        $branchId = $request->branch_id;
        if (!$currentUser->isSuperAdmin() && is_multi_branch()) {
            $branchId = $currentUser->branch_id;
        }

        // Only super admins can create other admins
        $role = $request->role;
        if ($role === 'admin' && !$currentUser->isSuperAdmin()) {
            $role = 'pharmacist'; // Downgrade if not super admin
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'branch_id' => $branchId,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Check permission
        if (!$this->canManageUser($user)) {
            abort(403, 'You can only manage users in your own branch.');
        }

        $currentUser = auth()->user();

        // Super admins can change branch, regular admins cannot
        if ($currentUser->isSuperAdmin()) {
            $branches = Branch::all();
        } else {
            $branches = Branch::where('id', $currentUser->branch_id)->get();
        }

        $permissions = Permission::all();
        return view('user_management.edit', compact('user', 'branches', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Check permission
        if (!$this->canManageUser($user)) {
            abort(403, 'You can only manage users in your own branch.');
        }

        $currentUser = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,pharmacist,cashier,doctor,lab_scientist'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        // Only super admins can change branch assignments
        $branchId = $request->branch_id;
        if (!$currentUser->isSuperAdmin() && is_multi_branch()) {
            $branchId = $user->branch_id; // Keep original branch
        }

        // Only super admins can set admin role for others
        $role = $request->role;
        if ($role === 'admin' && !$currentUser->isSuperAdmin() && $user->id !== $currentUser->id) {
            $role = $user->role; // Keep original role
        }

        // Prevent removing own admin status
        if ($user->id === $currentUser->id && $currentUser->role === 'admin' && $role !== 'admin') {
            $role = 'admin'; // Keep admin role for self
        }

        $inputs = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'branch_id' => $branchId,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $inputs['password'] = Hash::make($request->password);
        }

        $user->update($inputs);

        // Sync Permissions (only super admins can modify permissions in multi-branch)
        if ($request->has('permissions_submitted')) {
            if ($currentUser->isSuperAdmin() || is_single_branch()) {
                $user->permissions()->sync($request->permissions ?? []);
            }
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check permission
        if (!$this->canManageUser($user)) {
            abort(403, 'You can only manage users in your own branch.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        // Prevent deleting super admins unless you're a super admin
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admins can delete other Super Admins.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
