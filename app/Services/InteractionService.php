<?php

namespace App\Services;

use App\Models\DrugInteraction;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class InteractionService
{
    /**
     * Import standard interactions from local JSON source.
     * 
     * @return int Number of interactions created/verified
     */
    public function importStandardInteractions(): int
    {
        $path = database_path('interactions_onchigh.json');

        if (!File::exists($path)) {
            Log::error("Interaction source file not found at: $path");
            return 0;
        }

        $data = json_decode(File::get($path), true);
        if (!$data) {
            Log::error("Failed to decode interaction source file.");
            return 0;
        }

        $count = 0;

        foreach ($data as $item) {
            // Find products matching the names (case-insensitive partial match could be risky, 
            // so let's try exact or 'like' match. The source has "Aspirin", "Warfarin".
            // Our DB has "Aspirin 75mg". So 'LIKE %Name%' is safer.

            $drugAName = $item['drug_a'];
            $drugBName = $item['drug_b'];

            $productsA = Product::where('name', 'LIKE', "%{$drugAName}%")->get();
            $productsB = Product::where('name', 'LIKE', "%{$drugBName}%")->get();

            if ($productsA->isEmpty() || $productsB->isEmpty()) {
                continue;
            }

            foreach ($productsA as $p1) {
                foreach ($productsB as $p2) {
                    if ($p1->id === $p2->id)
                        continue;

                    // Enforce order to prevent duplicates if we assume A < B logic, 
                    // or just rely on the seeding logic which checks matching pairs.
                    // Let's use firstOrCreate with explicit IDs.

                    // Note: If we have multiple Aspirin products and multiple Warfarin products,
                    // this will create N*M interaction rows. This is correct behavior.

                    DrugInteraction::firstOrCreate(
                        [
                            'drug_a_id' => $p1->id,
                            'drug_b_id' => $p2->id,
                        ],
                        [
                            'severity' => $item['severity'], // 'severe' or 'moderate'
                            'description' => $item['description']
                        ]
                    );

                    // Also create the reverse pair? 
                    // The POS query checks (A=1 AND B=2) OR (A=2 AND B=1).
                    // So one record is sufficient.

                    $count++;
                }
            }
        }

        return $count;
    }
}
