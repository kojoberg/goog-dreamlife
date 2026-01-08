<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('branch_user', function (\Illuminate\Database\Eloquent\Builder $builder) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();

                // If user is Admin (and has a branch), they should only see shifts from users in their branch
                // If user is Super Admin (no branch), key is null, sees all.
                if ($user->branch_id) {
                    $builder->whereHas('user', function ($q) use ($user) {
                        $q->where('branch_id', $user->branch_id);
                    });
                }
            }
        });
    }

    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'starting_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function isOpen()
    {
        return $this->end_time === null;
    }
}
