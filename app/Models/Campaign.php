<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'filters' => 'array',
        'is_personalized' => 'boolean',
    ];

    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatsAttribute()
    {
        return [
            'total' => $this->recipients()->count(),
            'sent' => $this->recipients()->where('status', 'sent')->count(),
            'failed' => $this->recipients()->where('status', 'failed')->count(),
            'pending' => $this->recipients()->where('status', 'pending')->count(),
        ];
    }
}
