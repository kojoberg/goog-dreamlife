<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasBranchScope
{
    /**
     * Boot the trait.
     */
    protected static function bootHasBranchScope()
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            // Apply scope if user is logged in
            if (Auth::check()) {
                $user = Auth::user();

                // If user belongs to a branch, restrict query
                // Super Admins (role_id 1) might want to see all? 
                // For now, let's strictly enforce branch if set, unless explicitly ignored.

                if ($user->branch_id) {
                    $builder->where(function ($query) use ($user) {
                        $query->where('branch_id', $user->branch_id)
                            ->orWhereNull('branch_id');
                    });
                }
            }
        });

        // Auto-assign branch_id on create
        static::creating(function ($model) {
            if (Auth::check() && !$model->branch_id) {
                $model->branch_id = Auth::user()->branch_id;
            }
        });
    }
}
