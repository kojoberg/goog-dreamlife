<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        // Note: Filtering by stock (aggregate) in SQL is complex without subqueries.
        // For simplicity/speed in this MVP, we fetch all and filter in PHP if low_stock requested, 
        // OR we just use a `whereHas` for non-zero logic, but low stock vs reorder level implies checking each.
        // Let's retrieve all for now if filtered.

        if ($request->filter === 'low_stock') {
            // We can't easily paginate AFTER filtering a collection without manually building a Paginator.
            // Strategy: Get IDs of low stock items first?
            // Or better: Filter in memory. If dataset is huge -> performance hit.
            // Optimized approach: SQL check.
            // sum(inventory_batches.quantity) <= reorder_level

            // Let's use get() then filter then manual pagination or just show all (assuming < 1000 products for now)
            $products = $query->get()->filter(function ($p) {
                return $p->stock <= $p->reorder_level;
            });
            // No pagination for filtered view to keep it simple
        } else {
            $products = $query->paginate(10);
        }

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_chronic' => 'sometimes|boolean',
        ]);

        if (!$request->has('is_chronic')) {
            $validated['is_chronic'] = false;
        } else {
            $validated['is_chronic'] = true;
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_chronic' => 'sometimes|boolean',
        ]);

        // Checkbox handling hack if needed, but validation handles 'sometimes'
        // If unchecked, it won't be in request, so we should default it or merge.
        if (!$request->has('is_chronic')) {
            $validated['is_chronic'] = false;
        } else {
            $validated['is_chronic'] = true; // ensure it's bool
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
