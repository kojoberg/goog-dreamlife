<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            // Use query scope or raw query to allow pagination
            // "reorder_level" is a column, "stock" is NOT a column (it's likely an accessor or sum of batches).
            // If 'stock' is calculated via relation, we need a refined query.
            // Assuming Product hasMany InventoryBatch. 
            // Simplest fix for MVP: Fetch all, filter, then paginate manually (LengthAwarePaginator).

            $allProducts = $query->get()->filter(function ($p) {
                return $p->stock <= $p->reorder_level;
            });

            // Manually Paginate the Collection
            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            $perPage = 10;
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $allProducts->forPage($page, $perPage),
                $allProducts->count(),
                $perPage,
                $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        } else {
            $products = $query->paginate(10);
        }

        return view('products.index', compact('products'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products')->withoutTrashed()],
            'product_type' => 'required|in:goods,service', // New
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'drug_route' => 'nullable|string|max:255', // New
            'drug_form' => 'nullable|string|max:255',  // New
            'dosage' => 'nullable|string|max:255',     // New
            'is_chronic' => 'sometimes|boolean',
        ]);

        // Handle 'is_chronic' checkbox explicitly if it's not present in the request
        if (!$request->has('is_chronic')) {
            $validated['is_chronic'] = false;
        } else {
            $validated['is_chronic'] = true;
        }

        $product = Product::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'barcode' => $validated['barcode'] ?? null,
            'product_type' => $validated['product_type'],
            'unit_price' => $validated['unit_price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'reorder_level' => $validated['reorder_level'],
            'description' => $validated['description'] ?? null,
            'drug_route' => $validated['drug_route'] ?? null,
            'drug_form' => $validated['drug_form'] ?? null,
            'dosage' => $validated['dosage'] ?? null,
            'is_chronic' => $validated['is_chronic'],
        ]);

        // Trigger interaction sync
        \App\Jobs\SyncDrugInteractionsJob::dispatch($product);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($product->id)->withoutTrashed()],
            'product_type' => 'required|in:goods,service', // New
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'drug_route' => 'nullable|string|max:255', // New
            'drug_form' => 'nullable|string|max:255',  // New
            'dosage' => 'nullable|string|max:255',     // New
            'is_chronic' => 'sometimes|boolean',
        ]);

        if (!$request->has('is_chronic')) {
            $validated['is_chronic'] = false;
        } else {
            $validated['is_chronic'] = true;
        }

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'barcode' => $validated['barcode'] ?? $product->barcode,
            'product_type' => $validated['product_type'],
            'unit_price' => $validated['unit_price'],
            'cost_price' => $validated['cost_price'] ?? $product->cost_price,
            'reorder_level' => $validated['reorder_level'],
            'description' => $validated['description'] ?? null,
            'drug_route' => $validated['drug_route'] ?? null,
            'drug_form' => $validated['drug_form'] ?? null,
            'dosage' => $validated['dosage'] ?? null,
            'is_chronic' => $validated['is_chronic'],
        ]);

        // Trigger interaction sync
        \App\Jobs\SyncDrugInteractionsJob::dispatch($product);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function importForm()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        return view('products.import');
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=products_template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['name', 'category_id', 'product_type', 'unit_price', 'cost_price', 'reorder_level', 'description', 'drug_route', 'drug_form', 'dosage', 'is_chronic'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Example row
            fputcsv($file, [
                'Paracetamol 500mg',
                '1',
                'goods',
                '10.00',
                '5.00',
                '20',
                'Pain reliever',
                'Oral',
                'Tablet',
                '500mg',
                '0'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function processImport(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');

        // Simple CSV Parser
        $handle = fopen($file->getPathname(), 'r');
        if ($handle === false) {
            return back()->with('error', 'Could not open file.');
        }

        $header = fgetcsv($handle); // Skip header row

        $count = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            // Mapping: ['name', 'category_id', 'product_type', 'unit_price', 'cost_price', 'reorder_level', 'description', 'drug_route', 'drug_form', 'dosage', 'is_chronic']
            // Ensure row has enough columns
            if (count($row) < 4)
                continue; // Minimum required

            try {
                Product::create([
                    'name' => $row[0],
                    'category_id' => $row[1] ?: null, // Assumes valid ID provided. Ideally we'd look up by name logic if advanced.
                    'product_type' => in_array($row[2], ['goods', 'service']) ? $row[2] : 'goods',
                    'unit_price' => (float) $row[3],
                    'cost_price' => isset($row[4]) ? (float) $row[4] : 0,
                    'reorder_level' => isset($row[5]) ? (int) $row[5] : 10,
                    'description' => $row[6] ?? null,
                    'drug_route' => $row[7] ?? null,
                    'drug_form' => $row[8] ?? null,
                    'dosage' => $row[9] ?? null,
                    'is_chronic' => isset($row[10]) ? (bool) $row[10] : false,
                ]);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Row error: " . $e->getMessage();
            }
        }

        fclose($handle);

        if (count($errors) > 0) {
            return redirect()->route('products.index')->with('success', "Imported $count products. Some errors occurred: " . implode(', ', array_slice($errors, 0, 5)));
        }

        return redirect()->route('products.index')->with('success', "Imported $count products successfully.");
    }

    public function lookup(Request $request)
    {
        $barcode = $request->query('barcode');

        if (!$barcode) {
            return response()->json(['success' => false, 'message' => 'Barcode required'], 400);
        }

        try {
            // Use OpenFoodFacts API (v0)
            $url = "https://world.openfoodfacts.org/api/v0/product/{$barcode}.json";

            // Set User-Agent as requested by OpenFoodFacts
            $options = [
                "http" => [
                    "header" => "User-Agent: UVITECH-RxPMS/1.0 (internal-dev-test)"
                ]
            ];
            $context = stream_context_create($options);

            $json = file_get_contents($url, false, $context);

            if ($json === false) {
                return response()->json(['success' => false, 'message' => 'External API error'], 502);
            }

            $data = json_decode($json, true);

            if (isset($data['status']) && $data['status'] == 1) {
                $product = $data['product'];

                return response()->json([
                    'success' => true,
                    'data' => [
                        'name' => $product['product_name'] ?? $product['product_name_en'] ?? 'Unknown Product',
                        'description' => $product['generic_name'] ?? $product['generic_name_en'] ?? '',
                        'image_url' => $product['image_url'] ?? null,
                        // Could try to guess category or brand, but let's stick to basics
                    ]
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Product not found in database'], 404);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
