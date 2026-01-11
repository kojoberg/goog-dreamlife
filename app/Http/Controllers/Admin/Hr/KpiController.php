<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function __construct()
    {
        // KPIs are global settings - in multi-branch mode, only super admins can manage them
        $this->middleware(function ($request, $next) {
            if (is_multi_branch() && !auth()->user()->isSuperAdmin()) {
                abort(403, 'Only Super Admins can manage KPIs in multi-branch mode.');
            }
            return $next($request);
        })->except(['index']); // Allow viewing for all admins
    }

    public function index()
    {
        $kpis = Kpi::paginate(20);
        $canManage = is_single_branch() || auth()->user()->isSuperAdmin();
        return view('admin.hr.kpis.index', compact('kpis', 'canManage'));
    }

    public function create()
    {
        return view('admin.hr.kpis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:quantitative,qualitative',
            'role' => 'nullable|string',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'max_score' => 'required|integer|min:1'
        ]);

        Kpi::create($validated);
        return redirect()->route('admin.hr.kpis.index')->with('success', 'KPI created successfully.');
    }

    public function edit(Kpi $kpi)
    {
        return view('admin.hr.kpis.edit', compact('kpi'));
    }

    public function update(Request $request, Kpi $kpi)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:quantitative,qualitative',
            'role' => 'nullable|string',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'max_score' => 'required|integer|min:1'
        ]);

        $kpi->update($validated);
        return redirect()->route('admin.hr.kpis.index')->with('success', 'KPI updated successfully.');
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();
        return back()->with('success', 'KPI deleted.');
    }
}

