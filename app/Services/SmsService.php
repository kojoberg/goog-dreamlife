<?php

namespace App\Services;

use App\Models\CommunicationLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $baseUrl = 'https://uellosend.com/quicksend/';
    protected $balanceUrl = 'https://uellosend.com/balance/';
    protected $apiKey;
    protected $senderId;

    const BALANCE_CACHE_KEY = 'sms_balance';
    const BALANCE_CACHE_TTL = 300; // 5 minutes

    public function __construct()
    {
        $settings = Setting::first();
        if ($settings) {
            $this->apiKey = $settings->sms_api_key;
            $this->senderId = $settings->sms_sender_id;
        }
    }

    /**
     * Get the SMS balance from UelloSend.
     *
     * @param bool $forceRefresh Force a fresh API call instead of using cache
     * @return array
     */
    public function getBalance($forceRefresh = false)
    {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'API key not configured', 'balance' => null];
        }

        // Return cached balance if available and not forcing refresh
        if (!$forceRefresh && Cache::has(self::BALANCE_CACHE_KEY)) {
            return Cache::get(self::BALANCE_CACHE_KEY);
        }

        try {
            $response = Http::timeout(5)->post($this->balanceUrl, [
                'api_key' => $this->apiKey,
            ]);

            $data = $response->json();

            Log::info("SMS Balance Response: " . json_encode($data));

            if (isset($data['status']) && $data['status'] === 'Success') {
                // Parse balance from desc (format: "balance: 2.7200")
                $balance = null;
                if (isset($data['desc']) && preg_match('/balance:\s*([\d.]+)/i', $data['desc'], $matches)) {
                    $balance = floatval($matches[1]);
                }

                $result = [
                    'success' => true,
                    'balance' => $balance,
                    'message' => $data['desc'] ?? 'Balance retrieved',
                ];

                // Cache the result
                Cache::put(self::BALANCE_CACHE_KEY, $result, self::BALANCE_CACHE_TTL);

                return $result;
            }

            $errorMessage = $data['msg'] ?? ($data['desc'] ?? 'Failed to retrieve balance');
            return ['success' => false, 'message' => $errorMessage, 'balance' => null];

        } catch (\Exception $e) {
            Log::error("SMS Balance Check Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'balance' => null];
        }
    }

    /**
     * Clear the cached balance (call after SMS activity).
     */
    public function clearBalanceCache()
    {
        Cache::forget(self::BALANCE_CACHE_KEY);
    }

    /**
     * Log a communication attempt.
     *
     * @param string $recipient
     * @param string $message
     * @param string $status
     * @param string|null $response
     * @param string|null $context
     */
    protected function logCommunication($recipient, $message, $status, $response = null, $context = null)
    {
        try {
            CommunicationLog::create([
                'type' => 'sms',
                'recipient' => $recipient,
                'message' => $message,
                'status' => $status,
                'response' => $response,
                'context' => $context,
                'user_id' => auth()->id(),
                'branch_id' => auth()->user()?->branch_id,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log SMS communication: " . $e->getMessage());
        }
    }

    /**
     * Send a quick SMS to a single recipient.
     *
     * @param string $recipient
     * @param string $message
     * @param string|null $context Optional context for logging (e.g., "prescription_reminder")
     * @return array
     */
    public function sendQuickSms($recipient, $message, $context = null)
    {
        if (!$this->apiKey || !$this->senderId) {
            $this->logCommunication($recipient, $message, 'failed', 'SMS credentials not configured', $context);
            return ['success' => false, 'message' => 'SMS credentials not configured.'];
        }

        try {
            $fullMessage = $message . "\n\nSoftware powered by UviTech, Inc.";

            $response = Http::post($this->baseUrl, [
                'api_key' => $this->apiKey,
                'sender_id' => $this->senderId,
                'recipient' => $recipient,
                'message' => $fullMessage,
            ]);

            $data = $response->json();
            $responseJson = json_encode($data);

            Log::info("SMS Response: " . $responseJson);

            if (isset($data['status']) && $data['status'] === 'Success') {
                // Log successful send
                $this->logCommunication($recipient, $message, 'sent', $responseJson, $context);

                // Clear cached balance after successful SMS send
                $this->clearBalanceCache();
                return ['success' => true, 'data' => $data];
            }

            // Check 'msg' (some endpoints) or 'desc' (others)
            $errorMessage = $data['msg'] ?? ($data['desc'] ?? 'Unknown error');

            // Log failed send
            $this->logCommunication($recipient, $message, 'failed', $responseJson, $context);

            return ['success' => false, 'message' => $errorMessage];

        } catch (\Exception $e) {
            Log::error("SMS Service Error: " . $e->getMessage());

            // Log exception
            $this->logCommunication($recipient, $message, 'failed', $e->getMessage(), $context);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

