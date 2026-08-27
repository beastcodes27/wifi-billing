<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FimiPayService
{
    /**
     * Resolve Base URL for FimiPay API.
     */
    public function getBaseUrl(?Tenant $tenant = null): string
    {
        $tenantUrl = $tenant->settings['fimipay']['base_url'] ?? null;
        $url = $tenantUrl ?: config('services.fimipay.base_url', env('FIMIPAY_BASE_URL', 'https://fimipay.com/api/v1'));
        return rtrim($url, '/');
    }

    /**
     * Resolve Secret Key.
     */
    public function getSecretKey(?Tenant $tenant = null): string
    {
        $tenantKey = $tenant->settings['fimipay']['secret_key'] ?? null;

        return (string) ($tenantKey ?: config('services.fimipay.secret_key', env('FIMIPAY_SECRET_KEY', '')));
    }

    /**
     * Resolve Public ID.
     */
    public function getPublicId(?Tenant $tenant = null): string
    {
        $tenantId = $tenant->settings['fimipay']['public_id'] ?? null;

        return (string) ($tenantId ?: config('services.fimipay.public_id', env('FIMIPAY_PUBLIC_ID', '')));
    }

    /**
     * Format phone number to 255XXXXXXXXX format.
     */
    public function formatPhone(string $phone): string
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
     * Create a payment order via FimiPay.
     *
     * @param string $phone
     * @param float|int $amount
     * @param string $reference
     * @param array $metadata
     * @param string $name
     * @param string|null $email
     * @param Tenant|null $tenant
     * @return array
     */
    public function createOrder(
        string $phone,
        float|int $amount,
        string $reference,
        array $metadata = [],
        string $name = 'Mteja WiFi',
        ?string $email = null,
        ?Tenant $tenant = null
    ): array {
        $formattedPhone = $this->formatPhone($phone);
        $baseUrl = $this->getBaseUrl($tenant);
        $secretKey = $this->getSecretKey($tenant);
        $isTestMode = config('services.fimipay.environment', env('FIMIPAY_ENVIRONMENT')) === 'test'
            || str_starts_with($secretKey, 'sk_test_');

        $email = $email ?: "mteja{$formattedPhone}@sayuni.co.tz";

        // Callback URL
        $callbackUrl = route('portal.fimipay.webhook');
        if (str_contains(request()->getHost(), 'localhost') || str_contains(request()->getHost(), '127.0.0.1')) {
            // If running on local machine without public tunnel, fallback to production endpoint
            $callbackUrl = 'https://drinkpoint.store/api/fimipay-webhook';
        }

        $payload = [
            'buyer_name' => $name,
            'buyer_email' => $email,
            'buyer_phone' => $formattedPhone,
            'amount' => (int) round($amount),
            'currency' => 'TZS',
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'metadata' => $metadata,
        ];

        if ($isTestMode) {
            $payload['test_outcome'] = 'success';
        }

        try {
            Log::info("FimiPay create_order sending for ref {$reference}", [
                'phone' => $formattedPhone,
                'amount' => $amount,
                'url' => $baseUrl . '/payment/create_order',
            ]);

            $res = $this->sendCurlRequest($baseUrl . '/payment/create_order', $payload, $secretKey, 25);
            $respData = $res['data'] ?? [];

            $isSuccessful = $res['success'] && !empty($respData['data']['order_id']);
            $orderId = $isSuccessful ? $respData['data']['order_id'] : ($respData['order_id'] ?? null);

            if ($isSuccessful && $orderId) {
                Log::info("FimiPay create_order success", ['order_id' => $orderId, 'ref' => $reference]);
                return [
                    'success' => true,
                    'order_id' => $orderId,
                    'data' => $respData,
                    'message' => 'Order created successfully',
                ];
            }

            $errorMsg = $respData['message'] ?? $respData['error'] ?? $res['error'] ?? 'Imeshindwa kuanzisha malipo ya FimiPay.';
            Log::error("FimiPay create_order failed", [
                'status' => $res['status'],
                'response' => $respData,
            ]);

            return [
                'success' => false,
                'order_id' => null,
                'data' => $respData,
                'message' => $errorMsg,
            ];
        } catch (\Throwable $e) {
            Log::error('FimiPay Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'order_id' => null,
                'data' => [],
                'message' => 'Hitilafu ya malipo: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check Order Status from FimiPay.
     *
     * @param string $orderId
     * @param Tenant|null $tenant
     * @return array
     */
    public function getOrderStatus(string $orderId, ?Tenant $tenant = null): array
    {
        $baseUrl = $this->getBaseUrl($tenant);
        $secretKey = $this->getSecretKey($tenant);

        try {
            $res = $this->sendCurlRequest($baseUrl . '/payment/order_status', ['order_id' => $orderId], $secretKey, 15);
            $resData = $res['data'] ?? [];

            if ($res['success']) {
                // In FimiPay API:
                // Top-level $resData['status'] is the API HTTP envelope ("success" means HTTP request succeeded, NOT that payment is completed).
                // The actual payment status is strictly in resData['data']['payment_status'], resData['data']['order_status'], or resData['data']['status'].
                $rawPaymentStatus = null;
                if (isset($resData['data']) && is_array($resData['data'])) {
                    $rawPaymentStatus = $resData['data']['payment_status']
                        ?? $resData['data']['order_status']
                        ?? (isset($resData['data']['status']) && !in_array(strtolower((string)$resData['data']['status']), ['success', 'ok']) ? $resData['data']['status'] : null);
                }
                if (!$rawPaymentStatus && isset($resData['payment_status'])) {
                    $rawPaymentStatus = $resData['payment_status'];
                }
                if (!$rawPaymentStatus && isset($resData['order_status'])) {
                    $rawPaymentStatus = $resData['order_status'];
                }

                $statusUpper = strtoupper((string) ($rawPaymentStatus ?? 'PENDING'));

                $validSuccessStatuses = ['COMPLETED', 'PAID', 'SETTLED', 'SUCCESSFUL', 'COMPLETE'];
                $validFailedStatuses = ['FAILED', 'CANCELLED', 'EXPIRED', 'REJECTED'];

                $isVerifiedPaid = in_array($statusUpper, $validSuccessStatuses, true);
                $isFailed = in_array($statusUpper, $validFailedStatuses, true);

                $normalizedStatus = $isVerifiedPaid ? 'SUCCESS' : ($isFailed ? 'FAILED' : 'PENDING');

                Log::info("[PAYMENT] FimiPay getOrderStatus verification", [
                    'order_id' => $orderId,
                    'raw_payment_status' => $statusUpper,
                    'verified_status' => $normalizedStatus,
                    'is_paid' => $isVerifiedPaid,
                ]);

                return [
                    'success' => true,
                    'is_paid' => $isVerifiedPaid,
                    'status' => $normalizedStatus,
                    'payment_status' => $statusUpper,
                    'data' => $resData,
                ];
            }

            Log::warning("[PAYMENT] FimiPay order_status HTTP call failed or non-200", [
                'order_id' => $orderId,
                'http_status' => $res['status'],
                'data' => $resData,
            ]);

            return [
                'success' => false,
                'is_paid' => false,
                'status' => 'PENDING',
                'payment_status' => 'PENDING',
                'data' => $resData,
            ];
        } catch (\Throwable $e) {
            Log::error('[PAYMENT] FimiPay order_status Exception: ' . $e->getMessage(), [
                'order_id' => $orderId,
            ]);
            return [
                'success' => false,
                'is_paid' => false,
                'status' => 'PENDING',
                'payment_status' => 'PENDING',
                'data' => [],
            ];
        }
    }

    /**
     * Synchronous cURL execution.
     * Prevents Windows OpenSSL 3 async socket error:0A0003FC:SSL routines::sslv3 alert bad record mac.
     */
    protected function sendCurlRequest(string $endpoint, array $payload, string $secretKey, int $timeout = 25): array
    {
        $jsonPayload = json_encode($payload);
        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $secretKey,
            'User-Agent: WiFiBilling-FimiPay/1.0',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        if ($curlErrno !== 0) {
            Log::error("FimiPay cURL Error ({$curlErrno}): {$curlError} for {$endpoint}");
            return [
                'success' => false,
                'status' => $httpCode,
                'data' => null,
                'error' => "Mtandao wa FimiPay unachukua muda mrefu kujibu. Tafadhali jaribu tena.",
            ];
        }

        $decoded = json_decode($response, true);
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'status' => $httpCode,
            'data' => $decoded,
            'raw' => $response,
            'error' => $decoded['message'] ?? $decoded['error'] ?? null,
        ];
    }
}
