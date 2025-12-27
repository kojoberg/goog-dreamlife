<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    // In a real app, this secret should be in .env and kept very private
    private $secret = 'dreamlife-secure-secret-key-2025';

    /**
     * Generate a license key for a given duration in months.
     */
    public function generateKey(int $months): string
    {
        $expiry = Carbon::now()->addMonths($months)->format('Y-m-d');
        $payload = base64_encode(json_encode(['expiry' => $expiry, 'salt' => \Str::random(8)]));
        $signature = hash_hmac('sha256', $payload, $this->secret);

        return "DL-{$payload}-{$signature}";
    }

    /**
     * Validate a license key and return expiry date or false.
     */
    public function validateKey(string $key)
    {
        if (!str_starts_with($key, 'DL-')) {
            return false;
        }

        $parts = explode('-', $key);
        if (count($parts) !== 3) {
            return false;
        }

        $payload = $parts[1];
        $signature = $parts[2];

        // Verify signature
        $expectedSignature = hash_hmac('sha256', $payload, $this->secret);
        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }

        // Decode payload
        $data = json_decode(base64_decode($payload), true);
        if (!isset($data['expiry'])) {
            return false;
        }

        return $data['expiry']; // Returns date string Y-m-d
    }

    /**
     * Check if the currently installed license is valid (not expired).
     */
    public function isLicenseActive($expiryDate): bool
    {
        if (!$expiryDate)
            return false;
        return Carbon::parse($expiryDate)->isFuture();
    }
}
