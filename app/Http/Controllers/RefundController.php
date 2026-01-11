<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Sale;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Admin: List pending refund requests.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Refund::with(['sale.user', 'sale.items', 'requester'])->latest();

        // Branch scoping for regular admins in multi-branch mode
        if (!$user->isSuperAdmin() && is_multi_branch()) {
            $query->whereHas('sale.user', fn($q) => $q->where('branch_id', $user->branch_id));
        }

        $refunds = $query->paginate(20);

        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Cashier: Request a refund.
     */
    public function store(Request $request, Sale $sale)
    {
        if (!auth()->user()->hasPermission('process_refund')) {
            return back()->with('error', 'Unauthorized. You do not have permission to process refunds.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($sale->status !== 'completed') {
            return back()->with('error', 'Only completed sales can be refunded.');
        }

        if ($sale->refund) {
            return back()->with('error', 'A refund request already exists for this sale.');
        }

        // Check Refund Policy Days
        $settings = \App\Models\Setting::first();
        if ($settings && isset($settings->refund_policy_days) && $settings->refund_policy_days > 0) {
            $limitDate = $sale->created_at->addDays($settings->refund_policy_days);
            if (now()->gt($limitDate)) {
                return back()->with('error', "Refund period expired. Policy allows refunds within {$settings->refund_policy_days} days.");
            }
        }

        Refund::create([
            'sale_id' => $sale->id,
            'user_id' => Auth::id(),
            'amount' => $sale->total_amount,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        // Notify Admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\RefundRequestNotification($sale, Auth::user()));

        return back()->with('success', 'Refund request submitted for approval.');
    }

    /**
     * List current user's refund history.
     */
    public function myHistory()
    {
        $refunds = Refund::where('user_id', Auth::id())
            ->with(['sale.items', 'sale.patient'])
            ->latest()
            ->paginate(20);

        return view('refunds.history', compact('refunds'));
    }

    /**
     * Admin: Approve refund.
     */
    public function approve(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'This refund is not pending.');
        }

        try {
            DB::beginTransaction();

            // 1. Restock Inventory
            $this->inventoryService->restock($refund->sale);

            // 2. Update Sale Status
            $refund->sale->update(['status' => 'refunded']);

            // 3. Mark Refund Approved
            $refund->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'admin_note' => $request->admin_note
            ]);

            // 4. Reverse Loyalty Points
            if ($refund->sale->patient) {
                $patient = $refund->sale->patient;

                // Remove points earned from this sale
                if ($refund->sale->points_earned > 0) {
                    $patient->decrement('loyalty_points', $refund->sale->points_earned);
                }

                // Return points redeemed in this sale
                if ($refund->sale->points_redeemed > 0) {
                    $patient->increment('loyalty_points', $refund->sale->points_redeemed);
                }
            }

            DB::commit();

            return back()->with('success', 'Refund approved and inventory restocked.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing refund: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Reject refund.
     */
    public function reject(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'This refund is not pending.');
        }

        $refund->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'admin_note' => $request->admin_note
        ]);

        return back()->with('success', 'Refund request rejected.');
    }
}
