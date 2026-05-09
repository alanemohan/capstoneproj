<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsOtpService
{
    public function send(string $phone, string $otp): bool
    {
        $provider = config('services.otp.provider', 'log');

        if ($provider === 'log') {
            Log::info("[OTP:LOG_PROVIDER] OTP {$otp} sent to {$phone}");
            return true;
        }

        $endpoint = config('services.otp.endpoint');
        if (empty($endpoint)) {
            Log::error('[OTP] SMS endpoint is not configured.');
            return false;
        }

        $messageTemplate = (string) config('services.otp.message_template', 'Your OTP is :otp. It will expire in 5 minutes.');
        $message = str_replace(':otp', $otp, $messageTemplate);

        $headers = [
            'Accept' => 'application/json',
        ];

        $apiKey = config('services.otp.api_key');
        if (!empty($apiKey)) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        $payload = [
            'to' => $phone,
            'message' => $message,
            'sender' => config('services.otp.sender', 'NABHA'),
        ];

        $response = Http::timeout((int) config('services.otp.timeout', 8))
            ->withHeaders($headers)
            ->post($endpoint, $payload);

        if (!$response->successful()) {
            Log::error('[OTP] SMS provider call failed.', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}
