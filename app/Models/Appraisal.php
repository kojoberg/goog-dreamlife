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
        'final_score' => 'decimal:2',
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
        $this->load('details.kpi');

        $totalWeightedScore = 0;
        $totalWeight = 0;

        foreach ($this->details as $detail) {
            $weight = $detail->kpi->weight ?? 1; // Default weight 1 if missing
            $score = $detail->score ?? 0;

            $totalWeightedScore += ($score * $weight);
            $totalWeight += $weight;
        }

        if ($totalWeight > 0) {
            $finalScore = $totalWeightedScore / $totalWeight;
        } else {
            $finalScore = 0;
        }

        $this->update(['final_score' => $finalScore, 'total_score' => $finalScore]);

        return $finalScore;
    }
}
