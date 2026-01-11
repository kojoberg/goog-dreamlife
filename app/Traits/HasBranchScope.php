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

                // Super admins can see all branches
                if ($user->isSuperAdmin()) {
                    return; // No scope restriction for super admins
                }

                // Regular users see only their branch data
                if ($user->branch_id) {
                    $builder->where(function ($query) use ($user) {
                        $query->where('branch_id', $user->branch_id)
                            ->orWhereNull('branch_id');
                    });
                }
            }
        });

        // Auto-assign branch_id on create (only if not already set)
        static::creating(function ($model) {
            if (Auth::check() && empty($model->branch_id)) {
                // Only auto-assign if branch_id is not explicitly provided
                $model->branch_id = Auth::user()->branch_id;
            }
        });
    }
}
