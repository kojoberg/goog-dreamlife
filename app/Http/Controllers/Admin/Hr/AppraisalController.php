<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Appraisal;
use App\Models\AppraisalDetail;
use App\Models\Kpi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppraisalController extends Controller
{
    public function index()
    {
        $branchId = auth()->user()->getBranchIdForScope();

        $appraisals = Appraisal::with(['user', 'reviewer'])
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('user', function ($u) use ($branchId) {
                    $u->where('branch_id', $branchId);
                });
            })
            ->latest()
            ->paginate(20);

        return view('admin.hr.appraisals.index', compact('appraisals'));
    }

    public function create()
    {
        $branchId = auth()->user()->getBranchIdForScope();

        // Get staff (excluding patients)
        $users = User::where('role', '!=', 'patient')
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->get();

        return view('admin.hr.appraisals.create', compact('users'));
    }

    /**
     * Store the initial appraisal record and redirect to scoring page
     */
    public function store(Request $request)
    {
        if ($request->has('period_year') && $request->has('period_month_num')) {
            $request->merge(['period_month' => $request->period_year . '-' . $request->period_month_num]);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_month' => 'required|string', // YYYY-MM
            'appraisal_date' => 'required|date',
        ]);

        // Check for duplicate
        $exists = Appraisal::where('user_id', $request->user_id)
            ->where('period_month', $request->period_month)
            ->first();

        if ($exists) {
            return redirect()->route('admin.hr.appraisals.edit', $exists)
                ->with('info', 'Appraisal already exists for this month. You can edit it below.');
        }

        $appraisal = Appraisal::create([
            'user_id' => $request->user_id,
            'reviewer_id' => auth()->id(),
            'period_month' => $request->period_month,
            'appraisal_date' => $request->appraisal_date,
            'total_score' => 0,
            'status' => 'pending_employee', // New Workflow Start
            'final_score' => 0
        ]);

        return redirect()->route('admin.hr.appraisals.index')
            ->with('success', 'Appraisal initiated! The employee has been notified to complete their self-assessment.');
    }

    /**
     * Show the scoring form
     */
    public function edit(Appraisal $appraisal)
    {
        // Check authorization
        if (auth()->id() !== $appraisal->user_id && auth()->id() !== $appraisal->reviewer_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Get KPIs based on role
        $userRole = $appraisal->user->role;
        $kpis = Kpi::where(function ($q) use ($userRole) {
            $q->whereNull('role')->orWhere('role', $userRole);
        })->get(); // We will group them in the view or here. Grouping here is easier.

        $groupedKpis = $kpis->groupBy('category');

        $appraisal->load('details');

        return view('admin.hr.appraisals.edit', compact('appraisal', 'groupedKpis'));
    }

    public function update(Request $request, Appraisal $appraisal)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:1|max:5', // 1-5 Scale
            'comments' => 'nullable|array',
            'overall_comment' => 'nullable|string'
        ]);

        // Helper to determine mode
        $isManager = (auth()->id() === $appraisal->reviewer_id) || auth()->user()->isAdmin();
        $isEmployee = (auth()->id() === $appraisal->user_id);

        DB::transaction(function () use ($request, $appraisal, $isManager, $isEmployee) {

            // Employee Self-Assessment Mode
            if ($isEmployee && ($appraisal->status === 'pending_employee' || $appraisal->status === 'draft')) {
                foreach ($request->scores as $kpiId => $score) {
                    AppraisalDetail::updateOrCreate(
                        ['appraisal_id' => $appraisal->id, 'kpi_id' => $kpiId],
                        ['self_score' => $score, 'self_comments' => $request->comments[$kpiId] ?? null]
                    );
                }
                // Transition to Pending Manager if submitted
                if ($request->has('submit_appraisal')) {
                    $appraisal->update(['status' => 'pending_manager']);
                }
            }

            // Manager Review Mode
            if ($isManager && $appraisal->status !== 'completed') {
                foreach ($request->scores as $kpiId => $score) {
                    AppraisalDetail::updateOrCreate(
                        ['appraisal_id' => $appraisal->id, 'kpi_id' => $kpiId],
                        [
                            'score' => $score, // Manager Score
                            'comments' => $request->comments[$kpiId] ?? null
                        ]
                    );
                }

                // Finalize
                if ($request->has('finalize_appraisal')) {
                    $appraisal->update([
                        'status' => 'completed',
                        'comments' => $request->overall_comment
                    ]);
                } else {
                    // Just saving draft
                    $appraisal->update(['comments' => $request->overall_comment]);
                }

                // Always calculate running score
                $appraisal->calculateTotalScore();
            }
        });

        return redirect()->route('admin.hr.appraisals.index')->with('success', 'Appraisal saved successfully.');
    }

    public function show(Appraisal $appraisal)
    {
        $appraisal->load(['details.kpi', 'user', 'reviewer']);
        return view('admin.hr.appraisals.show', compact('appraisal'));
    }

    public function destroy(Appraisal $appraisal)
    {
        $appraisal->delete();
        return back()->with('success', 'Appraisal deleted.');
    }
}
