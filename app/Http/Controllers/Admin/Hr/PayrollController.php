<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $payrollService;

    public function index()
    {
        $branchId = auth()->user()->getBranchIdForScope();

        $payrolls = Payroll::with('user')
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('user', function ($userQ) use ($branchId) {
                    $userQ->where('branch_id', $branchId);
                });
            })
            ->orderBy('month_year', 'desc')
            ->paginate(20);

        return view('admin.hr.payroll.index', compact('payrolls'));
    }

    public function create()
    {
        return view('admin.hr.payroll.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $branchId = auth()->user()->getBranchIdForScope();
        $service = new PayrollService();
        $count = $service->generatePayrollForMonth($request->month, $branchId);

        return redirect()->route('admin.hr.payroll.index')->with('success', "Generate payroll for $count employees.");
    }

    public function show(Payroll $payroll)
    {
        return view('admin.hr.payroll.show', compact('payroll'));
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return back()->with('success', 'Payroll record deleted.');
    }
}
