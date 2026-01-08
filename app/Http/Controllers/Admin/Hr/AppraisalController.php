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
            'total_score' => 0
        ]);

        return redirect()->route('admin.hr.appraisals.edit', $appraisal);
    }

    /**
     * Show the scoring form
     */
    public function edit(Appraisal $appraisal)
    {
        // Get KPIs valid for this user's role/department ideally, but for now getting ALL or Role-matched
        $userRole = $appraisal->user->role;
        $kpis = Kpi::where(function ($q) use ($userRole) {
            $q->whereNull('role')->orWhere('role', $userRole);
        })->get();

        $appraisal->load('details');

        return view('admin.hr.appraisals.edit', compact('appraisal', 'kpis'));
    }

    public function update(Request $request, Appraisal $appraisal)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100', // Assuming 0-100 scale
            'comments' => 'nullable|array',
            'overall_comment' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $appraisal) {

            foreach ($request->scores as $kpiId => $score) {
                AppraisalDetail::updateOrCreate(
                    [
                        'appraisal_id' => $appraisal->id,
                        'kpi_id' => $kpiId
                    ],
                    [
                        'score' => $score,
                        'comments' => $request->comments[$kpiId] ?? null
                    ]
                );
            }

            $appraisal->calculateTotalScore();
            $appraisal->update(['comments' => $request->overall_comment]);
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
