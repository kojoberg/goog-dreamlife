<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\TaxRate;
use App\Models\TaxRemittance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaxReportController extends Controller
{
    public function index(Request $request)
    {
        // Default to current month
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get sales in period with tax
        $sales = Sale::whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->where('status', 'completed')
            ->where('tax_amount', '>', 0)
            ->get();

        // Calculate totals
        $totalTaxCollected = $sales->sum('tax_amount');
        $totalSales = $sales->sum('total_amount');

        // Active tax rates for reference
        $taxRates = TaxRate::active();

        // Map normalized code -> canonical name
        $taxCodeMap = $taxRates->pluck('name', 'code')->mapWithKeys(function ($name, $code) {
            return [strtoupper($code) => $name];
        })->all();

        // Aggregate tax breakdown
        $taxBreakdown = [];
        foreach ($sales as $sale) {
            if ($sale->tax_breakdown) {
                foreach ($sale->tax_breakdown as $code => $data) {
                    // Normalize code to uppercase to merge 'vat' and 'VAT'
                    $normCode = strtoupper($code);

                    // Extract amount and name depending on format (old sales vs new)
                    if (is_array($data)) {
                        $amount = $data['amount'] ?? 0;
                        $storedName = $data['name'] ?? $code;
                    } else {
                        // Old format: key => amount
                        $amount = (float) $data;
                        $storedName = $code;
                    }

                    if (!isset($taxBreakdown[$normCode])) {
                        $taxBreakdown[$normCode] = [
                            // Use canonical name from active rates if available, else stored name
                            'name' => $taxCodeMap[$normCode] ?? $storedName,
                            'amount' => 0,
                        ];
                    }
                    $taxBreakdown[$normCode]['amount'] += $amount;
                }
            }
        }

        // Get all remittance records
        $remittances = TaxRemittance::orderBy('period_start', 'desc')->paginate(10);

        // Active tax rates for reference
        $taxRates = TaxRate::active();

        return view('reports.tax', compact(
            'startDate',
            'endDate',
            'totalTaxCollected',
            'totalSales',
            'taxBreakdown',
            'remittances',
            'taxRates',
            'sales'
        ));
    }

    public function storeRemittance(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'total_collected' => 'required|numeric|min:0',
            'total_remitted' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'remittance_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $status = 'pending';
        if ($request->total_remitted >= $request->total_collected) {
            $status = 'paid';
        } elseif ($request->total_remitted > 0) {
            $status = 'partial';
        }

        TaxRemittance::create([
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'total_collected' => $request->total_collected,
            'total_remitted' => $request->total_remitted,
            'status' => $status,
            'reference_number' => $request->reference_number,
            'remittance_date' => $request->remittance_date,
            'notes' => $request->notes,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Tax remittance record created.');
    }

    public function updateRemittance(Request $request, TaxRemittance $remittance)
    {
        $request->validate([
            'total_remitted' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'remittance_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $status = 'pending';
        if ($request->total_remitted >= $remittance->total_collected) {
            $status = 'paid';
        } elseif ($request->total_remitted > 0) {
            $status = 'partial';
        }

        $remittance->update([
            'total_remitted' => $request->total_remitted,
            'status' => $status,
            'reference_number' => $request->reference_number,
            'remittance_date' => $request->remittance_date,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Remittance record updated.');
    }
}
