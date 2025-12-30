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
        $refunds = Refund::with(['sale.user', 'sale.items', 'requester'])
            ->latest()
            ->paginate(20);

        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Cashier: Request a refund.
     */
    public function store(Request $request, Sale $sale)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($sale->status !== 'completed') {
            return back()->with('error', 'Only completed sales can be refunded.');
        }

        if ($sale->refund) {
            return back()->with('error', 'A refund request already exists for this sale.');
        }

        Refund::create([
            'sale_id' => $sale->id,
            'user_id' => Auth::id(),
            'amount' => $sale->total_amount,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Refund request submitted for approval.');
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
