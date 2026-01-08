<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\DrugInteraction;

class GlobalDrugInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Categories Exist
        $catAnalgesic = Category::firstOrCreate(['name' => 'Analgesics'], ['description' => 'Painkillers']);
        $catAntibiotic = Category::firstOrCreate(['name' => 'Antibiotics'], ['description' => 'Infection fighters']);
        $catBloodThinner = Category::firstOrCreate(['name' => 'Anticoagulants'], ['description' => 'Blood thinners']);
        $catED = Category::firstOrCreate(['name' => 'ED Medications'], ['description' => 'Erectile Dysfunction']);
        $catCardiac = Category::firstOrCreate(['name' => 'Cardiac'], ['description' => 'Heart medications']);

        // 2. Ensure Drugs Exist
        // Helper function to create product definition ONLY (No Stock)
        $createDrug = function ($name, $category, $price, $cost, $stock, $isChronic = false) {
            $product = Product::firstOrCreate(
                ['name' => $name],
                [
                    'category_id' => $category->id,
                    'unit_price' => $price,
                    'reorder_level' => 10,
                    'is_chronic' => $isChronic,
                    'description' => 'Global Database Definition'
                ]
            );

            // Removing Initial Batch Creation so users start with 0 stock.
            // Cost price will be set on the product definition later or during first receive.

            return $product;
        };

        $aspirin = $createDrug('Aspirin 75mg', $catBloodThinner, 10, 5, 100);
        $warfarin = $createDrug('Warfarin 5mg', $catBloodThinner, 25, 12, 50, true);
        $ibuprofen = $createDrug('Ibuprofen 400mg', $catAnalgesic, 15, 6, 100);
        $sildenafil = $createDrug('Sildenafil (Viagra) 50mg', $catED, 50, 20, 30);
        $nitroglycerin = $createDrug('Nitroglycerin (GTN)', $catCardiac, 45, 22, 20, true);
        $simvastatin = $createDrug('Simvastatin 20mg', $catCardiac, 30, 10, 80, true);
        $clarithromycin = $createDrug('Clarithromycin 500mg', $catAntibiotic, 60, 35, 40);

        // 3. Create Interactions
        $interactions = [
            [
                'drug_a' => $aspirin,
                'drug_b' => $warfarin,
                'severity' => 'severe',
                'description' => 'Significantly increased risk of bleeding. Concurrent use should generally be avoided unless strictly monitored.',
            ],
            [
                'drug_a' => $ibuprofen,
                'drug_b' => $aspirin,
                'severity' => 'moderate',
                'description' => 'Ibuprofen may interfere with the anti-platelet effect of low-dose aspirin. Risk of GI Bleeding.',
            ],
            [
                'drug_a' => $ibuprofen,
                'drug_b' => $warfarin,
                'severity' => 'severe',
                'description' => 'NSAIDs like Ibuprofen increase the risk of bleeding when taken with Warfarin. Avoid combination.',
            ],
            [
                'drug_a' => $sildenafil,
                'drug_b' => $nitroglycerin,
                'severity' => 'severe',
                'description' => 'FATAL HYPOTENSION risk. Never combine Sildenafil with Nitrates (GTN).',
            ],
            [
                'drug_a' => $simvastatin,
                'drug_b' => $clarithromycin,
                'severity' => 'severe',
                'description' => 'Increased risk of myopathy/rhabdomyolysis. Suspend Simvastatin while taking Clarithromycin.',
            ],
            [
                'drug_a' => $createDrug('Morphine Sulfate 10mg', $catAnalgesic, 15, 8, 0),
                'drug_b' => $createDrug('Pethidine HCL 100mg', $catAnalgesic, 20, 10, 0),
                'severity' => 'severe',
                'description' => 'Profound sedation, respiratory depression, coma, and death may result if used together. Avoid combination.',
            ],
        ];

        foreach ($interactions as $data) {
            $exists = DrugInteraction::where(function ($q) use ($data) {
                $q->where('drug_a_id', $data['drug_a']->id)
                    ->where('drug_b_id', $data['drug_b']->id);
            })->orWhere(function ($q) use ($data) {
                $q->where('drug_a_id', $data['drug_b']->id)
                    ->where('drug_b_id', $data['drug_a']->id);
            })->exists();

            if (!$exists) {
                DrugInteraction::create([
                    'drug_a_id' => $data['drug_a']->id,
                    'drug_b_id' => $data['drug_b']->id,
                    'severity' => $data['severity'],
                    'description' => $data['description'],
                ]);
            }
        }
    }
}
