<?php

namespace App\Traits;

trait BranchScoped
{
    /**
     * Scope query to current user's branch.
     * Super admins see all data.
     */
    public function scopeForCurrentBranch($query)
    {
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        // Super admins see all branches
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Regular users only see their branch
        return $query->where($this->getTable() . '.branch_id', $user->branch_id);
    }

    /**
     * Scope query to a specific branch.
     */
    public function scopeForBranch($query, $branchId)
    {
        if ($branchId) {
            return $query->where($this->getTable() . '.branch_id', $branchId);
        }
        return $query;
    }
}
