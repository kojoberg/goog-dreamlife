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
                'business_name' => 'Dream Life Healthcare',
                'address' => '123 Health Street, City',
                'phone' => '+233 00 000 0000',
                'email' => 'info@dreamlife.com',
                'currency_symbol' => 'GHS'
            ]
        );

        // Fetch Git Version
        $systemVersion = $settings->current_version ?? 'v1.0';
        try {
            $branch = trim(exec('git branch --show-current'));
            $hash = trim(exec('git rev-parse --short HEAD'));

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
        unset($validated['logo']);

        $settings->update($validated);

        $message = "Settings updated successfully.";

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

                \Illuminate\Support\Facades\Mail::raw('This is a test email from Dream Life PMS.', function ($message) use ($request) {
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
                        "Test SMS from Dream Life PMS. System is operational."
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
}
