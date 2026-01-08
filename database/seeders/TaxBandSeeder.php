<?php

namespace Database\Seeders;

use App\Models\TaxBand;
use Illuminate\Database\Seeder;

class TaxBandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Based on GRA 2024 Monthly Income Tax Rates (Example)
     */
    public function run(): void
    {
        // Clear existing
        TaxBand::query()->delete();

        $bands = [
            // First 490 - Free (0%)
            ['band_width' => 490, 'tax_rate' => 0, 'sort_order' => 1],
            // Next 110 - 5%
            ['band_width' => 110, 'tax_rate' => 5, 'sort_order' => 2],
            // Next 130 - 10%
            ['band_width' => 130, 'tax_rate' => 10, 'sort_order' => 3],
            // Next 3,000 - 17.5%
            ['band_width' => 3000, 'tax_rate' => 17.5, 'sort_order' => 4],
            // Next 16,395 - 25%
            ['band_width' => 16395, 'tax_rate' => 25, 'sort_order' => 5],
            // Next 29,875 - 30%
            ['band_width' => 29875, 'tax_rate' => 30, 'sort_order' => 6],
            // Exceeding 50,000 - 35%
            ['band_width' => null, 'tax_rate' => 35, 'sort_order' => 7],
        ];

        foreach ($bands as $band) {
            TaxBand::create($band);
        }
    }
}
