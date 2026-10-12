<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\WifiUser;
use App\Services\MikrotikService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        $this->mikrotikService = $mikrotikService;
    }

    public function palmPesa(Request $request)
    {
        $orderId = $request->input('order_id');
        $paymentStatus = $request->input('payment_status');

        Log::info('Palm Pesa Webhook Received:', $request->all());

        if (!$orderId || !$paymentStatus) {
            return response()->json(['message' => 'Invalid webhook payload'], 400);
        }

        // Check if it's a SaaS Subscription payment
        $saasSubscription = \App\Models\SaasSubscription::where('reference_number', $orderId)->first();
        if ($saasSubscription) {
            if ($saasSubscription->status === 'active') {
                return response()->json(['message' => 'Already processed SaaS'], 200);
            }

            if ($paymentStatus === 'COMPLETED') {
                $saasSubscription->status = 'active';
                $saasSubscription->starts_at = now();
                $saasSubscription->ends_at = now()->addMonths($saasSubscription->plan->billing_cycle_months);
                $saasSubscription->save();

                return response()->json(['message' => 'SaaS Subscription activated'], 200);
            }

            $saasSubscription->status = 'failed';
            $saasSubscription->save();
            return response()->json(['message' => 'SaaS Subscription failed'], 200);
        }

        // If not SaaS, check if it's a Hotspot Voucher transaction
        $transaction = Transaction::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('palm_pesa_order_id', $orderId)
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->status === 'success') {
            return response()->json(['message' => 'Already processed'], 200);
        }

        if ($paymentStatus === 'COMPLETED') {
            $transaction->status = 'success';
            
            // Set Tenant Context so created models get the correct tenant_id
            $tenant = \App\Models\Tenant::find($transaction->tenant_id);
            if ($tenant) {
                app(\App\Services\TenantContext::class)->setTenant($tenant);
            }
            
            // Link to Customer if exists
            $customer = \App\Models\Customer::where('phone_number', $transaction->phone_number)->first();
            if ($customer) {
                $transaction->customer_id = $customer->id;
            }

            // Generate Mikrotik Voucher (5 random digits)
            $package = $transaction->package;
            $voucherCode = str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            $password = $voucherCode; 

            // Create WifiUser record
            $wifiUser = WifiUser::create([
                'customer_id' => $customer ? $customer->id : null,
                'phone_number' => $transaction->phone_number,
                'mikrotik_username' => $voucherCode,
                'mikrotik_password' => $password,
                'package_id' => $package->id,
                'payment_type' => 'palm_pesa',
                'expires_at' => now()->addMinutes($package->duration_minutes)
            ]);

            // Create Subscription
            \App\Models\Subscription::create([
                'customer_id' => $customer ? $customer->id : null,
                'wifi_user_id' => $wifiUser->id,
                'package_id' => $package->id,
                'transaction_id' => $transaction->id,
                'type' => 'new',
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addMinutes($package->duration_minutes),
                'notes' => 'Palm Pesa Auto-Provision'
            ]);

            $transaction->wifi_user_id = $wifiUser->id;
            $transaction->mikrotik_voucher = $voucherCode;
            // provisioned_at remains NULL until Mikrotik router pulls and confirms via /api/mikrotik/done
            $transaction->save();

            Log::info('Voucher and Subscription generated for: ' . $orderId);

        } elseif ($paymentStatus === 'FAILED') {
            $transaction->status = 'failed';
            $transaction->save();
        }

        return response()->json(['message' => 'Webhook received successfully'], 200);
    }

    public function fimiPay(Request $request)
    {
        $orderId = $request->input('order_id') ?? $request->input('data.order_id');
        $reference = $request->input('reference') ?? $request->input('data.reference');
        $rawStatus = $request->input('payment_status') 
            ?? $request->input('data.payment_status') 
            ?? $request->input('event')
            ?? $request->input('data.order_status');

        // Only fall back to status key if it's not the generic HTTP envelope 'success'/'error'
        if (!$rawStatus && $request->has('status') && !in_array(strtolower((string) $request->input('status')), ['success', 'error', 'ok'])) {
            $rawStatus = $request->input('status');
        }

        $status = strtoupper((string) ($rawStatus ?? ''));

        Log::info('[PAYMENT] FimiPay Webhook Received', [
            'order_id' => $orderId,
            'reference' => $reference,
            'status' => $status,
        ]);

        if (!$orderId && !$reference) {
            return response()->json(['status' => 'error', 'message' => 'Missing order_id or reference'], 400);
        }

        // Find transaction by order_id or transaction_reference
        $transaction = Transaction::withoutGlobalScopes()
            ->where(function ($q) use ($orderId, $reference) {
                if ($orderId) {
                    $q->where('gateway_transaction_id', $orderId)
                      ->orWhere('palm_pesa_order_id', $orderId);
                }
                if ($reference) {
                    $q->orWhere('transaction_reference', $reference);
                }
            })
            ->first();

        if (!$transaction) {
            Log::warning("[PAYMENT] FimiPay Webhook: Transaction not found for orderId: {$orderId}, reference: {$reference}");
            return response()->json(['status' => 'ignored', 'message' => 'Transaction not found'], 200);
        }

        if ($transaction->status === 'success') {
            return response()->json(['status' => 'already_processed'], 200);
        }

        if (in_array($status, ['SUCCESS', 'COMPLETED', 'PAID', 'COMPLETE', 'SUCCESSFUL', 'SETTLED', 'PAYMENT.SUCCESS', 'ORDER.PAID'])) {
            $transaction->status = 'success';
            $transaction->payment_gateway = 'fimipay';
            $transaction->payment_method = 'fimipay';
            
            // Set Tenant Context
            $tenantId = $transaction->tenant_id ?: 1;
            $tenant = \App\Models\Tenant::find($tenantId);
            if ($tenant) {
                app(\App\Services\TenantContext::class)->setTenant($tenant);
            }

            // Only generate voucher & wifi_user if not already created (idempotent)
            if (empty($transaction->mikrotik_voucher)) {
                // Link or auto-create customer record
                $customer = \App\Models\Customer::withoutGlobalScopes()->where('phone_number', $transaction->phone_number)->first();
                if (!$customer && !empty($transaction->phone_number)) {
                    $customer = \App\Models\Customer::create([
                        'tenant_id' => $tenantId,
                        'phone_number' => $transaction->phone_number,
                        'name' => 'Mteja ' . substr($transaction->phone_number, -4),
                        'status' => 'active',
                    ]);
                }
                if ($customer) {
                    $transaction->customer_id = $customer->id;
                }

                // Retrieve MAC and IP from transaction notes
                $deviceNotes = [];
                if (!empty($transaction->notes)) {
                    $deviceNotes = json_decode($transaction->notes, true) ?: [];
                }

                // Generate Mikrotik Voucher (6 random digits)
                $package = $transaction->package;
                $voucherCode = (string) mt_rand(100000, 999999);
                $password = $voucherCode; 

                // Create WifiUser record with device MAC & IP
                $wifiUser = WifiUser::create([
                    'tenant_id' => $tenantId,
                    'customer_id' => $customer ? $customer->id : null,
                    'phone_number' => $transaction->phone_number,
                    'mac_address' => $deviceNotes['mac'] ?? null,
                    'ip_address' => $deviceNotes['ip'] ?? null,
                    'mikrotik_username' => $voucherCode,
                    'mikrotik_password' => $password,
                    'package_id' => $package ? $package->id : null,
                    'payment_type' => 'fimipay',
                    'expires_at' => $package ? now()->addMinutes($package->duration_minutes) : now()->addDay()
                ]);

                // Create Subscription
                $subscription = null;
                if ($package && $customer) {
                    $subscription = \App\Models\Subscription::create([
                        'tenant_id' => $tenantId,
                        'customer_id' => $customer->id,
                        'wifi_user_id' => $wifiUser->id,
                        'package_id' => $package->id,
                        'transaction_id' => $transaction->id,
                        'type' => 'new',
                        'status' => 'active',
                        'starts_at' => now(),
                        'expires_at' => now()->addMinutes($package->duration_minutes),
                        'notes' => 'FimiPay Auto-Provision'
                    ]);
                }

                // Create Voucher record for tracking
                \App\Models\Voucher::create([
                    'tenant_id' => $tenantId,
                    'code' => $voucherCode,
                    'wifi_user_id' => $wifiUser->id,
                    'transaction_id' => $transaction->id,
                    'status' => 'available'
                ]);

                $transaction->wifi_user_id = $wifiUser->id;
                $transaction->mikrotik_voucher = $voucherCode;

                Log::info("[PAYMENT SUCCESS] FimiPay Webhook Payment Confirmed", [
                    'transaction_reference' => $transaction->transaction_reference,
                    'payment_status' => $status,
                    'phone' => $transaction->phone_number,
                    'amount' => $transaction->amount,
                ]);

                Log::info("[PROVISION] FimiPay Webhook Hotspot WifiUser created", [
                    'wifi_user_id' => $wifiUser->id,
                    'mikrotik_username' => $voucherCode,
                    'phone' => $transaction->phone_number,
                    'mac_address' => $wifiUser->mac_address,
                    'ip_address' => $wifiUser->ip_address,
                    'expires_at' => $wifiUser->expires_at?->toDateTimeString(),
                ]);

                if ($subscription) {
                    Log::info("[SUBSCRIPTION] FimiPay Webhook Subscription created", [
                        'subscription_id' => $subscription->id,
                        'package_id' => $package->id,
                        'duration_minutes' => $package->duration_minutes,
                        'starts_at' => $subscription->starts_at->toDateTimeString(),
                        'expires_at' => $subscription->expires_at->toDateTimeString(),
                    ]);
                }
            }

            // Notice: provisioned_at remains NULL until MikroTik router polls /api/mikrotik/sync and confirms /api/mikrotik/done
            $transaction->save();
            return response()->json(['status' => 'success', 'message' => 'FimiPay webhook handled']);
        } elseif (in_array($status, ['FAILED', 'CANCELLED', 'EXPIRED', 'REJECTED', 'PAYMENT.FAILED'])) {
            $transaction->status = 'failed';
            $transaction->save();
            Log::warning("[PAYMENT] FimiPay Webhook Payment Failed", [
                'transaction_reference' => $transaction->transaction_reference,
                'payment_status' => $status,
                'verified_status' => 'FAILED',
            ]);
            return response()->json(['status' => 'failed', 'message' => 'FimiPay transaction failed']);
        }

        return response()->json(['status' => 'pending']);
    }

    /**
     * SonicPesa webhook — activates a tenant's SaaS subscription.
     */
    public function sonicPesa(Request $request)
    {
        $raw = $request->getContent();
        $signature = $request->header('X-SonicPesa-Signature') ?? $request->header('X-Webhook-Signature');

        $service = app(\App\Services\SonicPesaService::class);

        if (! $service->verifyWebhookSignature($raw, $signature)) {
            Log::warning('[SAAS] SonicPesa webhook signature verification failed', ['signature' => $signature]);

            return response()->json(['status' => 'invalid_signature'], 401);
        }

        $payload = $request->json()->all() ?: (json_decode($raw, true) ?: []);
        Log::info('[SAAS] SonicPesa Webhook Received', $payload);

        $event = $payload['event'] ?? null;
        $orderId = $payload['order_id'] ?? ($payload['data']['order_id'] ?? null);
        $status = strtoupper((string) ($payload['payment_status'] ?? $payload['status'] ?? ''));

        if (! $orderId) {
            return response()->json(['status' => 'ignored', 'message' => 'Missing order_id'], 200);
        }

        $subscription = \App\Models\SaasSubscription::where('reference_number', $orderId)->first();

        if (! $subscription) {
            return response()->json(['status' => 'ignored', 'message' => 'Subscription not found'], 200);
        }

        if ($subscription->status === 'active') {
            return response()->json(['status' => 'already_processed'], 200);
        }

        if (in_array($status, ['COMPLETED', 'SUCCESS'], true) || $event === 'payment.success') {
            $subscription->activate();

            return response()->json(['status' => 'success', 'message' => 'Subscription activated'], 200);
        }

        if (in_array($status, ['FAILED', 'CANCELLED', 'USERCANCELLED', 'REJECTED', 'EXPIRED'], true) || $event === 'payment.failed') {
            $subscription->markFailed();

            return response()->json(['status' => 'failed'], 200);
        }

        return response()->json(['status' => 'pending'], 200);
    }
}
