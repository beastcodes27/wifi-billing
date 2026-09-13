<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Services\FimiPayService;
use Illuminate\Support\Facades\Log;

class PortalController extends Controller
{
    /**
     * Strictly verifies that the customer has an ACTIVE, unexpired subscription.
     * Authority: Subscription status='active' AND expires_at > now() AND WifiUser not removed.
     */
    protected function resolveActiveSubscriptionUser($mac = null, $voucherCode = null, $tenantId = null)
    {
        $query = \App\Models\WifiUser::withoutGlobalScopes()
            ->whereNull('removed_at');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if (!empty($mac) && !str_contains($mac, '$') && !str_contains($mac, '-address')) {
            $query->where(function($q) use ($mac, $voucherCode) {
                $q->where('mac_address', $mac);
                if (!empty($voucherCode)) {
                    $q->orWhere('mikrotik_username', $voucherCode);
                }
            });
        } elseif (!empty($voucherCode)) {
            $query->where('mikrotik_username', $voucherCode);
        } else {
            return null;
        }

        $wifiUser = $query->latest('id')->first();
        if (!$wifiUser) {
            return null;
        }

        // STRICT SUBSCRIPTION VALIDATION:
        if (!is_null($wifiUser->removed_at)) {
            Log::warning("[AUTO RECONNECT] Reconnect rejected: WifiUser was removed", [
                'id' => $wifiUser->id,
                'username' => $wifiUser->mikrotik_username,
                'mac' => $mac,
            ]);
            return null;
        }

        // Locate active Subscription record
        $subQuery = $wifiUser->subscriptions()
            ->withoutGlobalScopes()
            ->where('status', 'active')
            ->where('expires_at', '>', now());

        if ($tenantId) {
            $subQuery->where('tenant_id', $tenantId);
        }

        $activeSub = $subQuery->latest('id')->first();

        // If WifiUser has valid expires_at > now() but no Subscription record was created previously, auto-create one
        if (!$activeSub && $wifiUser->expires_at && $wifiUser->expires_at->gt(now())) {
            $activeSub = \App\Models\Subscription::create([
                'tenant_id' => $wifiUser->tenant_id ?: ($tenantId ?: 1),
                'customer_id' => $wifiUser->customer_id,
                'wifi_user_id' => $wifiUser->id,
                'package_id' => $wifiUser->package_id,
                'type' => 'new',
                'status' => 'active',
                'starts_at' => $wifiUser->activated_at ?: $wifiUser->created_at,
                'expires_at' => $wifiUser->expires_at,
                'notes' => 'Auto-restored active subscription authority'
            ]);
            Log::info("[SUBSCRIPTION] Active subscription authority restored for user", [
                'wifi_user_id' => $wifiUser->id,
                'expires_at' => $wifiUser->expires_at->toDateTimeString(),
            ]);
        }

        if (!$activeSub || !$activeSub->expires_at || $activeSub->expires_at->isPast() || $activeSub->status !== 'active') {
            Log::info("[AUTO RECONNECT] Auto-reconnect denied: No active unexpired subscription found", [
                'wifi_user_id' => $wifiUser->id,
                'mac' => $mac,
                'status' => $activeSub ? $activeSub->status : 'none',
                'expires_at' => $activeSub ? $activeSub->expires_at?->toDateTimeString() : null,
            ]);
            return null;
        }

        // Align WifiUser with the active subscription expiry (never alter expires_at)
        if (!$wifiUser->expires_at || $activeSub->expires_at->gt($wifiUser->expires_at)) {
            $wifiUser->update(['expires_at' => $activeSub->expires_at]);
            $wifiUser->expires_at = $activeSub->expires_at;
        }

        // Update MAC if device connected with a randomized MAC
        if (!empty($mac) && !str_contains($mac, '$') && !str_contains($mac, '-address') && $wifiUser->mac_address !== $mac) {
            $wifiUser->update(['mac_address' => $mac]);
        }

        return $wifiUser;
    }

