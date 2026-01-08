<?php

if (!function_exists('is_multi_branch')) {
    /**
     * Check if the pharmacy is operating in multi-branch mode.
     *
     * @return bool
     */
    function is_multi_branch(): bool
    {
        return config('pharmacy.mode') === 'multi';
    }
}

if (!function_exists('is_single_branch')) {
    /**
     * Check if the pharmacy is operating in single-branch mode.
     *
     * @return bool
     */
    function is_single_branch(): bool
    {
        return config('pharmacy.mode') === 'single';
    }
}
