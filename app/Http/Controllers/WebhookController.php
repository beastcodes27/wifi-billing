<?php

namespace App\Http\Controllers;

use App\Models\SaasSubscription;
use App\Models\Transaction;
use App\Services\HotspotProvisioner;
use App\Services\SonicPesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * SonicPesa webhook.
     *
     * Handles two kinds of pay-in:
     *  - SaaS subscription payments (tenant monthly fee)  -> reference_number
     *  - Guest hotspot payments                           -> gateway_transaction_id
     */
    public function sonicPesa(Request $request, HotspotProvisioner $provisioner)
    {
        $raw = $request->getContent();
        $signature = $request->header('X-SonicPesa-Signature') ?? $request->header('X-Webhook-Signature');

        $service = app(SonicPesaService::class);

        if (! $service->verifyWebhookSignature($raw, $signature)) {
            Log::warning('[PAYMENT] SonicPesa webhook signature verification failed', ['signature' => $signature]);

            return response()->json(['status' => 'invalid_signature'], 401);
        }

        $payload = $request->json()->all() ?: (json_decode($raw, true) ?: []);
        Log::info('[PAYMENT] SonicPesa Webhook Received', $payload);

        $event = $payload['event'] ?? null;
        $orderId = $payload['order_id'] ?? ($payload['data']['order_id'] ?? null);
        $status = strtoupper((string) ($payload['payment_status'] ?? $payload['status'] ?? ''));

        if (! $orderId) {
            return response()->json(['status' => 'ignored', 'message' => 'Missing order_id'], 200);
        }

        $isSuccess = in_array($status, ['COMPLETED', 'SUCCESS'], true) || $event === 'payment.success';
        $isFailure = in_array($status, ['FAILED', 'CANCELLED', 'USERCANCELLED', 'REJECTED', 'EXPIRED'], true) || $event === 'payment.failed';

        // 1) SaaS subscription (tenant monthly fee)
        $subscription = SaasSubscription::where('reference_number', $orderId)->first();
        if ($subscription) {
            if ($subscription->status === 'active') {
                return response()->json(['status' => 'already_processed'], 200);
            }
            if ($isSuccess) {
                $subscription->activate();

                return response()->json(['status' => 'success', 'message' => 'Subscription activated'], 200);
            }
            if ($isFailure) {
                $subscription->markFailed();

                return response()->json(['status' => 'failed'], 200);
            }

            return response()->json(['status' => 'pending'], 200);
        }

        // 2) Guest hotspot transaction
        $transaction = Transaction::withoutGlobalScopes()
            ->where(function ($q) use ($orderId) {
                $q->where('gateway_transaction_id', $orderId)
                  ->orWhere('palm_pesa_order_id', $orderId);
            })
            ->first();

        if ($transaction) {
            if ($transaction->status === 'success') {
                return response()->json(['status' => 'already_processed'], 200);
            }
            if ($isSuccess) {
                $provisioner->provision($transaction);

                return response()->json(['status' => 'success', 'message' => 'Voucher provisioned'], 200);
            }
            if ($isFailure) {
                $transaction->update(['status' => 'failed']);

                return response()->json(['status' => 'failed'], 200);
            }

            return response()->json(['status' => 'pending'], 200);
        }

        return response()->json(['status' => 'ignored', 'message' => 'Not found'], 200);
    }
}
