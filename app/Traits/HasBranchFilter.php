<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Provides branch-scoped query filtering for controllers.
 * In multi-branch mode, regular admins only see data from their branch.
 * Super admins see data from all branches.
 */
trait HasBranchFilter
{
    /**
     * Apply branch filter to a query if in multi-branch mode and user is not super admin.
     *
     * @param Builder $query
     * @param string $branchColumn Column name for branch_id (default: 'branch_id')
     * @return Builder
     */
    protected function applyBranchFilter(Builder $query, string $branchColumn = 'branch_id'): Builder
    {
        $user = auth()->user();

        // Super admins see everything
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // In single-branch mode, no filtering needed
        if (is_single_branch()) {
            return $query;
        }

        // In multi-branch mode, filter by user's branch
        return $query->where($branchColumn, $user->branch_id);
    }

    /**
     * Check if current user should see all branches.
     *
     * @return bool
     */
    protected function canSeeAllBranches(): bool
    {
        return auth()->user()->isSuperAdmin() || is_single_branch();
    }

    /**
     * Get the current user's branch ID for filtering.
     *
     * @return int|null
     */
    protected function getCurrentBranchId(): ?int
    {
        return auth()->user()->branch_id;
    }
}