    public function index(Request $request)
    {
        if ($request->has('lang')) {
            session(['locale' => $request->query('lang')]);
        }
        $lang = session('locale', 'sw');

        // Mikrotik sends mac, ip, link-login, link-orig, etc in the URL for the hotspot
        $mac = $request->query('mac', '');
        $ip = $request->query('ip', '');
        $loginUrl = $request->query('link-login') ?: $request->query('link-login-only', 'http://192.168.88.1/login');
        if (empty($loginUrl)) {
            $loginUrl = 'http://192.168.88.1/login';
        }
        $linkOrig = $request->query('link-orig') ?: 'http://www.google.com';
        $error = $request->query('error');
        $errorMessage = null;

        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();

        // Check if user has an active, unexpired subscription for automatic reconnection
        $forcePortal = $request->has('force_portal') || $request->boolean('force_portal');
        $isTerminalError = $error && (str_contains(strtolower($error), 'expired') || str_contains(strtolower($error), 'uptime') || str_contains(strtolower($error), 'limit') || str_contains(strtolower($error), 'invalid'));

        if (!$forcePortal && !$isTerminalError) {
            $savedVoucher = $request->cookie('medbando_active_voucher') ?: session('active_voucher');
            $activeWifiUser = $this->resolveActiveSubscriptionUser($mac, $savedVoucher, $tenantId);

            // If active user found and came via captive portal (loginUrl available):
            if ($activeWifiUser && !empty($loginUrl)) {
                $remainingMinutes = max(1, (int) now()->diffInMinutes($activeWifiUser->expires_at, false));

                Log::info("[AUTO RECONNECT] Auto-reconnect approved for active customer subscription", [
                    'username' => $activeWifiUser->mikrotik_username,
                    'mac' => $mac,
                    'ip' => $ip,
                    'expires_at' => $activeWifiUser->expires_at->toDateTimeString(),
                    'remaining_minutes' => $remainingMinutes,
                ]);

                return response()->view('portal.auto_reconnect', compact('activeWifiUser', 'loginUrl', 'linkOrig', 'mac', 'ip', 'lang'))
                    ->withCookie(cookie('medbando_active_voucher', $activeWifiUser->mikrotik_username, $remainingMinutes));
            }
        }

        // Clean up expired cookie if no active user
        if ($request->cookie('medbando_active_voucher')) {
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('medbando_active_voucher'));
        }

        Log::info("[PORTAL] Customer captive portal loaded", [
            'mac' => $mac,
            'ip' => $ip,
            'error' => $error,
        ]);

        $packages = Package::where('is_active', true)->get();

        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        // Online payment via FimiPay is active
        $hasAutoPayment = true;
        $adminPhone = '0762457308'; // Default fallback

        if ($tenant) {
            // Get admin phone (hotspot owner)
            $owner = \App\Models\User::where('tenant_id', $tenant->id)->first();
            if ($owner && $owner->phone_number) {
                $adminPhone = $owner->phone_number;
            }
        }

        if ($error) {
            $lower = strtolower($error);
            if (str_contains($lower, 'timeout')) {
                $errorMessage = ($lang == 'en')
                    ? 'Payment time expired. Please try again.'
                    : 'Muda wa malipo umekwisha. Jaribu tena.';
            } elseif (str_contains($lower, 'invalid') || str_contains($lower, 'not found') || str_contains($lower, 'wrong')) {
                $errorMessage = ($lang == 'en')
                    ? 'Invalid voucher number.'
                    : 'Namba ya vocha sio sahihi.';
            } elseif (str_contains($lower, 'uptime') || str_contains($lower, 'expired') || str_contains($lower, 'limit')) {
                $errorMessage = ($lang == 'en')
                    ? 'This voucher has expired.'
                    : 'Vocha hii imekwisha.';
            } elseif (str_contains($lower, 'already') || str_contains($lower, 'simultaneous')) {
                $errorMessage = ($lang == 'en')
                    ? 'Voucher in use on another device.'
                    : 'Vocha inatumika kifaa kingine.';
            } else {
                $errorMessage = $error;
            }
        }

