<?php

namespace App\Http\Controllers;

use App\Models\DrugInteraction;
use App\Models\Product;
use Illuminate\Http\Request;

class DrugInteractionController extends Controller
{
    public function sync()
    {
        set_time_limit(300); // 5 minutes

        try {
            $service = new \App\Services\DrugInteractionService();
            $count = $service->dispatchSyncJobs();

            return back()->with('success', "Sync initiated for {$count} products in the background.");
        } catch (\Exception $e) {
            return back()->with('error', 'Sync launch failed: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $interactions = DrugInteraction::with(['drugA', 'drugB'])->paginate(10);
        return view('interactions.index', compact('interactions'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('interactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'drug_a_id' => 'required|exists:products,id',
            'drug_b_id' => 'required|exists:products,id|different:drug_a_id',
            'severity' => 'required|in:mild,moderate,severe',
            'description' => 'required|string',
        ]);

        // Check if exists
        $exists = DrugInteraction::where(function ($q) use ($validated) {
            $q->where('drug_a_id', $validated['drug_a_id'])
                ->where('drug_b_id', $validated['drug_b_id']);
        })->orWhere(function ($q) use ($validated) {
            $q->where('drug_a_id', $validated['drug_b_id'])
                ->where('drug_b_id', $validated['drug_a_id']);
        })->exists();

        if ($exists) {
            return back()->with('error', 'Interaction for these drugs already exists.');
        }

        DrugInteraction::create($validated);

        return redirect()->route('drug-interactions.index')->with('success', 'Interaction recorded.');
    }

    public function destroy(DrugInteraction $drugInteraction)
    {
        $drugInteraction->delete();
        return back()->with('success', 'Interaction deleted.');
    }
}
