<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use \App\Traits\HasBranchScope;

    protected $guarded = [];

    protected $casts = [
        'tax_breakdown' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
