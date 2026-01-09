<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\InventoryBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    // --- PURCHASE ORDERS ---
    public function index()
    {
        $orders = PurchaseOrder::with(['supplier', 'user'])->latest()->paginate(10);
        return view('procurement.orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = \App\Models\Product::all();
        return view('procurement.orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create_purchase_order')) {
            return back()->with('error', 'Unauthorized. You do not have permission to create Purchase Orders.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $order = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'expected_date' => $request->expected_date,
                'notes' => $request->notes,
                'total_amount' => 0, // Recalculate below
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
                $total += $item['quantity'] * $item['unit_cost'];
            }

            $order->update(['total_amount' => $total]);
        });

        return redirect()->route('procurement.orders.index')->with('success', 'Purchase Order Created');
    }

    public function show(PurchaseOrder $order)
    {
        $order->load(['items.product', 'supplier']);

        // Get users for receiver selection (filter by branch in multi-mode)
        $usersQuery = \App\Models\User::whereIn('role', ['pharmacist', 'admin']);
        if (is_multi_branch() && $order->branch_id) {
            $usersQuery->where('branch_id', $order->branch_id);
        }
        $users = $usersQuery->orderBy('name')->get();

        return view('procurement.orders.show', compact('order', 'users'));
    }

    public function receive(Request $request, PurchaseOrder $order)
    {
        if (!auth()->user()->hasPermission('receive_stock')) {
            return back()->with('error', 'Unauthorized. You do not have permission to receive stock.');
        }

        // Receive Stock
        if ($order->status === 'received') {
            return back()->with('error', 'Order already received.');
        }

        $request->validate([
            'received_by' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.batch_number' => 'required|string|max:100',
            'items.*.expiry_date' => 'required|date|after:today',
        ]);

        DB::transaction(function () use ($order, $request) {
            foreach ($order->items as $item) {
                // Check if this item is in the request
                if (isset($request->items[$item->id])) {
                    $itemData = $request->items[$item->id];
                    $receivedQty = (int) $itemData['quantity'];

                    // Validate not receiving more than ordered (or remaining)
                    $remaining = $item->quantity_ordered - $item->quantity_received;
                    if ($receivedQty > $remaining) {
                        $receivedQty = $remaining; // Cap at remaining
                    }

                    if ($receivedQty > 0) {
                        $batchNumber = $itemData['batch_number'];
                        $expiryDate = $itemData['expiry_date'];

                        // Update Item (Increment received count)
                        $item->increment('quantity_received', $receivedQty);

                        // Add to Inventory Batch
                        InventoryBatch::create([
                            'product_id' => $item->product_id,
                            'supplier_id' => $order->supplier_id,
                            'batch_number' => $batchNumber,
                            'quantity' => $receivedQty,
                            'expiry_date' => $expiryDate,
                            'cost_price' => $item->unit_cost,
                        ]);

                        // Update Product Cost Price
                        $item->product->update(['cost_price' => $item->unit_cost]);
                    }
                }
            }

            // Update Order Status
            // Check if ALL items are fully received
            $order->refresh();
            $allReceived = $order->items->every(function ($item) {
                return $item->quantity_received >= $item->quantity_ordered;
            });

            $order->update([
                'status' => $allReceived ? 'received' : 'partial',
                'received_by' => $request->received_by
            ]);
        });

        return back()->with('success', 'Stock Received and Inventory Updated.');
    }

    public function print(PurchaseOrder $order)
    {
        $order->load(['items.product', 'supplier', 'user']);
        $settings = \App\Models\Setting::firstOrCreate(['id' => 1], [
            'business_name' => 'Dream Life Healthcare',
            'phone' => '000-000-0000',
            'currency_symbol' => 'GHS'
        ]);
        return view('procurement.orders.print', compact('order', 'settings'));
    }
}
