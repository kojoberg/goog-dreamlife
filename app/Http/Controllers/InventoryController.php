<?php

namespace App\Http\Controllers;

use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;

class InventoryController extends Controller
{
    /**
     * Import standard interactions.
     */
    public function importStandardInteractions()
    {
        Artisan::call('interactions:import-standard');
        return back()->with('success', 'Standard interactions imported successfully.');
    }

    /**
     * Display current inventory batches.
     */
    public function index(Request $request)
    {
        // Eager load product and supplier
        $query = InventoryBatch::with(['product', 'supplier']);

        if ($request->filter === 'expired') {
            $settings = \App\Models\Setting::first();
            $days = $settings->alert_expiry_days ?? 90;
            $query->where('expiry_date', '<=', now()->addDays($days));
        }

        $batches = $query->orderBy('expiry_date', 'asc')
            ->paginate(15);

        return view('inventory.index', compact('batches'));
    }

    /**
     * Show form to receive new stock.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        // Super admins can select target branch
        $branches = null;
        if (auth()->user()->isSuperAdmin() && is_multi_branch()) {
            $branches = \App\Models\Branch::all();
        }

        return view('inventory.create', compact('products', 'suppliers', 'branches'));
    }

    /**
     * Store new stock batches (supports multiple items).
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('receive_stock')) {
            abort(403, 'Unauthorized action.');
        }

        // Get common data
        $supplierId = $request->input('supplier_id');
        $branchId = $request->input('branch_id');

        // Super admin can specify branch, others use their own
        if (!auth()->user()->isSuperAdmin() || !$branchId) {
            $branchId = auth()->user()->branch_id;
        }

        // Parse items from JSON
        $items = json_decode($request->input('items', '[]'), true);

        if (empty($items) || !is_array($items)) {
            return back()->with('error', 'No items to receive.');
        }

        $successCount = 0;
        $errors = [];

        foreach ($items as $index => $item) {
            // Validate each item
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }

            // Check product exists
            $product = Product::find($item['product_id']);
            if (!$product) {
                $errors[] = "Row " . ($index + 1) . ": Product not found.";
                continue;
            }

            // Create inventory batch
            InventoryBatch::create([
                'product_id' => $item['product_id'],
                'supplier_id' => $supplierId,
                'batch_number' => $item['batch_number'] ?? null,
                'quantity' => (int) $item['quantity'],
                'cost_price' => $item['cost_price'] ? (float) $item['cost_price'] : null,
                'expiry_date' => $item['expiry_date'] ?: null,
                'branch_id' => $branchId,
            ]);

            $successCount++;
        }

        if ($successCount === 0) {
            return back()->with('error', 'No items were added. ' . implode(' ', $errors));
        }

        $message = "$successCount item(s) received successfully.";
        if (!empty($errors)) {
            $message .= " Some errors: " . implode(' ', $errors);
        }

        return redirect()->route('inventory.index')->with('success', $message);
    }
    /**
     * Show history of received stock.
     */
    public function history()
    {
        $batches = InventoryBatch::with(['product', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('inventory.history', compact('batches'));
    }
    /**
     * Show detailed view of a stock batch.
     */
    public function show($id)
    {
        $batch = InventoryBatch::with(['product', 'supplier'])->findOrFail($id);
        return view('inventory.show', compact('batch'));
    }
}
