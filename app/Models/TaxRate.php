<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get all active tax rates.
     */
    public static function active()
    {
        return static::where('is_active', true)->get();
    }

    /**
     * Calculate total tax percentage from all active rates.
     */
    public static function totalPercentage(): float
    {
        return static::active()->sum('percentage');
    }

    /**
     * Calculate tax breakdown for a given subtotal.
     */
    public static function calculateBreakdown(float $subtotal): array
    {
        $breakdown = [];
        $totalTax = 0;

        foreach (static::active() as $tax) {
            $amount = round($subtotal * ($tax->percentage / 100), 2);
            $breakdown[$tax->code] = [
                'name' => $tax->name,
                'percentage' => $tax->percentage,
                'amount' => $amount,
            ];
            $totalTax += $amount;
        }

        return [
            'breakdown' => $breakdown,
            'total_tax' => $totalTax,
        ];
    }

    /**
     * Calculate tax breakdown for INCLUSIVE pricing.
     * Product prices already include tax - we extract/back-calculate the tax amount.
     * 
     * Formula: baseAmount = inclusiveTotal / (1 + totalTaxRate)
     *          taxAmount = inclusiveTotal - baseAmount
     * 
     * @param float $inclusiveTotal The total amount including all taxes
     * @return array Contains 'base_amount', 'total_tax', and 'breakdown'
     */
    public static function calculateInclusiveBreakdown(float $inclusiveTotal): array
    {
        $activeTaxes = static::active();
        $totalPercentage = $activeTaxes->sum('percentage');

        if ($totalPercentage <= 0) {
            return [
                'base_amount' => $inclusiveTotal,
                'total_tax' => 0,
                'breakdown' => [],
            ];
        }

        // Calculate base amount (price before tax)
        $divisor = 1 + ($totalPercentage / 100);
        $baseAmount = round($inclusiveTotal / $divisor, 2);
        $totalTax = round($inclusiveTotal - $baseAmount, 2);

        // Calculate individual tax amounts proportionally
        $breakdown = [];
        $taxSum = 0;

        foreach ($activeTaxes as $tax) {
            // Each tax's share of the total tax based on its percentage
            $taxAmount = round($totalTax * ($tax->percentage / $totalPercentage), 2);
            $breakdown[$tax->code] = [
                'name' => $tax->name,
                'percentage' => $tax->percentage,
                'amount' => $taxAmount,
            ];
            $taxSum += $taxAmount;
        }

        // Adjust for rounding errors (add/subtract difference to first tax)
        if ($taxSum != $totalTax && !empty($breakdown)) {
            $firstKey = array_key_first($breakdown);
            $breakdown[$firstKey]['amount'] += ($totalTax - $taxSum);
        }

        return [
            'base_amount' => $baseAmount,
            'total_tax' => $totalTax,
            'breakdown' => $breakdown,
        ];
    }
}
