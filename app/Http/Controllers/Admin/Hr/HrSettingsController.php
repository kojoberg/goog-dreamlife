<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HrSettingsController extends Controller
{
    public function index()
    {
        $leaveTypes = \App\Models\Hr\LeaveType::all();
        $workShifts = \App\Models\Hr\WorkShift::all();

        return view('admin.hr.settings', compact('leaveTypes', 'workShifts'));
    }

    public function update(Request $request)
    {
        // General Config Update
        // Placeholder for future settings logic
        // $request->validate([...]);
        // Setting::set('hr_config_key', $request->value);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function storeLeaveType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:leave_types,name',
            'days_allowed' => 'required|integer|min:0',
        ]);

        \App\Models\Hr\LeaveType::create($request->all());
        return back()->with('success', 'Leave Type added.');
    }

    public function storeWorkShift(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        \App\Models\Hr\WorkShift::create($request->all());
        return back()->with('success', 'Work Shift created.');
    }
}
