<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Mock Laravel App for Http Facade (Simplified, or just use Guzzle directly since facade needs app boot)
// Actually, easier to just use standard PHP curl or Guzzle if installed.
// The project has 'guzzlehttp/guzzle' in composer.json (part of Laravel).

$client = new \GuzzleHttp\Client();

$url = 'https://uellosend.com/quicksend/';
$apiKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.=eyJ1c2VyX2lkIjo5LCJhcGlTZWNyZXQiOiJHPW5LMTdReGJpPUI2TEEiLCJleHAiOjIwMjAxMH0';
$senderId = 'UVITECH';
$recipient = '0240000000'; // Dummy number
$message = 'Debug Test Message';

echo "Testing JSON Payload...\n";
try {
    $response = $client->post($url, [
        'json' => [
            'api_key' => $apiKey,
            'sender_id' => $senderId,
            'recipient' => $recipient,
            'message' => $message,
        ],
        'http_errors' => false
    ]);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Body: " . $response->getBody() . "\n";
} catch (\Exception $e) {
    echo "JSON Error: " . $e->getMessage() . "\n";
}

echo "\n----------------\n";
echo "Testing Form Params Payload...\n";
try {
    $response = $client->post($url, [
        'form_params' => [
            'api_key' => $apiKey,
            'sender_id' => $senderId,
            'recipient' => $recipient,
            'message' => $message,
        ],
        'http_errors' => false
    ]);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Body: " . $response->getBody() . "\n";
} catch (\Exception $e) {
    echo "Form Error: " . $e->getMessage() . "\n";
}
