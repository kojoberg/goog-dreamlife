<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'work_days',
        'is_default',
    ];

    protected $casts = [
        'work_days' => 'array',
        'is_default' => 'boolean',
    ];
}
