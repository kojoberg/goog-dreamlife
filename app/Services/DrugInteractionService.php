<?php

namespace App\Services;

use App\Models\Product;
use App\Models\DrugInteraction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DrugInteractionService
{
    protected $baseUrl = 'https://rxnav.nlm.nih.gov/REST/';

    /**
     * Main entry point: Syncs interactions for all products or a specific one.
     */
    public function syncInteractions(?Product $product = null)
    {
        if ($product) {
            $this->processProduct($product);
        } else {
            // Process all products, prioritize those never synced
            $products = Product::where('product_type', 'goods') // Only physical drugs
                ->orderBy('last_interaction_sync', 'asc')
                ->get();

            foreach ($products as $p) {
                $this->processProduct($p);
                // Sleep to be nice to the API
                usleep(200000); // 200ms
            }
        }
    }

    /**
     * Process a single product: identify RxCUI and fetch matches
     */
    public function processProduct(Product $product)
    {
        // 1. Identify RxCUI if missing
        if (!$product->rxcui) {
            $rxcui = $this->findRxCui($product->name);
            if ($rxcui) {
                $product->update(['rxcui' => $rxcui]);
            } else {
                Log::info("Could not find RxCUI for product: {$product->name}");
                // Mark synced anyway to avoid infinite retries immediately? 
                // No, let's leave it null but update timestamp to defer.
                $product->update(['last_interaction_sync' => now()]);
                return;
            }
        }

        // 2. Fetch Interactions for this RxCUI
        $interactions = $this->fetchInteractionsFromApi($product->rxcui);

        if (empty($interactions)) {
            $product->update(['last_interaction_sync' => now()]);
            return;
        }

        // 3. Match against DB
        $count = 0;
        foreach ($interactions as $interactionData) {
            // The API returns distinct interacting concepts.
            // Structure: interactionTypeGroup -> interactionType -> interactionPair -> interactionConcept
            // We need the RxCUI of the OTHER drug.

            $otherRxcui = $interactionData['rxcui'];
            $severity = $interactionData['severity'];
            $description = $interactionData['description'];

            // Find products in OUR db that match the interacting RxCUI
            $matchingProducts = Product::where('rxcui', $otherRxcui)
                ->where('id', '!=', $product->id)
                ->get();

            foreach ($matchingProducts as $otherProduct) {
                // Determine order (Generic: A interacts with B is same as B interacts with A)
                // Existing Migration uses drug_a_id and drug_b_id.
                // We typically store ordered by ID to prevent duplicates.

                $drugA = $product->id < $otherProduct->id ? $product->id : $otherProduct->id;
                $drugB = $product->id < $otherProduct->id ? $otherProduct->id : $product->id;

                DrugInteraction::firstOrCreate(
                    [
                        'drug_a_id' => $drugA,
                        'drug_b_id' => $drugB,
                    ],
                    [
                        'severity' => $severity,
                        'description' => $description,
                        'source' => 'NLM RxNav'
                    ]
                );
                $count++;
            }
        }

        $product->update(['last_interaction_sync' => now()]);
        Log::info("Synced interactions for {$product->name}. Found/Verified {$count} local links.");
    }

    /**
     * Search RxNav for RxCUI
     */
    protected function findRxCui($name)
    {
        // Heuristics: Remove dosage if it confuses the rough search, or keep it?
        // "Paracetamol 500mg" works well with approximateTerm.

        try {
            $response = Http::timeout(10)->get($this->baseUrl . 'approximateTerm.json', [
                'term' => $name,
                'maxEntries' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['approximateGroup']['candidate'][0]['rxcui'])) {
                    return $data['approximateGroup']['candidate'][0]['rxcui'];
                }
            }
        } catch (\Exception $e) {
            Log::error("RxNav Search Error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch raw interactions list
     */
    protected function fetchInteractionsFromApi($rxcui)
    {
        try {
            $response = Http::timeout(15)->get($this->baseUrl . 'interaction/interaction.json', [
                'rxcui' => $rxcui,
                'sources' => 'ONCHigh' // Only high certainty? Or omit for all. User said "publicly known". 
                // 'DrugBank' is simpler but proprietary. 'ONCHigh' is a good high-severity filter.
                // Let's try omitting source to get everything, usually returns ONCHigh and DrugBank.
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $parsed = [];

                if (!isset($data['interactionTypeGroup'])) {
                    return [];
                }

                foreach ($data['interactionTypeGroup'] as $group) {
                    foreach ($group['interactionType'] as $type) {
                        foreach ($type['interactionPair'] as $pair) {
                            $concept = $pair['interactionConcept'][1]; // [0] is source, [1] is target

                            $parsed[] = [
                                'rxcui' => $concept['minConceptItem']['rxcui'],
                                'name' => $concept['minConceptItem']['name'],
                                'severity' => $pair['severity'] === 'N/A' ? 'Major' : $pair['severity'], // API often says N/A for established interactions, safe to treat as significant
                                'description' => $pair['description']
                            ];
                        }
                    }
                }
                return $parsed;
            }
        } catch (\Exception $e) {
            Log::error("RxNav Interaction Error: " . $e->getMessage());
        }

        return [];
    }
}
