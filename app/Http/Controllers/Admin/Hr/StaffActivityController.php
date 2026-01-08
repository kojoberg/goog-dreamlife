<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Prescription;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffActivityController extends Controller
{
    public function index(Request $request)
    {
        // Date Filter (Default to current month)
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Get all staff
        $branchId = auth()->user()->getBranchIdForScope();

        $staff = User::where('role', '!=', 'patient')
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->get();

        $activityReport = $staff->map(function ($user) use ($start, $end) {
            // 1. Sales Performance
            $salesStats = Sale::where('user_id', $user->id)
                ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->selectRaw('COUNT(*) as count, SUM(total_amount) as revenue')
                ->first();

            // 2. Clinical Activity (Prescriptions)
            $rxCount = Prescription::where('user_id', $user->id)
                ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->count();

            // 3. System Actions (Audit Logs)
            $actionCount = AuditLog::where('user_id', $user->id)
                ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
                ->count();

            return [
                'user' => $user,
                'sales_count' => $salesStats->count ?? 0,
                'sales_revenue' => $salesStats->revenue ?? 0,
                'rx_count' => $rxCount,
                'system_actions' => $actionCount,
                'role' => ucfirst($user->role)
            ];
        });

        // Sort by revenue desc (default)
        $activityReport = $activityReport->sortByDesc('sales_revenue');

        return view('admin.hr.activity.index', compact('activityReport', 'start', 'end'));
    }

    /**
     * Show detailed activity log for a specific user
     */
    public function show(User $user)
    {
        $activities = AuditLog::where('user_id', $user->id)
            ->latest()
            ->paginate(50);

        return view('admin.hr.activity.show', compact('user', 'activities'));
    }
}
