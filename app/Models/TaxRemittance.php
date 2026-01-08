<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRemittance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'remittance_date' => 'date',
        'tax_breakdown' => 'array',
        'total_collected' => 'decimal:2',
        'total_remitted' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'green',
            'partial' => 'yellow',
            default => 'red',
        };
    }

    /**
     * Get outstanding amount.
     */
    public function getOutstandingAttribute(): float
    {
        return $this->total_collected - $this->total_remitted;
    }
}
