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

        // 2. Ensure Drugs Exist (Using firstOrCreate to avoid duplicates)
        $msg = "Seeded by Global Import";

        $aspirin = Product::firstOrCreate(
            ['name' => 'Aspirin 75mg'],
            ['category_id' => $catBloodThinner->id, 'sell_price' => 10, 'cost_price' => 5, 'stock' => 100, 'reorder_level' => 20, 'description' => $msg]
        );

        $warfarin = Product::firstOrCreate(
            ['name' => 'Warfarin 5mg'],
            ['category_id' => $catBloodThinner->id, 'sell_price' => 25, 'cost_price' => 12, 'stock' => 50, 'reorder_level' => 10, 'is_chronic' => true, 'description' => $msg]
        );

        $ibuprofen = Product::firstOrCreate(
            ['name' => 'Ibuprofen 400mg'],
            ['category_id' => $catAnalgesic->id, 'sell_price' => 15, 'cost_price' => 6, 'stock' => 100, 'reorder_level' => 20, 'description' => $msg]
        );

        $sildenafil = Product::firstOrCreate(
            ['name' => 'Sildenafil (Viagra) 50mg'],
            ['category_id' => $catED->id, 'sell_price' => 50, 'cost_price' => 20, 'stock' => 30, 'reorder_level' => 5, 'description' => $msg]
        );

        $nitroglycerin = Product::firstOrCreate(
            ['name' => 'Nitroglycerin (GTN)'],
            ['category_id' => $catCardiac->id, 'sell_price' => 45, 'cost_price' => 22, 'stock' => 20, 'reorder_level' => 5, 'is_chronic' => true, 'description' => $msg]
        );

        $simvastatin = Product::firstOrCreate(
            ['name' => 'Simvastatin 20mg'],
            ['category_id' => $catCardiac->id, 'sell_price' => 30, 'cost_price' => 10, 'stock' => 80, 'reorder_level' => 15, 'is_chronic' => true, 'description' => $msg]
        );

        $clarithromycin = Product::firstOrCreate(
            ['name' => 'Clarithromycin 500mg'],
            ['category_id' => $catAntibiotic->id, 'sell_price' => 60, 'cost_price' => 35, 'stock' => 40, 'reorder_level' => 10, 'description' => $msg]
        );

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
        ];

        foreach ($interactions as $data) {
            // Check if exists to avoid dupes
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
