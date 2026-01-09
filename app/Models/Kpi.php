<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'weight' => 'decimal:2',
        'max_score' => 'integer',
    ];

    protected $attributes = [
        'max_score' => 5, // Enforce default scale of 5
    ];

    public const CATEGORIES = [
        'Core Function' => 'Core Function',
        'Behavioral' => 'Behavioral',
        'Leadership' => 'Leadership',
        'Project' => 'Project-Based',
    ];

    public function targets()
    {
        return $this->hasMany(StaffTarget::class);
    }
}
