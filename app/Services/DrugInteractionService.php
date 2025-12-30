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
    /**
     * Dispatch sync jobs for background processing.
     */
    public function dispatchSyncJobs()
    {
        $products = Product::where('product_type', 'goods')
            ->orderBy('last_interaction_sync', 'asc')
            ->get();

        foreach ($products as $p) {
            \App\Jobs\SyncDrugInteractionsJob::dispatch($p);
        }

        return count($products);
    }

    /**
     * Main entry point: Syncs interactions for all products or a specific one.
     * @deprecated Use dispatchSyncJobs for bulk updates to avoid timeouts.
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
    /**
     * Process a single product: Search OpenFDA for interactions.
     * Strategy: Search for OTHER drugs whose label warns about THIS drug.
     * Query: drug_interactions:"Product Name"
     */
    public function processProduct(Product $product)
    {
        // 1. Fetch Interactions from OpenFDA
        // We search for labels that mention this drug in their interactions section.
        // The results represent the "Other Drug" in the interaction pair.
        $interactingLabels = $this->fetchInteractionsFromOpenFDA($product->name);

        if (empty($interactingLabels)) {
            $product->update(['last_interaction_sync' => now()]);
            return;
        }

        $count = 0;
        foreach ($interactingLabels as $label) {
            // Extract potential names for the interacting drug
            $brandNames = $label['openfda']['brand_name'] ?? [];
            $genericNames = $label['openfda']['generic_name'] ?? [];
            $potentialNames = array_merge($brandNames, $genericNames);

            if (empty($potentialNames)) {
                continue;
            }

            // Extract the detailed interaction text
            // The field is usually an array of strings (paragraphs)
            $interactionText = $label['drug_interactions'] ?? [];
            if (is_array($interactionText)) {
                $interactionText = implode("\n\n", $interactionText);
            }

            // Truncate if too long (e.g. 1000 chars) to be safe for UI/DB
            $description = substr($interactionText, 0, 1000);
            if (strlen($interactionText) > 1000) {
                $description .= '... (consult full label)';
            }

            if (empty($description)) {
                $description = "OpenFDA Warning: The label for {$potentialNames[0]} mentions an interaction queries.";
            }

            // Find valid local products that match any of these names
            // Simple WHERE IN match for now. Could be improved with fuzzy search.
            $matchingProducts = Product::whereIn('name', $potentialNames)
                ->where('id', '!=', $product->id)
                ->get();

            foreach ($matchingProducts as $otherProduct) {
                $drugA = $product->id < $otherProduct->id ? $product->id : $otherProduct->id;
                $drugB = $product->id < $otherProduct->id ? $otherProduct->id : $product->id;

                DrugInteraction::updateOrCreate(
                    [
                        'drug_a_id' => $drugA,
                        'drug_b_id' => $drugB,
                    ],
                    [
                        'severity' => 'Moderate', // Default to moderate as we don't parse severity yet
                        'description' => $description,
                        'source' => 'OpenFDA'
                    ]
                );
                $count++;
            }
        }

        $product->update(['last_interaction_sync' => now()]);
        Log::info("Synced interactions for {$product->name} via OpenFDA. Found/Verified {$count} local links.");
    }

    /**
     * Fetch labels of drugs that interact with the given drug name.
     */
    protected function fetchInteractionsFromOpenFDA($drugName)
    {
        try {
            // Search for labels where 'drug_interactions' field contains the drug name.
            // Limit to 50 to avoid huge payloads, can paginate if needed.
            $response = Http::timeout(20)->get('https://api.fda.gov/drug/label.json', [
                'search' => 'drug_interactions:"' . $drugName . '"',
                'limit' => 100
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['results'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error("OpenFDA Search Error for {$drugName}: " . $e->getMessage());
        }

        return [];
    }

    // Deprecated helpers removed (findRxCui, fetchInteractionsFromApi)
}
