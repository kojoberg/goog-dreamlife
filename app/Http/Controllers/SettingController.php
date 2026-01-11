<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Branch;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index()
    {
        $settings = Setting::firstOrCreate(
            ['id' => 1],
            [
                'business_name' => 'UVITECH Healthcare',
                'address' => '123 Health Street, City',
                'phone' => '+233 00 000 0000',
                'email' => 'info@uvitech.com',
                'currency_symbol' => 'GHS'
            ]
        );

        // Fetch Git Version
        // Fetch Git Version
        $systemVersion = $settings->current_version ?? 'v1.0';

        // Use the robust helper
        $gitVer = \App\Helpers\SystemHelper::getSystemVersion();
        if ($gitVer && $gitVer !== 'v1.0 (Static)') {
            $systemVersion = $gitVer;
        }

        // Get main branch for cashier workflow toggle
        $mainBranch = Branch::first();

        return view('settings.index', compact('settings', 'systemVersion', 'mainBranch'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'tin_number' => 'nullable|string|max:50', // New
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'currency_symbol' => 'required|string|max:10',
            'alert_expiry_days' => 'required|integer|min:1',
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|string',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_from_address' => 'nullable|email',
            'smtp_from_name' => 'nullable|string',
            'sms_api_key' => 'nullable|string',
            'sms_sender_id' => 'nullable|string',
            'loyalty_spend_per_point' => 'nullable|numeric|min:0',
            'loyalty_point_value' => 'nullable|numeric|min:0',
            'logo' => 'nullable|image|max:2048', // 2MB Max
            // New Preferences
            'enable_tax' => 'nullable|in:on,1,true', // HTML Checkbox sends 'on' or nothing
            'notify_low_stock_email' => 'nullable|in:on,1,true',
            'notify_low_stock_sms' => 'nullable|in:on,1,true',
            'notify_expiry_email' => 'nullable|in:on,1,true',
            'notify_expiry_sms' => 'nullable|in:on,1,true',
            'license_key' => 'nullable|string',
            'font_family' => 'nullable|string|in:Segoe UI,Inter,Roboto,Open Sans,Lato',
            'refund_policy_days' => 'nullable|integer|min:0', // New
            'refund_policy_text' => 'nullable|string|max:500', // New
        ]);

        $settings = Setting::first();

        // Handle File Upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $settings->logo_path = $path;
        }

        // Convert checkbox to boolean (if present = true, else false)
        $validated['enable_tax'] = $request->has('enable_tax');
        $validated['notify_low_stock_email'] = $request->has('notify_low_stock_email');
        $validated['notify_low_stock_sms'] = $request->has('notify_low_stock_sms');
        $validated['notify_expiry_email'] = $request->has('notify_expiry_email');
        $validated['notify_expiry_sms'] = $request->has('notify_expiry_sms');

        // Remove file from validated array to avoid overwrite error if not handled above
        // Remove file from validated array to avoid overwrite error if not handled above
        unset($validated['logo']);

        // Handle License Key separately to ensure validation
        if ($request->has('license_key')) {
            unset($validated['license_key']);
        }

        $settings->update($validated);

        // Update main branch cashier workflow setting (for single-branch mode)
        if (is_single_branch()) {
            $mainBranch = Branch::first();
            if ($mainBranch) {
                $mainBranch->update(['has_cashier' => $request->boolean('has_cashier')]);
            }
        }

        // Handle License Key Update
        if ($request->filled('license_key')) {
            // Only validate if it's different from current OR if current expiry is missing (re-validate)
            if ($request->license_key !== $settings->license_key || !$settings->license_expiry) {
                $licenseService = new \App\Services\LicenseService();
                $expiryDate = $licenseService->validateKey($request->license_key);

                if ($expiryDate) {
                    $settings->update([
                        'license_key' => $request->license_key,
                        'license_expiry' => $expiryDate
                    ]);
                    $message = "Settings updated. License validated successfully (expires: $expiryDate).";
                } else {
                    // Start flash error but don't stop other settings from saving? 
                    // Or Revert? For now, we just warn and DON'T save the key.
                    // But we already saved other settings.
                    return redirect()->route('settings.index')
                        ->with('error', 'Invalid License Key provided. Other settings saved.');
                }
            }
        } elseif ($request->has('license_key') && $request->license_key === null) {
            // User cleared the key
            $settings->update([
                'license_key' => null,
                'license_expiry' => null
            ]);
        }

        $message = $message ?? "Settings updated successfully.";

        // Test Email
        if ($request->has('test_email_recipient') && $request->test_email_recipient) {
            try {
                // Dynamically configure mailer
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $settings->smtp_host,
                    'mail.mailers.smtp.port' => $settings->smtp_port,
                    'mail.mailers.smtp.username' => $settings->smtp_username,
                    'mail.mailers.smtp.password' => $settings->smtp_password,
                    'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                    'mail.from.address' => $settings->smtp_from_address,
                    'mail.from.name' => $settings->smtp_from_name,
                ]);

                \Illuminate\Support\Facades\Mail::raw('This is a test email from UVITECH RxPMS.', function ($message) use ($request) {
                    $message->to($request->test_email_recipient)
                        ->subject('SMTP Configuration Test');
                });

                $message .= " Test Email sent.";
            } catch (\Exception $e) {
                return redirect()->route('settings.index')->with('error', 'Settings saved, but Email failed: ' . $e->getMessage());
            }
        }

        // Test SMS
        if ($request->has('test_sms_recipient') && $request->test_sms_recipient) {
            if ($settings->sms_api_key && $settings->sms_sender_id) {
                try {
                    $smsService = new \App\Services\SmsService();
                    $response = $smsService->sendQuickSms(
                        $request->test_sms_recipient,
                        "Test SMS from UVITECH RxPMS. System is operational."
                    );

                    if ($response['success']) {
                        $message .= " Test SMS sent.";
                    } else {
                        $message .= " Test SMS failed: " . $response['message'];
                    }
                } catch (\Exception $e) {
                    $message .= " Test SMS error: " . $e->getMessage();
                }
            } else {
                $message .= " Test SMS skipped (Missing API Key/Sender ID).";
            }
        }

        return redirect()->route('settings.index')->with('success', $message);
    }

    /**
     * Update the software via Git.
     */
    public function updateSoftware(Request $request)
    {
        try {
            // Limit to Admin
            if (!auth()->user()->isAdmin()) {
                abort(403);
            }

            // Check if exec/shell_exec are available
            if (!function_exists('exec') || !function_exists('shell_exec')) {
                $errorMsg = 'Server execution functions (exec/shell_exec) are disabled. Contact your hosting provider.';
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => $errorMsg], 500);
                }
                return back()->with('error', $errorMsg);
            }

            // Check if git is available
            $gitVersion = @shell_exec('git --version 2>&1');
            if (!$gitVersion || stripos($gitVersion, 'git version') === false) {
                $errorMsg = 'Git is not installed or accessible on the server.';
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => $errorMsg], 500);
                }
                return back()->with('error', $errorMsg);
            }

            $basePath = base_path();
            $log = [];

            // Get current branch
            $currentBranch = trim(@shell_exec("cd {$basePath} && git branch --show-current 2>/dev/null"));
            if (!$currentBranch) {
                $currentBranch = 'v4.0-multi'; // Default branch
            }

            // CHECK MODE - Just check for updates, don't apply
            if ($request->has('check')) {
                // Fetch latest from origin
                exec("cd {$basePath} && git fetch origin 2>&1", $fetchOutput, $fetchCode);

                if ($fetchCode !== 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to fetch from repository. Check your network connection.'
                    ], 500);
                }

                // Check for new commits
                $newCommits = @shell_exec("cd {$basePath} && git log HEAD..origin/{$currentBranch} --oneline 2>/dev/null");

                if (empty(trim($newCommits ?? ''))) {
                    return response()->json([
                        'status' => 'up_to_date',
                        'message' => 'System is already on the latest version.',
                        'branch' => $currentBranch
                    ]);
                } else {
                    return response()->json([
                        'status' => 'update_available',
                        'commits' => trim($newCommits),
                        'message' => 'New updates are available.',
                        'branch' => $currentBranch
                    ]);
                }
            }

            // PERFORM UPDATE MODE
            $log[] = "Starting update on branch: {$currentBranch}";

            // Step 1: Stash any local changes
            $log[] = "Step 1: Checking for local changes...";
            $stashOutput = @shell_exec("cd {$basePath} && git stash 2>&1");
            $hasStash = strpos($stashOutput, 'Saved working directory') !== false;
            if ($hasStash) {
                $log[] = "  → Local changes stashed";
            } else {
                $log[] = "  → No local changes to stash";
            }

            // Step 2: Pull from origin
            $log[] = "Step 2: Pulling latest changes from GitHub...";
            exec("cd {$basePath} && git pull origin {$currentBranch} 2>&1", $pullOutput, $pullCode);

            if ($pullCode !== 0) {
                // Try to restore stash before returning error
                if ($hasStash) {
                    @shell_exec("cd {$basePath} && git stash pop 2>&1");
                }
                $errorLog = implode("\n", $pullOutput);
                return back()->with('error', "Git pull failed:\n{$errorLog}");
            }
            $log[] = "  → " . (implode(", ", array_slice($pullOutput, 0, 3)) ?: "Already up to date");

            // Step 3: Pop stashed changes (if any)
            if ($hasStash) {
                $log[] = "Step 3: Restoring local changes...";
                @shell_exec("cd {$basePath} && git stash pop 2>&1");
                $log[] = "  → Local changes restored";
            }

            // Step 4: Run Migrations
            $log[] = "Step 4: Running database migrations...";
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                $log[] = "  → Migrations completed";
            } catch (\Exception $e) {
                $log[] = "  → Migration warning: " . $e->getMessage();
            }

            // Step 5: Composer install (if accessible)
            $log[] = "Step 5: Checking dependencies...";
            $composerPath = trim(@shell_exec('which composer 2>/dev/null') ?? '');
            if ($composerPath && file_exists($basePath . '/composer.lock')) {
                exec("cd {$basePath} && {$composerPath} install --no-dev --optimize-autoloader --no-interaction 2>&1", $compOutput, $compCode);
                if ($compCode === 0) {
                    $log[] = "  → Composer dependencies updated";
                } else {
                    $log[] = "  → Composer skipped (may need manual run)";
                }
            } else {
                $log[] = "  → Composer not available, skipping";
            }

            // Step 6: Clear all caches
            $log[] = "Step 6: Clearing system caches...";
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            $log[] = "  → All caches cleared";

            // Step 7: Optimize for production
            $log[] = "Step 7: Optimizing application...";
            try {
                \Illuminate\Support\Facades\Artisan::call('config:cache');
                \Illuminate\Support\Facades\Artisan::call('route:cache');
                $log[] = "  → Application optimized";
            } catch (\Exception $e) {
                $log[] = "  → Optimization skipped: " . $e->getMessage();
            }

            $log[] = "";
            $log[] = "✓ Update completed successfully!";

            return back()->with('success', implode("\n", $log));

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Update Error: ' . $e->getMessage());
        }
    }
}

