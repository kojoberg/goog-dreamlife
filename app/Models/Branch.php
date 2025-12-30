<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'is_main',
        'has_cashier',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'has_cashier' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
