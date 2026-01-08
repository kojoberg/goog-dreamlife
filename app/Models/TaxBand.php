<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxBand extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'band_width' => 'decimal:2', // Null indicates 'Excess'
        'tax_rate' => 'decimal:2',
    ];
}
