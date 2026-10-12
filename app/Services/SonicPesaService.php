<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client for the SonicPesa mobile-money gateway.
 *
 * @see https://sonicpesa.com  (API base: https://api.sonicpesa.com/api/v1)
 */
class SonicPesaService
{
    public function baseUrl(): string
    {
        return rtrim(config('services.sonicpesa.base_url', 'https://api.sonicpesa.com/api/v1'), '/');
    }

    public function currency(): string
    {
        return (string) config('services.sonicpesa.currency', 'TZS');
    }

    protected function headers(): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($key = config('services.sonicpesa.api_key')) {
            $headers['X-API-KEY'] = $key;
        }
        if ($secret = config('services.sonicpesa.api_secret')) {
            $headers['X-API-SECRET'] = $secret;
        }

        return $headers;
    }

    /**
     * Normalise a Tanzanian phone number to the 255XXXXXXXXX format.
     */
    public function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            return '255' . substr($cleaned, 1);
        }
        if (strlen($cleaned) === 9) {
            return '255' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Initiate a mobile-money collect (pay-in) request.
     *
     * @return array Decoded gateway response (includes `_http_status`).
     */
    public function createOrder(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(30)
                ->post($this->baseUrl() . '/payment/create_order', [
                    'buyer_email' => $data['buyer_email'] ?? 'noreply@medbando.app',
                    'buyer_name' => $data['buyer_name'] ?? 'Business Owner',
                    'buyer_phone' => $this->normalizePhone($data['phone']),
                    'amount' => (int) $data['amount'],
                    'currency' => $this->currency(),
                ]);

            $json = $response->json() ?: [];
            $json['_http_status'] = $response->status();

            return $json;
        } catch (\Throwable $e) {
            Log::error('SonicPesa createOrder exception: ' . $e->getMessage());

            return ['_error' => $e->getMessage()];
        }
    }

    /**
     * Poll the status of a previously created order.
     */
    public function orderStatus(string $orderId): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(20)
                ->post($this->baseUrl() . '/payment/order_status', ['order_id' => $orderId]);

            return $response->json() ?: [];
        } catch (\Throwable $e) {
            Log::error('SonicPesa orderStatus exception: ' . $e->getMessage());

            return [];
        }
    }

    public function extractOrderId(array $response): ?string
    {
        $id = $response['data']['order_id'] ?? $response['order_id'] ?? null;

        return $id !== null ? (string) $id : null;
    }

    /**
     * Extract the payment status string (uppercased) from a gateway response.
     */
    public function extractStatus(array $response): string
    {
        return strtoupper((string) (
            $response['data']['payment_status']
            ?? $response['data']['status']
            ?? $response['payment_status']
            ?? ''
        ));
    }

    /**
     * Verify the HMAC-SHA256 signature sent with a webhook.
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signature): bool
    {
        $secret = config('services.sonicpesa.api_secret');

        if (empty($secret)) {
            // Without a configured secret we cannot verify; accept but record it.
            Log::warning('SonicPesa webhook received but SONIC_PESA_API_SECRET is not configured; skipping verification.');

            return true;
        }

        if (empty($signature)) {
            return false;
        }

        $computed = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($computed, $signature);
    }
}
