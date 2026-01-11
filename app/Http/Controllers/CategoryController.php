<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories',
        ]);

        $category = Category::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Category created successfully.'
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'names' => 'required|string',
        ]);

        $names = array_filter(array_map('trim', explode(',', $request->names)));
        $created = 0;
        $skipped = [];

        foreach ($names as $name) {
            if (empty($name))
                continue;

            // Check if category already exists
            if (Category::where('name', $name)->exists()) {
                $skipped[] = $name;
                continue;
            }

            Category::create(['name' => $name]);
            $created++;
        }

        $message = "Created $created categories.";
        if (count($skipped) > 0) {
            $message .= " Skipped existing: " . implode(', ', $skipped);
        }

        return redirect()->route('categories.index')->with('success', $message);
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
