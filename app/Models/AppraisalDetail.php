<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $casts = [
    //     'score' => 'decimal:2',
    // ];

    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class);
    }

    public function kpi()
    {
        return $this->belongsTo(Kpi::class);
    }
}
