<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index()
    {
        $kpis = Kpi::paginate(20);
        return view('admin.hr.kpis.index', compact('kpis'));
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
            'description' => 'nullable|string'
        ]);

        Kpi::create($validated);
        return redirect()->route('admin.hr.kpis.index')->with('success', 'KPI created successfully.');
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();
        return back()->with('success', 'KPI deleted.');
    }
}
