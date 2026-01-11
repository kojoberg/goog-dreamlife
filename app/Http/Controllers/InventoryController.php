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
     * Store new stock batch.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('receive_stock')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'batch_number' => 'nullable|string|max:50',
            'quantity' => 'required|integer|min:1',
            'cost_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Super admin can specify branch, others use their own
        if (!auth()->user()->isSuperAdmin() || !isset($validated['branch_id'])) {
            $validated['branch_id'] = auth()->user()->branch_id;
        }

        InventoryBatch::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Stock received successfully.');
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
