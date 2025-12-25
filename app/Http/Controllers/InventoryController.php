<?php

namespace App\Http\Controllers;

use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display current inventory batches.
     */
    public function index()
    {
        // Eager load product and supplier
        $batches = InventoryBatch::with(['product', 'supplier'])
            ->orderBy('expiry_date', 'asc')
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
        return view('inventory.create', compact('products', 'suppliers'));
    }

    /**
     * Store new stock batch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'batch_number' => 'nullable|string|max:50',
            'quantity' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        InventoryBatch::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Stock received successfully.');
    }
}
