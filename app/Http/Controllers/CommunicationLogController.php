<?php

namespace App\Http\Controllers;

use App\Models\CommunicationLog;
use Illuminate\Http\Request;

class CommunicationLogController extends Controller
{
    /**
     * Display a listing of communication logs.
     * Access: Super Admin (multi-branch) OR Admin (single-branch)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Access control: Super Admin in multi-branch, Admin in single-branch
        if (is_multi_branch() && !$user->isSuperAdmin()) {
            abort(403, 'Super Admin access required in multi-branch mode.');
        }

        if (!$user->isAdmin()) {
            abort(403, 'Admin access required.');
        }

        $query = CommunicationLog::with(['user', 'branch'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('context', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // Stats
        $stats = [
            'total' => CommunicationLog::count(),
            'sms' => CommunicationLog::ofType('sms')->count(),
            'email' => CommunicationLog::ofType('email')->count(),
            'failed' => CommunicationLog::withStatus('failed')->count(),
        ];

        return view('admin.communication-logs.index', compact('logs', 'stats'));
    }
}
