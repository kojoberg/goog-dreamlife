<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DrugInteraction;

class SafetyDatabaseController extends Controller
{
    /**
     * Display the safety database status and inventory interactions.
     */
    public function index()
    {
        // Count reference data
        $referenceCount = DB::table('reference_interactions')->count();

        // Count active inventory links
        $activeLinksCount = DrugInteraction::count();

        // Get recent active interactions
        $interactions = DrugInteraction::with(['drugA', 'drugB'])->latest()->paginate(20);

        return view('admin.safety.index', compact('referenceCount', 'activeLinksCount', 'interactions'));
    }

    /**
     * Sync Reference Database with Inventory.
     * Matches string names from reference table to actual Product IDs.
     */
    public function sync()
    {
        $references = DB::table('reference_interactions')->get();
        $syncedCount = 0;

        foreach ($references as $ref) {
            // Find products matching Drug A name
            $drugA_Candidates = Product::where(function ($q) use ($ref) {
                $q->where('name', 'LIKE', "%{$ref->drug_a_name}%")
                    ->orWhere('description', 'LIKE', "%{$ref->drug_a_name}%");
            })->pluck('id');

            // Find products matching Drug B name
            $drugB_Candidates = Product::where(function ($q) use ($ref) {
                $q->where('name', 'LIKE', "%{$ref->drug_b_name}%")
                    ->orWhere('description', 'LIKE', "%{$ref->drug_b_name}%");
            })->pluck('id');

            if ($drugA_Candidates->isEmpty() || $drugB_Candidates->isEmpty()) {
                continue;
            }

            // Create Combinations (Cross Join)
            foreach ($drugA_Candidates as $idA) {
                foreach ($drugB_Candidates as $idB) {
                    // Prevent self-interaction (though unlikely given different names)
                    if ($idA === $idB)
                        continue;

                    // Ensure strict ordering ID_A < ID_B to check existence easily if we wanted Unique(A,B)
                    // But migration enforces Unique(A,B), so we just try/catch or firstOrCreate.
                    // Actually migration enforces Unique(A,B). We must check both directions.

                    $exists = DrugInteraction::where(function ($q) use ($idA, $idB) {
                        $q->where('drug_a_id', $idA)->where('drug_b_id', $idB);
                    })->orWhere(function ($q) use ($idA, $idB) {
                        $q->where('drug_a_id', $idB)->where('drug_b_id', $idA);
                    })->exists();

                    if (!$exists) {
                        DrugInteraction::create([
                            'drug_a_id' => $idA,
                            'drug_b_id' => $idB,
                            'severity' => $ref->severity,
                            'description' => $ref->description . " (Source: Safety DB Sync)",
                        ]);
                        $syncedCount++;
                    }
                }
            }
        }

        return back()->with('success', "Sync Complete! Linked {$syncedCount} new interaction pairs to your inventory.");
    }

    /**
     * Show the form for editing a drug interaction.
     */
    public function edit(DrugInteraction $interaction)
    {
        $products = Product::orderBy('name')->get();
        return view('admin.safety.edit', compact('interaction', 'products'));
    }

    /**
     * Update the specified drug interaction.
     */
    public function update(Request $request, DrugInteraction $interaction)
    {
        $validated = $request->validate([
            'severity' => 'required|in:mild,moderate,severe',
            'description' => 'required|string|max:1000',
        ]);

        $interaction->update($validated);

        return redirect()->route('safety.index')->with('success', 'Interaction updated successfully.');
    }

    /**
     * Remove the specified drug interaction.
     */
    public function destroy(DrugInteraction $interaction)
    {
        $interaction->delete();
        return back()->with('success', 'Interaction deleted successfully.');
    }
}
