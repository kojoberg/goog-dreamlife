<?php

namespace App\Services;

use App\Models\DrugInteraction;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DrugInteractionService
{
    protected $baseUrl = 'https://rxnav.nlm.nih.gov/REST';

    /**
     * Resolve and Cache RxCUI for a product.
     */
    public function resolveRxCui(Product $product)
    {
        // Skip if already has RxCUI or is a service/device
        if ($product->rxcui || $product->product_type !== 'goods') {
            return $product->rxcui;
        }

        // Clean name (remove dosage/form for search if needed, but RxNav approximate matching handles well)
        // For better results, stripping dosage might help if precise match fails, but let's try raw name first.
        // Or search 'approximateTerm'
        $response = Http::get("{$this->baseUrl}/approximateTerm.json", [
            'term' => $product->name,
            'maxEntries' => 1
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $candidates = $data['approximateGroup']['candidate'] ?? [];

            if (!empty($candidates)) {
                $candidateRxcui = $candidates[0]['rxcui'];

                // Refine to Ingredient (IN) or Precise Ingredient (PIN) to ensuring matching with Interaction API outputs
                $relatedResponse = Http::get("{$this->baseUrl}/rxcui/{$candidateRxcui}/related.json", ['tty' => 'IN']);

                $finalRxcui = $candidateRxcui; // Fallback

                if ($relatedResponse->successful()) {
                    $relatedData = $relatedResponse->json();
                    $conceptGroup = $relatedData['relatedGroup']['conceptGroup'] ?? [];
                    foreach ($conceptGroup as $group) {
                        if ($group['tty'] === 'IN' && !empty($group['conceptProperties'])) {
                            $finalRxcui = $group['conceptProperties'][0]['rxcui'];
                            break;
                        }
                    }
                }

                $product->update(['rxcui' => $finalRxcui]);
                Log::info("Resolved RxCUI for {$product->name}: $finalRxcui (Original: $candidateRxcui)");
                return $finalRxcui;
            }
        }

        Log::warning("Could not resolve RxCUI for {$product->name}");
        return null;
    }

    /**
     * Sync interactions for all products.
     */
    public function syncInteractions()
    {
        // For simplicity in this iteration, we run sync synchronously or dispatch a command.
        // Let's implement the logic to check interactions for a single product here, 
        // and loop in the command/controller.

        $products = Product::where('product_type', 'goods')->whereNotNull('rxcui')->get();
        // Since API rate limits exist, we should process in chunks or job queue.
        // For "MVP" feature: Loop all products with RxCUIs.

        $count = 0;
        foreach ($products as $product) {
            $this->findInteractionsFor($product);
            $count++;
        }

        return $count;
    }

    /**
     * Find interactions for a specific product against ALL other known products.
     * RxNav interaction/interaction.json?rxcui=... returns ALL interactions for that drug.
     * We then filter that list against proper RxCUIs we have in our DB.
     */
    public function findInteractionsFor(Product $product)
    {
        if (!$product->rxcui)
            return;

        $response = Http::get("{$this->baseUrl}/interaction/interaction.json", [
            'rxcui' => $product->rxcui
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $interactionTypeGroup = $data['interactionTypeGroup'] ?? [];

            foreach ($interactionTypeGroup as $group) {
                foreach ($group['interactionType'] ?? [] as $type) {
                    foreach ($type['interactionPair'] ?? [] as $pair) {
                        // The pair contains the interaction concept.
                        // We need to check if the *other* drug in the pair exists in our DB.

                        $otherRxcui = $pair['interactionConcept'][1]['minConceptItem']['rxcui'];
                        $description = $pair['description'];
                        $severity = 'moderate'; // RxNav doesn't always strictly give mild/severe in this endpoint, default to moderate or parse description.

                        // Find matching product in DB
                        $otherProduct = Product::where('rxcui', $otherRxcui)->first();

                        if ($otherProduct && $otherProduct->id !== $product->id) {
                            $this->recordInteraction($product, $otherProduct, $severity, $description);
                        }
                    }
                }
            }
        }
    }

    protected function recordInteraction(Product $a, Product $b, $severity, $description)
    {
        // Avoid duplicate (A-B vs B-A)
        // We enforce A < B by ID
        $firstId = $a->id < $b->id ? $a->id : $b->id;
        $secondId = $a->id < $b->id ? $b->id : $a->id;

        $exists = DrugInteraction::where('drug_a_id', $firstId)
            ->where('drug_b_id', $secondId)
            ->exists();

        if (!$exists) {
            DrugInteraction::create([
                'drug_a_id' => $firstId,
                'drug_b_id' => $secondId,
                'severity' => $severity,
                'description' => substr($description, 0, 500) // Truncate if too long
            ]);
            Log::info("Recorded interaction: {$a->name} <-> {$b->name}");
        }
    }
}
