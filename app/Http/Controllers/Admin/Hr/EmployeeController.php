<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display listing of employees
     */
    public function index()
    {
        $branchId = auth()->user()->getBranchIdForScope();

        // Get all users who have an employee profile OR are potential employees (exclude patients)
        // AND match the current admin's branch scope
        $employees = User::when($branchId, function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
            ->where(function ($query) {
                $query->whereHas('employeeProfile')
                    ->orWhere(function ($q) {
                        $q->where('role', '!=', 'patient')
                            ->where('role', '!=', 'super_admin');
                    });
            })
            ->with(['employeeProfile', 'branch'])
            ->latest()
            ->paginate(20);

        return view('admin.hr.employees.index', compact('employees'));
    }

    /**
     * Show form for editing employee HR details
     */
    public function edit(User $employee)
    {
        // Security: Check Branch
        $scope = auth()->user()->getBranchIdForScope();
        if ($scope && $employee->branch_id !== $scope) {
            abort(403, 'Unauthorized access to this employee.');
        }

        // Ensure profile exists
        if (!$employee->employeeProfile) {
            EmployeeProfile::create(['user_id' => $employee->id]); // Direct create to bypass relation issues
            $employee->load('employeeProfile'); // Refresh relation
        }

        $departments = Department::all();

        return view('admin.hr.employees.edit', ['user' => $employee, 'departments' => $departments]);
    }

    /**
     * Update employee HR details
     */
    public function update(Request $request, User $employee)
    {
        // Security: Check Branch
        $scope = auth()->user()->getBranchIdForScope();
        if ($scope && $employee->branch_id !== $scope) {
            abort(403);
        }

        $request->validate([
            // User fields
            'name' => 'required|string|max:255',
            'role' => 'required|string',

            // HR Profile fields
            'job_title' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'employment_status' => 'required|string',
            'date_joined' => 'nullable|date',
            'basic_salary' => 'required|numeric|min:0', // Changed to required

            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'tin' => 'nullable|string',
            'ssnit_number' => 'nullable|string',
            'phone' => 'nullable|string', // Added
            'address' => 'nullable|string', // Added
        ]);

        DB::transaction(function () use ($request, $employee) {
            // Update User
            $employee->update([
                'name' => $request->name,
                'role' => $request->role,
            ]);

            // Update Profile
            $employee->employeeProfile()->updateOrCreate(
                ['user_id' => $employee->id],
                [
                    'job_title' => $request->job_title,
                    'department_id' => $request->department_id,
                    'employment_status' => $request->employment_status,
                    'date_joined' => $request->date_joined,
                    'basic_salary' => $request->basic_salary ?? 0,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'tin' => $request->tin,
                    'ssnit_number' => $request->ssnit_number,
                ]
            );
        });

        return redirect()->route('admin.hr.employees.index')->with('success', 'Employee profile updated.');
    }

    public function show(User $employee)
    {
        return redirect()->route('admin.hr.employees.edit', $employee);
    }
}
