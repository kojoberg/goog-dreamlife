<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            [
                'name' => 'NHIL (National Health Insurance Levy)',
                'code' => 'NHIL',
                'percentage' => 2.50,
                'description' => 'National Health Insurance Levy - 2.5%',
                'is_active' => true,
            ],
            [
                'name' => 'GETFund Levy',
                'code' => 'GETF',
                'percentage' => 2.50,
                'description' => 'Ghana Education Trust Fund - 2.5%',
                'is_active' => true,
            ],
            [
                'name' => 'COVID-19 Health Recovery Levy',
                'code' => 'COVID',
                'percentage' => 1.00,
                'description' => 'COVID-19 Health Recovery Levy - 1%',
                'is_active' => true,
            ],
            [
                'name' => 'VAT (Value Added Tax)',
                'code' => 'VAT',
                'percentage' => 15.00,
                'description' => 'Value Added Tax - 15%',
                'is_active' => true,
            ],
        ];

        foreach ($taxes as $tax) {
            TaxRate::firstOrCreate(
                ['code' => $tax['code']],
                $tax
            );
        }
    }
}
