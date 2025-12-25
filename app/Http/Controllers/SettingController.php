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

        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'currency_symbol' => 'required|string|max:10',
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|string',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_from_address' => 'nullable|email',
            'smtp_from_name' => 'nullable|string',
            'sms_api_key' => 'nullable|string',
            'sms_sender_id' => 'nullable|string',
            'loyalty_spend_per_point' => 'required|numeric|min:0',
            'loyalty_point_value' => 'required|numeric|min:0',
        ]);

        $settings = Setting::first();
        $settings->update($validated);

        $message = "Settings updated successfully.";

        // Test Email
        if ($request->has('test_email_recipient') && $request->test_email_recipient) {
            try {
                // Dynamically configure mailer
                config([
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
