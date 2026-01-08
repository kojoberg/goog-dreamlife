<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::orderBy('name')->get();
        return view('settings.tax-rates', compact('taxRates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:tax_rates,code',
            'percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        TaxRate::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'percentage' => $request->percentage,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return back()->with('success', 'Tax rate added successfully.');
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:tax_rates,code,' . $taxRate->id,
            'percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $taxRate->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'percentage' => $request->percentage,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Tax rate updated successfully.');
    }

    public function toggle(TaxRate $taxRate)
    {
        $taxRate->update(['is_active' => !$taxRate->is_active]);
        $status = $taxRate->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Tax rate {$status}.");
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();
        return back()->with('success', 'Tax rate deleted.');
    }
}
