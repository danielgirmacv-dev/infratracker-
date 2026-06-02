<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareTurnstile
{
    public function isConfigured(): bool
    {
        return (bool) config('services.turnstile.site_key')
            && (bool) config('services.turnstile.secret_key');
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        if (!$token) {
            return false;
        }

        if (config('services.turnstile.skip_verify')) {
            return true;
        }

        $secret = config('services.turnstile.secret_key');
        if (!$secret) {
            return false;
        }

        $payload = [
            'secret' => $secret,
            'response' => $token,
        ];

        if ($ip) {
            $payload['remoteip'] = $ip;
        }

        try {
            $response = Http::asForm()
                ->connectTimeout(5)
                ->timeout(15)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $payload);

            if (!$response->successful()) {
                return false;
            }

            return $response->json('success') === true;
        } catch (ConnectionException $e) {
            if ($this->shouldBypassVerifyOnFailure()) {
                Log::warning('Turnstile siteverify unreachable; allowing login in local dev.', [
                    'error' => $e->getMessage(),
                ]);

                return true;
            }

            Log::error('Turnstile siteverify failed.', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Local dev fallback when outbound HTTPS to Cloudflare is blocked or times out.
     */
    private function shouldBypassVerifyOnFailure(): bool
    {
        if (!app()->environment('local')) {
            return false;
        }

        return config('services.turnstile.skip_verify')
            || config('services.turnstile.use_test_keys');
    }
}
