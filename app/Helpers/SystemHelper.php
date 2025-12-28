<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class SystemHelper
{
    /**
     * Get the current system version from Git.
     * Tries multiple methods to get the branch and commit hash.
     *
     * @return string
     */
    public static function getSystemVersion(): string
    {
        $version = 'v1.0 (Static)';
        $branch = null;
        $hash = null;

        try {
            // Attempt 1: exec() commands
            $branch = trim(shell_exec('git branch --show-current 2>/dev/null'));
            $hash = trim(shell_exec('git rev-parse --short HEAD 2>/dev/null'));

            // Attempt 2: Read .git files directly (fallback if exec is disabled/fails)
            if (empty($branch) || empty($hash)) {
                $headPath = base_path('.git/HEAD');
                if (file_exists($headPath)) {
                    $headContent = trim(file_get_contents($headPath));
                    if (strpos($headContent, 'ref: ') === 0) {
                        $branchPath = base_path('.git/' . substr($headContent, 5));
                        $parts = explode('/', $headContent);
                        $branch = end($parts);

                        if (file_exists($branchPath)) {
                            $hash = substr(trim(file_get_contents($branchPath)), 0, 7);
                        }
                    } else {
                        // Detached HEAD
                        $branch = 'HEAD';
                        $hash = substr($headContent, 0, 7);
                    }
                }
            }

            if ($branch || $hash) {
                $version = ($branch ?: 'HEAD') . ' (' . ($hash ?: 'Unknown') . ')';
            }
        } catch (\Exception $e) {
            // Keep default
        }

        return $version;
    }
}