        return view('portal.index', compact('packages', 'mac', 'ip', 'loginUrl', 'linkOrig', 'lang', 'hasAutoPayment', 'adminPhone', 'errorMessage'));
    }

    public function checkVoucher($code)
    {
        $code = trim($code);
        if (empty($code)) {
            return response()->json([
                'valid' => false,
                'message' => 'Weka namba ya vocha.'
            ]);
        }

        $user = \App\Models\WifiUser::withoutGlobalScopes()
            ->where('mikrotik_username', $code)
            ->first();

        // Check if explicitly expired in database
        if ($user) {
            if (($user->expires_at && $user->expires_at->isPast()) || !is_null($user->removed_at)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Vocha hii imekwisha muda wake.'
                ]);
            }
            return response()->json([
                'valid' => true,
                'message' => 'Sahihi'
            ]);
        }

        $voucher = \App\Models\Voucher::withoutGlobalScopes()
            ->where('code', $code)
            ->first();

        if ($voucher) {
            if ($voucher->status === 'expired' || $voucher->status === 'cancelled') {
                return response()->json([
                    'valid' => false,
                    'message' => 'Vocha hii imekwisha muda wake.'
                ]);
            }
            return response()->json([
                'valid' => true,
                'message' => 'Sahihi'
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Namba ya vocha haipo au si sahihi.'
        ]);
    }

    public function processPayment(Request $request, FimiPayService $fimiPayService)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'mac_address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('portal.index')->with('error', $validator->errors()->first());
        }

        $package = Package::withoutGlobalScopes()->find($request->package_id) ?: Package::find($request->package_id);
        if (!$package) {
            return redirect()->route('portal.index')->with('error', 'Kifurushi ulichochagua hakipatikani.');
        }

        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        if (!$tenant && !empty($package->tenant_id)) {
            $tenant = \App\Models\Tenant::find($package->tenant_id);
            if ($tenant) {
                app(\App\Services\TenantContext::class)->setTenant($tenant);
            }
        }

        $transactionRef = 'TXN-' . strtoupper(uniqid());

        // Format phone number
        $phone = $fimiPayService->formatPhone($request->phone_number);

        $mac = $request->input('mac_address', '');
        $ip = $request->input('ip_address', '');
        $resolvedLoginUrl = $request->input('login_url', 'http://192.168.88.1/login');
        if (empty($resolvedLoginUrl)) {
            $resolvedLoginUrl = 'http://192.168.88.1/login';
        }
        $linkOrig = $request->input('link_orig', 'http://www.google.com');

        $metadata = [
            'transaction_reference' => $transactionRef,
            'package_id' => $package->id,
            'mac_address' => $mac,
            'ip_address' => $ip,
            'tenant_id' => $tenant ? $tenant->id : null,
        ];

        Log::info("[PORTAL] Payment request initiated", [
            'package_id' => $package->id,
            'package_name' => $package->name,
            'price' => $package->price,
            'phone' => $phone,
            'mac' => $mac,
        ]);

        // Initiate payment via FimiPay API
        $res = $fimiPayService->createOrder(
            phone: $phone,
            amount: $package->price,
            reference: $transactionRef,
            metadata: $metadata,
            name: 'Mteja WiFi',
            email: "mteja{$phone}@sayuni.co.tz",
            tenant: $tenant
        );

        if ($res['success'] && !empty($res['order_id'])) {
            $orderId = $res['order_id'];

            Log::info("[PAYMENT] FimiPay payment order created", [
                'transaction_reference' => $transactionRef,
                'order_id' => $orderId,
                'amount' => $package->price,
                'phone' => $phone,
            ]);

            // Save transaction to DB as pending with device identity in notes
            $resolvedTenantId = $tenant ? $tenant->id : 1;
            \App\Models\Transaction::create([
                'tenant_id' => $resolvedTenantId,
                'transaction_reference' => $transactionRef,
                'palm_pesa_order_id' => $orderId, // backward-compat
                'gateway_transaction_id' => $orderId,
                'payment_gateway' => 'fimipay',
                'payment_method' => 'fimipay',
                'phone_number' => $request->phone_number,
                'amount' => $package->price,
                'status' => 'pending',
                'package_id' => $package->id,
                'notes' => json_encode([
                    'mac' => $mac,
                    'ip' => $ip,
                    'login_url' => $resolvedLoginUrl,
                    'link_orig' => $linkOrig,
                ]),
            ]);

            session(['recent_txn_ref' => $transactionRef]);
            \Illuminate\Support\Facades\Cookie::queue('recent_txn_ref', $transactionRef, 120);

            return redirect()->route('portal.processing', [
                'transactionRef' => $transactionRef,
                'loginUrl' => $resolvedLoginUrl,
                'mac' => $mac,
                'ip' => $ip,
                'linkOrig' => $linkOrig,
            ]);
        }

        $errorMsg = $res['message'] ?? 'Malipo yameshindwa kuanza. Tafadhali jaribu tena.';
        Log::error("[ERROR] FimiPay payment order creation failed", [
            'phone' => $phone,
            'message' => $errorMsg,
        ]);
        return redirect()->route('portal.index')->with('error', $errorMsg);
    }

    public function processing(Request $request, $transactionRef)
    {
        $transaction = \App\Models\Transaction::withoutGlobalScopes()
            ->where('transaction_reference', $transactionRef)
            ->firstOrFail();

        // Restore device context from request or transaction notes fallback
        $deviceNotes = [];
        if (!empty($transaction->notes)) {
            $deviceNotes = json_decode($transaction->notes, true) ?: [];
        }

        $loginUrl = $request->query('loginUrl') ?: ($deviceNotes['login_url'] ?? 'http://192.168.88.1/login');
        if (empty($loginUrl)) {
            $loginUrl = 'http://192.168.88.1/login';
        }
        $mac = $request->query('mac') ?: ($deviceNotes['mac'] ?? '');
        $ip = $request->query('ip') ?: ($deviceNotes['ip'] ?? '');
        $linkOrig = $request->query('linkOrig') ?: ($deviceNotes['link_orig'] ?? 'http://www.google.com');
        
        return view('portal.processing', [
            'transactionRef' => $transactionRef,
            'phone' => $transaction->phone_number,
            'phoneNumber' => $transaction->phone_number,
            'amount' => $transaction->amount,
            'package' => $transaction->package,
            'loginUrl' => $loginUrl,
            'mac' => $mac,
            'ip' => $ip,
            'linkOrig' => $linkOrig,
        ]);
    }

    public function checkStatus($transactionRef, FimiPayService $fimiPayService)
    {
        $transaction = \App\Models\Transaction::withoutGlobalScopes()->where('transaction_reference', $transactionRef)->first();
        if (!$transaction) {
            return response()->json(['status' => 'failed']);
        }
        
        $orderId = $transaction->gateway_transaction_id ?: $transaction->palm_pesa_order_id;

        if ($transaction->status === 'pending' && $orderId) {
            try {
                $tenant = $transaction->tenant_id 
                    ? \App\Models\Tenant::find($transaction->tenant_id) 
                    : (app(\App\Services\TenantContext::class)->getTenant() ?: \App\Models\Tenant::find(1));
                if ($tenant) {
                    app(\App\Services\TenantContext::class)->setTenant($tenant);
                }
                
                $isSuccessfulPayment = false;
                $isFailedPayment = false;
                $paymentStatus = 'PENDING';

                // Primary: FimiPay Gateway
                if ($transaction->payment_gateway === 'fimipay' || empty($transaction->payment_gateway)) {
                    $res = $fimiPayService->getOrderStatus($orderId, $tenant);
                    $paymentStatus = strtoupper($res['payment_status'] ?? 'PENDING');
                    $isSuccessfulPayment = !empty($res['is_paid']);
                    $isFailedPayment = ($res['status'] ?? '') === 'FAILED';
                } else {
                    // Fallback for legacy Palm Pesa transactions
                    $apiKey = $tenant->settings['palmpesa']['api_key'] ?? env('PALM_PESA_API_KEY');
                    $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                        ->post(env('PALM_PESA_BASE_URL') . '/order-status', [
                            'order_id' => $orderId
                        ]);

                    $responseData = $response->json();
                    if (isset($responseData['data'][0]['payment_status'])) {
                        $paymentStatus = strtoupper($responseData['data'][0]['payment_status']);
                    }
                    $isSuccessfulPayment = in_array($paymentStatus, ['COMPLETED', 'PAID', 'SETTLED', 'SUCCESSFUL'], true);
                    $isFailedPayment = in_array($paymentStatus, ['FAILED', 'CANCELLED', 'EXPIRED', 'REJECTED'], true);
                }

                if ($isSuccessfulPayment) {
                    $transaction->status = 'success';
                    
                    // Only generate voucher & wifi_user if not already created (idempotent)
                    if (empty($transaction->mikrotik_voucher)) {
                        $package = $transaction->package;
                        $voucherCode = (string) mt_rand(100000, 999999);
                        
                        // Link or auto-create customer record
                        $customer = \App\Models\Customer::withoutGlobalScopes()->where('phone_number', $transaction->phone_number)->first();
                        if (!$customer && !empty($transaction->phone_number)) {
                            $customer = \App\Models\Customer::create([
                                'tenant_id' => $tenant ? $tenant->id : 1,
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

                        $durationMinutes = $package ? $package->duration_minutes : 60;
                        $expiresAt = now()->addMinutes($durationMinutes);

                        $wifiUser = \App\Models\WifiUser::create([
                            'tenant_id' => $tenant ? $tenant->id : 1,
                            'customer_id' => $customer ? $customer->id : null,
                            'phone_number' => $transaction->phone_number,
                            'mac_address' => $deviceNotes['mac'] ?? null,
                            'ip_address' => $deviceNotes['ip'] ?? null,
                            'mikrotik_username' => $voucherCode,
                            'mikrotik_password' => $voucherCode,
                            'package_id' => $package ? $package->id : null,
                            'payment_type' => 'fimipay',
                            'expires_at' => $expiresAt
                        ]);
                        
                        $subscription = null;
                        if ($package && $customer) {
                            $subscription = \App\Models\Subscription::create([
                                'tenant_id' => $tenant ? $tenant->id : 1,
                                'customer_id' => $customer->id,
                                'wifi_user_id' => $wifiUser->id,
                                'package_id' => $package->id,
                                'transaction_id' => $transaction->id,
                                'type' => 'new',
                                'status' => 'active',
                                'starts_at' => now(),
                                'expires_at' => $expiresAt,
                                'notes' => 'FimiPay Auto-Provision'
                            ]);
                        }

                        // Create Voucher record for tracking
                        \App\Models\Voucher::create([
                            'tenant_id' => $tenant ? $tenant->id : 1,
                            'code' => $voucherCode,
                            'wifi_user_id' => $wifiUser->id,
                            'transaction_id' => $transaction->id,
                            'status' => 'available'
                        ]);

                        $transaction->wifi_user_id = $wifiUser->id;
                        $transaction->mikrotik_voucher = $voucherCode;

                        // Save voucher to cookie for auto-reconnect
                        \Illuminate\Support\Facades\Cookie::queue('medbando_active_voucher', $voucherCode, $durationMinutes);
                        session(['active_voucher' => $voucherCode]);

                        Log::info("[PAYMENT SUCCESS] Payment verified successfully via FimiPay", [
                            'transaction_reference' => $transaction->transaction_reference,
                            'order_id' => $orderId,
                            'phone_number' => $transaction->phone_number,
                            'amount' => $transaction->amount,
                            'payment_status' => $paymentStatus,
                            'verified_status' => 'SUCCESS',
                        ]);

                        Log::info("[PROVISION] Hotspot WifiUser provisioned for MikroTik sync", [
                            'wifi_user_id' => $wifiUser->id,
                            'mikrotik_username' => $voucherCode,
                            'phone' => $transaction->phone_number,
                            'mac_address' => $wifiUser->mac_address,
                            'ip_address' => $wifiUser->ip_address,
                            'expires_at' => $expiresAt->toDateTimeString(),
                        ]);

                        if ($subscription) {
                            Log::info("[SUBSCRIPTION] Active subscription created", [
                                'subscription_id' => $subscription->id,
                                'package_id' => $package->id,
                                'package_name' => $package->name,
                                'duration_minutes' => $durationMinutes,
                                'starts_at' => $subscription->starts_at->toDateTimeString(),
                                'expires_at' => $subscription->expires_at->toDateTimeString(),
                                'status' => 'active',
                            ]);
                        }
                    }

                    // Notice: provisioned_at remains NULL until MikroTik router polls /api/mikrotik/sync and confirms /api/mikrotik/done
                    $transaction->save();
                } elseif ($isFailedPayment) {
                    $transaction->status = 'failed';
                    $transaction->save();

                    Log::warning("[PAYMENT] Payment failed or cancelled", [
                        'transaction_reference' => $transaction->transaction_reference,
                        'payment_status' => $paymentStatus,
                        'verified_status' => 'FAILED',
                    ]);
                } else {
                    Log::info("[PAYMENT] Payment status pending - user access denied until confirmed", [
                        'transaction_reference' => $transaction->transaction_reference,
                        'payment_status' => $paymentStatus,
                        'verified_status' => 'PENDING',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('[ERROR] FimiPay status check exception: ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'status' => $transaction->status,
            'voucher' => $transaction->status === 'success' ? $transaction->mikrotik_voucher : null,
            'is_provisioned' => !is_null($transaction->provisioned_at)
        ]);
    }

    /**
     * Recovery mechanism for existing customers:
     * Restores access for customer with active subscription without asking for voucher or payment.
     */
    public function restoreBundle(Request $request)
    {
        $rawPhone = trim($request->input('phone_number', ''));
        $mac = trim($request->input('mac_address', ''));
        $ip = trim($request->input('ip_address', ''));
        $loginUrl = $request->input('login_url', 'http://192.168.88.1/login');
        if (empty($loginUrl)) {
            $loginUrl = 'http://192.168.88.1/login';
        }
        $linkOrig = $request->input('link_orig', 'http://www.google.com');

        if (empty($rawPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'Tafadhali weka namba yako ya simu.',
            ]);
        }

        $key = 'restore-bundle:' . $request->ip();
        Log::info("[PORTAL] Customer requested bundle restoration", [
            'phone' => $rawPhone,
            'mac' => $mac,
            'ip' => $ip,
        ]);

        if (empty($rawPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'Tafadhali weka namba yako ya simu.',
            ]);
        }

        $key = 'restore-bundle:' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 10)) {
            Log::warning("[PORTAL] Rate limit hit on restore bundle", ['ip' => $request->ip()]);
            return response()->json([
                'success' => false,
                'message' => 'Umejaribu mara nyingi mno. Tafadhali subiri dakika moja kabla ya kujaribu tena.',
            ], 429);
        }
        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        // Clean phone number (e.g. 0762457308, +255762457308, 255762457308)
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        $last9 = strlen($cleanPhone) >= 9 ? substr($cleanPhone, -9) : $cleanPhone;
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();

        // Find WifiUser with valid, unexpired subscription
        $query = \App\Models\WifiUser::withoutGlobalScopes()
            ->where(function($q) use ($cleanPhone, $last9) {
                $q->where('phone_number', $cleanPhone)
                  ->orWhere('phone_number', 'LIKE', '%' . $last9);
            })
            ->whereNull('removed_at');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $wifiUser = $query->latest('id')->first();

        $activeSub = null;
        if ($wifiUser) {
            $subQ = $wifiUser->subscriptions()
                ->withoutGlobalScopes()
                ->where('status', 'active')
                ->where('expires_at', '>', now());
            if ($tenantId) {
                $subQ->where('tenant_id', $tenantId);
            }
            $activeSub = $subQ->latest('id')->first();

            // Auto-create active subscription authority if missing
            if (!$activeSub && $wifiUser->expires_at && $wifiUser->expires_at->gt(now())) {
                $activeSub = \App\Models\Subscription::create([
                    'tenant_id' => $wifiUser->tenant_id ?: ($tenantId ?: 1),
                    'customer_id' => $wifiUser->customer_id,
                    'wifi_user_id' => $wifiUser->id,
                    'package_id' => $wifiUser->package_id,
                    'type' => 'new',
                    'status' => 'active',
                    'starts_at' => $wifiUser->activated_at ?: $wifiUser->created_at,
                    'expires_at' => $wifiUser->expires_at,
                    'notes' => 'Auto-restored active subscription authority'
                ]);
            }
        }

        if (!$wifiUser || !$activeSub || $activeSub->expires_at->isPast()) {
            // Check if there is an expired subscription to give informative feedback
            $expiredUser = \App\Models\WifiUser::withoutGlobalScopes()
                ->where(function($q) use ($cleanPhone, $last9) {
                    $q->where('phone_number', $cleanPhone)
                      ->orWhere('phone_number', 'LIKE', '%' . $last9);
                })
                ->where('expires_at', '<=', now())
                ->latest('id')
                ->first();

            if ($expiredUser) {
                Log::info("[EXPIRY] Customer restore bundle attempted for expired subscription", [
                    'phone' => $cleanPhone,
                    'expired_at' => $expiredUser->expires_at?->toDateTimeString(),
                ]);
                return response()->json([
                    'success' => false,
                    'is_expired' => true,
                    'message' => 'Muda wa bando lako umekwisha. Tafadhali chagua kifurushi hapa chini kulipia upya.',
                ]);
            }

            Log::warning("[AUTO RECONNECT] Restore bundle: No active subscription found", ['phone' => $cleanPhone]);
            return response()->json([
                'success' => false,
                'is_expired' => false,
                'message' => 'Hakuna bando linalofanya kazi lililopatikana kwa namba hii. Tafadhali chagua kifurushi hapa chini.',
            ]);
        }

        // ACTIVE SUBSCRIPTION FOUND!
        // 1. DO NOT create a new subscription
        // 2. DO NOT change expires_at
        // 3. DO NOT extend duration
        // 4. DO NOT charge customer
        // 5. Update device MAC to current device so subsequent reconnects are automatic
        if (!empty($mac) && !str_contains($mac, '$') && !str_contains($mac, '-address')) {
            $wifiUser->update([
                'mac_address' => $mac,
                'ip_address' => $ip ?: $wifiUser->ip_address,
            ]);
        }

        // Save active voucher to cookie for auto-reconnect
        $remainingMinutes = max(1, (int) now()->diffInMinutes($activeSub->expires_at, false));
        \Illuminate\Support\Facades\Cookie::queue('medbando_active_voucher', $wifiUser->mikrotik_username, $remainingMinutes);
        session(['active_voucher' => $wifiUser->mikrotik_username]);

        $remainingHours = (int) floor($remainingMinutes / 60);
        $remainingMins = $remainingMinutes % 60;
        $remainingStr = $remainingHours > 0 ? "{$remainingHours}h {$remainingMins}m" : "{$remainingMins}m";

        Log::info("[SUBSCRIPTION] Active subscription verified for customer recovery", [
            'subscription_id' => $activeSub->id,
            'phone' => $cleanPhone,
            'expires_at' => $activeSub->expires_at->toDateTimeString(),
        ]);

        Log::info("[AUTO RECONNECT] Existing customer restored active subscription without voucher", [
            'phone' => $cleanPhone,
            'username' => $wifiUser->mikrotik_username,
            'mac' => $mac,
            'expires_at' => $activeSub->expires_at->toDateTimeString(),
            'remaining' => $remainingStr,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bando lako lipo active! Inakuunganisha...',
            'username' => $wifiUser->mikrotik_username,
            'password' => $wifiUser->mikrotik_password ?? $wifiUser->mikrotik_username,
            'package_name' => $wifiUser->package ? $wifiUser->package->name : 'Bando Active',
            'expires_at' => $activeSub->expires_at->toDateTimeString(),
            'remaining_time' => $remainingStr,
            'login_url' => $loginUrl,
            'link_orig' => $linkOrig,
        ]);
    }

    /**
     * Check if a saved voucher code from device localStorage is still active
     */
    public function checkSavedSession(Request $request)
    {
        $code = trim($request->query('voucher', ''));
        $mac = trim($request->query('mac', ''));
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();

        Log::info("[PORTAL] Checking saved session from device localStorage", ['voucher' => $code, 'mac' => $mac]);

        if (empty($code)) {
            return response()->json(['valid' => false]);
        }

        $wifiUser = $this->resolveActiveSubscriptionUser($mac, $code, $tenantId);

        if ($wifiUser) {
            Log::info("[AUTO RECONNECT] Saved session verified active", [
                'username' => $wifiUser->mikrotik_username,
                'expires_at' => $wifiUser->expires_at?->toDateTimeString(),
            ]);

            return response()->json([
                'valid' => true,
                'username' => $wifiUser->mikrotik_username,
                'password' => $wifiUser->mikrotik_password ?? $wifiUser->mikrotik_username,
                'expires_at' => $wifiUser->expires_at->toDateTimeString(),
            ]);
        }

        Log::info("[AUTO RECONNECT] Saved session expired or not found", ['voucher' => $code]);
        return response()->json(['valid' => false]);
    }
}
