<?php

namespace App\Http\Controllers;

use App\Models\Setting;
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
        $systemVersion = $settings->current_version ?? 'v1.0';
        try {
            // Attempt 1: exec()
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
                $systemVersion = ($branch ?: 'HEAD') . ' (' . ($hash ?: 'Unknown') . ')';
            }
        } catch (\Exception $e) {
            // Keep default
        }

        return view('settings.index', compact('settings', 'systemVersion'));
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
            // Check if git is available
            $gitVersion = shell_exec('git --version');
            if (!$gitVersion) {
                if ($request->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => 'Git is not installed.'], 500);
                }
                return back()->with('error', 'Git is not installed or accessible on the server.');
            }

            // Limit to Admin
            if (!auth()->user()->isAdmin()) {
                abort(403);
            }

            $basePath = base_path();
            $param = '2>&1';

            // CHECK MODE
            if ($request->has('check')) {
                // 1. Fetch
                exec("cd {$basePath} && git fetch origin {$param}", $output, $returnCode);

                // 2. Check for new commits
                // Get current branch
                $currentBranch = trim(shell_exec("cd {$basePath} && git branch --show-current 2>/dev/null"));
                if (!$currentBranch)
                    $currentBranch = 'main';

                $logCmd = "cd {$basePath} && git log HEAD..origin/{$currentBranch} --oneline";
                $newCommits = shell_exec($logCmd);

                if (empty(trim($newCommits))) {
                    return response()->json([
                        'status' => 'up_to_date',
                        'message' => 'System is already on the latest version.'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'update_available',
                        'commits' => $newCommits,
                        'message' => 'New updates are available.'
                    ]);
                }
            }

            // PERFORM UPDATE MODE
            // Go to root, pull changes
            $command = "cd {$basePath} && git pull origin main {$param}";

            // Detect branch
            $currentBranch = trim(shell_exec("cd {$basePath} && git branch --show-current 2>/dev/null"));
            if ($currentBranch) {
                $command = "cd {$basePath} && git pull origin {$currentBranch} {$param}";
            }

            exec($command, $output, $returnCode);

            $log = implode("\n", $output);

            if ($returnCode !== 0) {
                return back()->with('error', "Update Failed:\n" . $log);
            }

            // Re-optimize
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');

            return back()->with('success', "System Updated Successfully.\nLog:\n" . $log);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Update Error: ' . $e->getMessage());
        }
    }
}
