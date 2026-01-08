<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_taxable' => 'boolean',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
