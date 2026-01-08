<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Appraisal;
use App\Models\Message;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HrDashboardController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->getBranchIdForScope();

        // 1. Employee Stats
        $employeesQuery = User::where('role', '!=', 'patient')
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });

        $totalEmployees = $employeesQuery->count();

        // Breakdown by role (e.g. Doctor: 5, Nurse: 10)
        $employeesByRole = (clone $employeesQuery)
            ->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        // 2. Payroll Stats (Latest Month)
        // Find latest payroll month
        // We need to filter Payrolls by user branch
        $latestPayrollMonth = Payroll::max('month_year');

        $payrollQuery = Payroll::where('month_year', $latestPayrollMonth)
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('user', function ($u) use ($branchId) {
                    $u->where('branch_id', $branchId);
                });
            });

        $lastPayrollCost = $payrollQuery->sum('net_salary');
        $lastPayrollCount = $payrollQuery->count();

        // 3. Pending Appraisals (Users who haven't had an appraisal this month)
        // Simple metric: Count of appraisals done this month vs Total Staff
        $currentMonth = now()->format('Y-m');
        $appraisalsCount = Appraisal::where('period_month', $currentMonth)
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('user', function ($u) use ($branchId) {
                    $u->where('branch_id', $branchId);
                });
            })
            ->count();

        $pendingAppraisals = max(0, $totalEmployees - $appraisalsCount);

        // 4. Recent Messages
        $recentMessages = Message::where('recipient_id', auth()->id())
            ->orWhere('recipient_role', auth()->user()->role)
            ->orWhere('recipient_role', 'all')
            ->with('sender')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.hr.dashboard', compact(
            'totalEmployees',
            'employeesByRole',
            'lastPayrollCost',
            'lastPayrollCount',
            'latestPayrollMonth',
            'pendingAppraisals',
            'appraisalsCount',
            'recentMessages'
        ));
    }
}
