<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $baseUrl = 'https://uellosend.com/quicksend/';
    protected $apiKey;
    protected $senderId;

    public function __construct()
    {
        $settings = Setting::first();
        $this->apiKey = $settings->sms_api_key;
        $this->senderId = $settings->sms_sender_id;
    }

    /**
     * Send a quick SMS to a single recipient.
     *
     * @param string $recipient
     * @param string $message
     * @return array
     */
    public function sendQuickSms($recipient, $message)
    {
        if (!$this->apiKey || !$this->senderId) {
            return ['success' => false, 'message' => 'SMS credentials not configured.'];
        }

        try {
            $response = Http::post($this->baseUrl, [
                'api_key' => $this->apiKey,
                'sender_id' => $this->senderId,
                'recipient' => $recipient,
                'message' => $message,
            ]);

            $data = $response->json();

            Log::info("SMS Response: " . json_encode($data));

            if (isset($data['status']) && $data['status'] === 'Success') {
                return ['success' => true, 'data' => $data];
            }

            return ['success' => false, 'message' => $data['msg'] ?? 'Unknown error'];

        } catch (\Exception $e) {
            Log::error("SMS Service Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
