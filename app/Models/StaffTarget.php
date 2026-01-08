<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffTarget extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'target_value' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kpi()
    {
        return $this->belongsTo(Kpi::class);
    }
}
