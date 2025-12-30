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
        return view('procurement.orders.show', compact('order'));
    }

    public function receive(Request $request, PurchaseOrder $order)
    {
        // Receive Stock
        if ($order->status === 'received') {
            return back()->with('error', 'Order already received.');
        }

        $request->validate(['received_by' => 'required|string|max:255']);

        DB::transaction(function () use ($order, $request) {
            foreach ($order->items as $item) {
                if ($item->quantity_received < $item->quantity_ordered) {
                    // Update Item
                    $qty = $item->quantity_ordered; // Simple: Full receive
                    $item->update(['quantity_received' => $qty]);

                    // Add to Inventory Batch
                    InventoryBatch::create([
                        'product_id' => $item->product_id,
                        'supplier_id' => $order->supplier_id,
                        'batch_number' => 'PO-' . $order->id . '-' . time(),
                        'quantity' => $qty,
                        'expiry_date' => now()->addYear(),
                        'cost_price' => $item->unit_cost,
                    ]);

                    // Update Product Cost Price to latest received cost
                    $item->product->update(['cost_price' => $item->unit_cost]);
                }
            }
            $order->update([
                'status' => 'received',
                'received_by' => $request->received_by // Save staff name
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
