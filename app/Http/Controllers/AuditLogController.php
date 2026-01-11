<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Access check: Must be admin
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = AuditLog::with('user')->latest();

        // Branch scoping: Non-super admins in multi-branch mode see only their branch's logs
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            $query->whereHas('user', fn($q) => $q->where('branch_id', $user->branch_id));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(20);

        // Filter users list by branch for non-super admins
        $usersQuery = User::query();
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            $usersQuery->where('branch_id', $user->branch_id);
        }
        $users = $usersQuery->get();

        return view('admin.audit.index', compact('logs', 'users'));
    }
}

