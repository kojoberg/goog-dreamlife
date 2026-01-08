<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Product;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (strlen($query) < 2) {
            return response()->json([
                'patients' => [],
                'products' => [],
            ]);
        }

        $patients = Patient::where('name', 'like', "%{$query}%")
            ->orWhere('id', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'phone']);

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->take(5)
            ->get();

        $products->transform(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'generic_name' => $product->description, // Fallback to description or null
                'unit_price' => $product->unit_price,
                'stock' => $product->stock, // Accessor
            ];
        });

        return response()->json([
            'patients' => $patients,
            'products' => $products,
        ]);
    }
}
