<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefillQueue extends Model
{
    protected $guarded = [];

    protected $casts = [
        'scheduled_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }
}
