<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'appraisal_date' => 'date',
        'total_score' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function details()
    {
        return $this->hasMany(AppraisalDetail::class);
    }

    public function calculateTotalScore()
    {
        $total = $this->details()->avg('score');
        $this->update(['total_score' => $total]);
        return $total;
    }
}
