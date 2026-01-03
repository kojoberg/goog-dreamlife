<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    /**
     * Display pending invoices for the cashier's branch.
     */
    public function index()
    {
        $branchId = Auth::user()->branch_id;

        // Fetch valid pending sales for this branch
        $pendingSales = Sale::where('status', 'pending_payment')
            ->whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with(['user', 'patient'])
            ->latest()
            ->get();

        return view('cashier.index', compact('pendingSales'));
    }

    /**
     * Display sales history processed by this cashier.
     */
    public function history()
    {
        // Sales processed by this cashier are linked to their shifts
        $sales = Sale::where('status', 'completed')
            ->whereHas('shift', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->with(['patient', 'items'])
            ->latest()
            ->paginate(20);

        return view('cashier.history', compact('sales'));
    }

    /**
     * Show the payment form for a specific sale.
     */
    public function show(Sale $sale)
    {
        // Enforce Shift Check
        if (!Auth::user()->hasOpenShift()) {
            return redirect()->route('shifts.create')
                ->with('error', 'You must open a shift before processing payments.');
        }

        $sale->load('items.product', 'patient', 'user');
        return view('cashier.show', compact('sale'));
    }

    /**
     * Store a newly created resource in storage.
     * This method might be used to select a sale for payment after a shift check.
     */
    public function store(Request $request)
    {
        // Enforce Shift Check
        if (!Auth::user()->hasOpenShift()) {
            return redirect()->route('shifts.create')
                ->with('error', 'You must open a shift before processing payments.');
        }

        $sale = Sale::findOrFail($request->sale_id);
        // The original snippet had 'uct', 'patient', 'user');' which seems like a copy-paste error.
        // Assuming the intent was to load relations if needed, or simply find the sale.
        // If this method is meant to *show* the sale after selection, it should load relations.
        $sale->load('items.product', 'patient', 'user');
        return view('cashier.show', compact('sale'));
    }

    /**
     * Process the payment and finalize the sale.
     */
    public function update(Request $request, Sale $sale)
    {
        if ($sale->status !== 'pending_payment') {
            \Illuminate\Support\Facades\Log::info("Status mismatch: " . $sale->status);
            return redirect()->route('cashier.index')->with('error', 'This sale is already processed.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,mobile_money,card',
            'amount_tendered' => 'required|numeric|min:' . $sale->total_amount,
        ]);

        try {
            DB::beginTransaction();

            // Get Cashier's Open Shift
            $shift = \App\Models\Shift::where('user_id', Auth::id())
                ->whereNull('end_time')
                ->first();

            if (!$shift) {
                \Illuminate\Support\Facades\Log::info("No shift found for user " . Auth::id());
                return back()->with('error', 'You must have an open shift to accept payments.');
            }

            // Update Sale Details
            $amountTendered = $request->amount_tendered;
            $changeAmount = $amountTendered - $sale->total_amount;

            $sale->update([
                'status' => 'completed',
                'amount_tendered' => $amountTendered,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
                // We might want to track WHO finalized it. 
                // Currently user_id is the Creator (Pharmacist). 
                // We might need a 'cashier_id' column or just rely on 'shift_id' (which links to Cashier).
                'shift_id' => $shift->id, // Assign to Cashier's Shift
            ]);

            DB::commit();

            // Notify (SMS/Email) - Copied logic from PosController
            $this->sendNotifications($sale);

            return redirect()->route('pos.receipt', $sale->id)->with('success', 'Payment successful.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    protected function sendNotifications(Sale $sale)
    {
        // Email
        $patient = $sale->patient; // Or checking request inputs if they were stored? 
        // Note: PosController used request->email. We don't have that here unless we store it on Sale or Patient.
        // Assuming Patient email for now or skipping ad-hoc email if not stored.

        if ($patient && $patient->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($patient->email)->send(new \App\Mail\SaleReceipt($sale));
            } catch (\Exception $e) {
            }
        }

        // SMS
        if ($patient && $patient->phone) {
            try {
                $smsService = new \App\Services\SmsService();
                $message = "Thank you for shopping at Dream Life! Amount: GHS " . number_format($sale->total_amount, 2) . ". Receipt #" . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
                $smsService->sendQuickSms($patient->phone, $message);
            } catch (\Exception $e) {
            }
        }
    }
}
