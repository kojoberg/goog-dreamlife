<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    // Public Key for Verification (Production Grade)
    private $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkpv4Zmfktnh6eE4z8W41
lAWPf2xynbOpHMqnmiDjPiiZFY73+NwM7AX6tQWzeH+HvdBXDPVQfTP2Ra16sSTq
1VYCzm9y0hvMdDhgYOpuTGT7elzYvMmxO+5keEj1laJFvvzfRGoKIoxTkMexQT1q
jHwYirJANq2YnAuUjJ5xpJoSCFC0kSSMXZ4pnUZtyKEWBENiQVh+GumEYv2Yjhv0
c+33K2kj55UxFIvyRVHqYQqIpPSwhigR+rc9xdAzD/XducGwtaRqQAo6WuwOfH5K
qCipZZ8okCDp/2QFMa4Ab9ah/jr6+886JPMjE/cfEt/Agr6iGDji9WOjuZ2XM6r7
KwIDAQAB
-----END PUBLIC KEY-----
EOD;

    /**
     * Validate a license key and return expiry date or false.
     * key format: UVITECH-{Base64Payload}-{Base64Signature}
     */
    public function validateKey(string $key)
    {
        if (!str_starts_with($key, 'UVITECH-')) {
            return false;
        }

        $parts = explode('-', $key);
        if (count($parts) !== 3) {
            return false;
        }

        $payload = $parts[1];
        $signature = base64_decode($parts[2]);

        // 1. Verify Signature
        $result = openssl_verify($payload, $signature, $this->publicKey, OPENSSL_ALGO_SHA256);

        if ($result !== 1) {
            return false; // Invalid or error
        }

        // 2. Decode and Check Expiry
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
