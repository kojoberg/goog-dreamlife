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
        // Sales processed by this cashier are linked to their cashier shifts
        $sales = Sale::where('status', 'completed')
            ->whereHas('cashierShift', function ($q) {
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
                'cashier_shift_id' => $shift->id, // Assign to Cashier's Shift (keeps pharmacist's shift_id intact)
            ]);

            // Award Loyalty Points (now that payment is received)
            $settings = Setting::first();
            if ($settings && $settings->loyalty_spend_per_point > 0 && $sale->total_amount > 0) {
                $pointsEarned = floor($sale->total_amount / $settings->loyalty_spend_per_point);
                if ($pointsEarned > 0) {
                    $sale->update(['points_earned' => $pointsEarned]);

                    $patient = $sale->patient;
                    if ($patient) {
                        $patient->increment('loyalty_points', $pointsEarned);
                    }
                }
            }

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
                // Log successful email
                \App\Models\CommunicationLog::create([
                    'type' => 'email',
                    'recipient' => $patient->email,
                    'message' => 'Sale Receipt #' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                    'status' => 'sent',
                    'context' => 'cashier_receipt',
                    'user_id' => auth()->id(),
                    'branch_id' => auth()->user()?->branch_id,
                ]);
            } catch (\Exception $e) {
                // Log failed email
                \App\Models\CommunicationLog::create([
                    'type' => 'email',
                    'recipient' => $patient->email,
                    'message' => 'Sale Receipt #' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                    'status' => 'failed',
                    'response' => $e->getMessage(),
                    'context' => 'cashier_receipt',
                    'user_id' => auth()->id(),
                    'branch_id' => auth()->user()?->branch_id,
                ]);
            }
        }

        // SMS
        if ($patient && $patient->phone) {
            try {
                $smsService = new \App\Services\SmsService();
                $message = "Thank you for shopping at Dream Life! Amount: GHS " . number_format($sale->total_amount, 2) . ". Receipt #" . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
                $smsService->sendQuickSms($patient->phone, $message, 'cashier_receipt');
            } catch (\Exception $e) {
            }
        }
    }
}
