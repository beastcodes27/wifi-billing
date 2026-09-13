<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class MikrotikSyncController extends Controller
{
    public function sync(Request $request)
    {
        $token = $request->query('token');
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId() ?: 1;
        \Illuminate\Support\Facades\Cache::put('mikrotik_last_sync_' . $tenantId, now(), 60);
        \Illuminate\Support\Facades\Cache::put('mikrotik_last_sync_all', now(), 60);

        // Resolve correct server URL
        $serverUrl = $request->schemeAndHttpHost();
        if (empty($serverUrl) || str_contains($serverUrl, 'localhost') || str_contains($serverUrl, '127.0.0.1')) {
            $configured = config('app.url');
            if (!empty($configured) && !str_contains($configured, 'localhost') && !str_contains($configured, '127.0.0.1')) {
                $serverUrl = $configured;
            } else {
                $serverUrl = 'https://drinkpoint.store';
            }
        }
        $serverUrl = rtrim($serverUrl, '/');
        $modeParam = str_starts_with($serverUrl, 'https') ? 'mode=https check-certificate=no ' : '';

        $transactions = Transaction::withoutGlobalScopes()
            ->with(['package' => function($q) {
                $q->withoutGlobalScopes();
            }])
            ->where('status', 'success')
            ->whereNull('provisioned_at')
            ->whereNotNull('mikrotik_voucher')
            ->get();

        // Also retrieve all existing WifiUsers who have an ACTIVE, unexpired subscription
        // to automatically re-provision them if missing from MikroTik Hotspot
        $activeUsers = \App\Models\WifiUser::withoutGlobalScopes()
            ->with(['package' => function($q) {
                $q->withoutGlobalScopes();
            }])
            ->whereNotNull('mikrotik_username')
            ->whereNull('removed_at')
            ->where('expires_at', '>', now())
            ->whereHas('subscriptions', function($q) {
                $q->withoutGlobalScopes()->where('status', 'active')->where('expires_at', '>', now());
            })
            ->get();

        \Illuminate\Support\Facades\Log::info("[MIKROTIK SYNC] Router polled sync endpoint", [
            'token' => $token,
            'client_ip' => $request->ip(),
            'pending_vouchers_count' => $transactions->count(),
            'active_sub_users_count' => $activeUsers->count(),
            'pending_voucher_ids' => $transactions->pluck('id')->toArray(),
        ]);

        if ($transactions->isEmpty() && $activeUsers->isEmpty()) {
            return response(':log info "Hakuna vocha mpya za kutengeneza";', 200)
                ->header('Content-Type', 'text/plain');
        }

        $script = ":log info \"[MIKROTIK SYNC] Inakagua watumiaji " . ($transactions->count() + $activeUsers->count()) . "...\";\n";

        // 1. RECOVERY PROVISIONING: Ensure ALL active unexpired users exist on MikroTik Hotspot
        $existingCodes = [];
        foreach ($activeUsers as $u) {
            $code = $u->mikrotik_username;
            $pass = $u->mikrotik_password ?? $code;
            $existingCodes[] = $code;
            $profile = $u->package && $u->package->mikrotik_profile ? $u->package->mikrotik_profile : "default";
            $safeComment = addslashes("Active Sub: " . $u->phone_number);

            \Illuminate\Support\Facades\Log::info("[PROVISION] Active subscription re-provisioned into MikroTik script", [
                'username' => $code,
                'phone' => $u->phone_number,
                'expires_at' => $u->expires_at?->toDateTimeString(),
            ]);

            $script .= ":local prof \"{$profile}\";\n"
                . ":if ([:len [/ip hotspot user profile find name=\$prof]] = 0) do={ :set prof \"default\"; };\n"
                . ":if ([:len [/ip hotspot user find name=\"{$code}\"]] = 0) do={ "
                . "do { /ip hotspot user add name=\"{$code}\" password=\"{$pass}\" profile=\$prof comment=\"{$safeComment}\"; } "
                . "on-error={ do { /ip hotspot user add name=\"{$code}\" password=\"{$pass}\" profile=\"default\" comment=\"{$safeComment}\"; } on-error={}; }; "
                . "};\n";

            if ($u->mac_address && strpos($u->mac_address, '$') === false && strpos($u->mac_address, '-address') === false && strlen($u->mac_address) >= 11) {
                $script .= ":if ([:len [/ip hotspot user find name=\"{$u->mac_address}\"]] = 0) do={ "
                    . "do { /ip hotspot user add name=\"{$u->mac_address}\" password=\"\" profile=\$prof comment=\"Auto-Auth: {$safeComment}\"; } "
                    . "on-error={ do { /ip hotspot user add name=\"{$u->mac_address}\" password=\"\" profile=\"default\" comment=\"Auto-Auth: {$safeComment}\"; } on-error={}; }; "
                    . "};\n";
            }
        }

        // 2. NEW PROVISIONING: Process new unprovisioned transactions
        foreach ($transactions as $txn) {
            $voucher = $txn->mikrotik_voucher;
            $doneUrl = $serverUrl . "/api/mikrotik/done?id={$txn->id}" . ($token ? "&token={$token}" : "");

            \Illuminate\Support\Facades\Log::info("[PROVISION] New voucher queued for MikroTik Hotspot sync", [
                'transaction_id' => $txn->id,
                'voucher' => $voucher,
                'phone' => $txn->phone_number,
            ]);

            // If already added above, just trigger the done verification
            if (in_array($voucher, $existingCodes, true)) {
                $script .= ":if ([:len [/ip hotspot user find name=\"{$voucher}\"]] > 0) do={ "
                    . "/tool fetch url=\"{$doneUrl}\" {$modeParam}keep-result=no; "
                    . "};\n";
                continue;
            }

            $profile = $txn->package && $txn->package->mikrotik_profile ? $txn->package->mikrotik_profile : "default";
            $comment = "Phone: " . $txn->phone_number . " Pkg: " . ($txn->package ? $txn->package->name : 'N/A');
            $safeComment = addslashes($comment);

            // Robust provisioning:
            // 1. Verify profile existence, fallback to "default" if profile not found
            // 2. Add voucher user (WITHOUT limit-uptime, so expiry is strictly governed by subscription.expires_at)
            // 3. VERIFY user actually exists before fetching /api/mikrotik/done
            $script .= ":local prof \"{$profile}\";\n"
                . ":if ([:len [/ip hotspot user profile find name=\$prof]] = 0) do={ :set prof \"default\"; };\n"
                . ":if ([:len [/ip hotspot user find name=\"{$voucher}\"]] = 0) do={ "
                . "do { /ip hotspot user add name=\"{$voucher}\" password=\"{$voucher}\" profile=\$prof comment=\"{$safeComment}\"; } "
                . "on-error={ do { /ip hotspot user add name=\"{$voucher}\" password=\"{$voucher}\" profile=\"default\" comment=\"{$safeComment}\"; } "
                . "on-error={ :log error \"[MIKROTIK SYNC] Imeshindikana kuongeza {$voucher}\"; }; }; "
                . "};\n"
                . ":if ([:len [/ip hotspot user find name=\"{$voucher}\"]] > 0) do={ "
                . ":log info \"[MIKROTIK USER CREATED] Vocha {$voucher} imehakikiwa kwenye MikroTik Hotspot\"; "
                . "/tool fetch url=\"{$doneUrl}\" {$modeParam}keep-result=no; "
                . "} else={ "
                . ":log error \"[MIKROTIK PROVISION FAILED] Vocha {$voucher} haikuweza kusajiliwa kwenye MikroTik!\"; "
                . "};\n";
        }

        $script .= ":log info \"[MIKROTIK SYNC] Vocha mpya zimeshughulikiwa!\";\n";

        return response($script, 200)->header('Content-Type', 'text/plain');
    }

    public function markDone(Request $request)
    {
        $idsParam = $request->query('ids');
        $idParam = $request->query('id');

        $targetIds = [];
        if ($idsParam) {
            $targetIds = array_filter(array_map('trim', explode(',', $idsParam)));
        } elseif ($idParam) {
            $targetIds = [trim($idParam)];
        }

        if (!empty($targetIds)) {
            $txns = Transaction::withoutGlobalScopes()->whereIn('id', $targetIds)->get();
            foreach ($txns as $txn) {
                $txn->update(['provisioned_at' => now()]);

                \Illuminate\Support\Facades\Log::info("[MIKROTIK USER] User confirmed created in MikroTik Hotspot", [
                    'transaction_reference' => $txn->transaction_reference,
                    'voucher' => $txn->mikrotik_voucher,
                    'phone' => $txn->phone_number,
                    'provisioned_at' => now()->toDateTimeString(),
                    'mikrotik_status' => 'USER_CREATED_CONFIRMED',
                ]);
            }
        }
        
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function onLogin(Request $request)
    {
        $username = $request->query('username');
        $mac = $request->query('mac');
        $ip = $request->query('ip');

        \Illuminate\Support\Facades\Log::info("[MIKROTIK LOGIN] Login callback received from MikroTik Hotspot", [
            'username' => $username,
            'mac' => $mac,
            'ip' => $ip,
        ]);

        if ($username) {
            $user = \App\Models\WifiUser::withoutGlobalScopes()->where('mikrotik_username', $username)->first();
            if ($user) {
                // Check if user is expired or removed
                if (($user->expires_at && $user->expires_at->isPast()) || !is_null($user->removed_at)) {
                    \Illuminate\Support\Facades\Log::warning("[EXPIRY] Expired user attempted login rejected", [
                        'username' => $username,
                        'mac' => $mac,
                        'ip' => $ip,
                        'status' => 'LOGIN_FAILED_EXPIRED',
                        'expired_at' => $user->expires_at?->toDateTimeString(),
                    ]);
                    // Return command to kick active session and remove user immediately
                    return response(':log warning "Mtumiaji ' . $username . ' amekwisha muda wake"; do { /ip hotspot active remove [find user="' . $username . '"]; /ip hotspot user remove [find name="' . $username . '"]; /ip hotspot cookie remove [find user="' . $username . '"]; } on-error={};', 200)->header('Content-Type', 'text/plain');
                }

                $isFirstActivation = is_null($user->activated_at);
                $updateData = [
                    'is_online' => true,
                ];
                
                // Do not update MAC if it is incorrectly formatted or template variable
                if ($mac && strpos($mac, '-address') === false && strpos($mac, '$') === false) {
                    $updateData['mac_address'] = $mac;
                }
                if ($ip && strpos($ip, '$') === false) {
                    $updateData['ip_address'] = $ip;
                }
                
                if ($isFirstActivation) {
                    $updateData['activated_at'] = now();
                    if ($user->package) {
                        $updateData['expires_at'] = now()->addMinutes($user->package->duration_minutes);
                    }

                    // Update Voucher table status to used if tracked
                    \App\Models\Voucher::withoutGlobalScopes()
                        ->where('wifi_user_id', $user->id)
                        ->where('status', 'available')
                        ->update([
                            'status'  => 'used',
                            'used_at' => now(),
                        ]);
                }

                // Explicitly ensure Subscription is marked active
                \App\Models\Subscription::withoutGlobalScopes()
                    ->where('wifi_user_id', $user->id)
                    ->update([
                        'status' => 'active',
                        'starts_at' => $user->activated_at ?: now(),
                        'expires_at' => $updateData['expires_at'] ?? $user->expires_at
                    ]);
                
                $user->update($updateData);

                \Illuminate\Support\Facades\Log::info("[SUBSCRIPTION] Subscription verified active on Hotspot login", [
                    'username' => $username,
                    'expires_at' => ($updateData['expires_at'] ?? $user->expires_at)?->toDateTimeString(),
                ]);

                \Illuminate\Support\Facades\Log::info("[MIKROTIK LOGIN] User authenticated successfully in Hotspot", [
                    'username' => $username,
                    'mac' => $updateData['mac_address'] ?? $user->mac_address,
                    'ip' => $updateData['ip_address'] ?? $user->ip_address,
                    'login_status' => 'SUCCESS',
                    'subscription_expiry' => ($updateData['expires_at'] ?? $user->expires_at)?->toDateTimeString(),
                    'is_reconnect' => !$isFirstActivation,
                ]);

                \Illuminate\Support\Facades\Log::info("[MIKROTIK LOGIN] Internet access active for voucher", [
                    'username' => $username,
                    'mac' => $updateData['mac_address'] ?? $user->mac_address,
                    'ip' => $updateData['ip_address'] ?? $user->ip_address,
                    'session_status' => 'ONLINE',
                    'subscription_expires_at' => ($updateData['expires_at'] ?? $user->expires_at)?->toDateTimeString(),
                ]);

                return response('User logged in successfully', 200)->header('Content-Type', 'text/plain');
            }
        }
        
        \Illuminate\Support\Facades\Log::warning("[MIKROTIK LOGIN] Unknown or unprovisioned username login attempt", [
            'username' => $username,
            'mac' => $mac,
            'ip' => $ip,
            'login_status' => 'FAILED_NOT_FOUND',
        ]);
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function onLogout(Request $request)
    {
        $username = $request->query('username');
        $bytesIn = $request->query('bytes-in', 0);
        $bytesOut = $request->query('bytes-out', 0);
        $uptime = $request->query('uptime', '');
        $mac = $request->query('mac', '');

        if ($username) {
            $user = \App\Models\WifiUser::withoutGlobalScopes()->where('mikrotik_username', $username)->first();
            if ($user) {
                $sessionBytes = (int)$bytesIn + (int)$bytesOut;
                
                $user->update([
                    'is_online' => false,
                    'data_used' => $user->data_used + $sessionBytes,
                    'session_time' => $uptime,
                    'last_logout_at' => now(),
                ]);

                \Illuminate\Support\Facades\Log::info("[MIKROTIK LOGIN] User session disconnected from Hotspot", [
                    'username' => $username,
                    'mac' => $mac ?: $user->mac_address,
                    'uptime' => $uptime,
                    'data_used_bytes' => $sessionBytes,
                    'subscription_expiry' => $user->expires_at?->toDateTimeString(),
                ]);

                return response('User logged out successfully', 200)->header('Content-Type', 'text/plain');
            }
        }
        
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function activeUsersSync(Request $request)
    {
        // Receive simple text format: "user1,in1,out1,uptime1,mac1,ip1;user2,in2,out2,uptime2,mac2,ip2;"
        $data = trim($request->getContent());
        if (empty($data)) {
            $data = trim($request->input('data', ''));
        }

        $activeUsernames = [];

        if (!empty($data) && $data !== 'none') {
            $sessions = array_filter(explode(';', $data));
            foreach ($sessions as $sessionStr) {
                $parts = explode(',', $sessionStr);
                if (count($parts) >= 4) {
                    $username = trim($parts[0]);
                    $bytesIn = (int)($parts[1] ?? 0);
                    $bytesOut = (int)($parts[2] ?? 0);
                    $uptime = trim($parts[3] ?? '');
                    $mac = isset($parts[4]) ? trim($parts[4]) : null;
                    $ip = isset($parts[5]) ? trim($parts[5]) : null;

                    if ($username !== '') {
                        $activeUsernames[] = $username;

                        $user = \App\Models\WifiUser::withoutGlobalScopes()->where('mikrotik_username', $username)->first();
                        if ($user) {
                            $updateData = [
                                'is_online' => true,
                                'data_used' => $bytesIn + $bytesOut,
                                'session_time' => $uptime
                            ];
                            
                            if ($mac && strpos($mac, '-address') === false && $mac !== '') {
                                $updateData['mac_address'] = $mac;
                            }
                            if ($ip && $ip !== '') {
                                $updateData['ip_address'] = $ip;
                            }
                            
                            $user->update($updateData);
                        } else {
                            // User is active on MikroTik but not found in DB (e.g. manually added voucher or batch)
                            $token = $request->query('token');
                            $router = $token ? \App\Models\Router::withoutGlobalScopes()->where('sync_token', $token)->first() : null;
                            $tenantId = $router ? $router->tenant_id : (app(\App\Services\TenantContext::class)->getTenantId() ?: 1);
                            $voucher = \App\Models\Voucher::withoutGlobalScopes()->where('code', $username)->first();
                            $pkgId = $voucher ? $voucher->package_id : (\App\Models\Package::first()?->id ?? 1);
                            
                            try {
                                \App\Models\WifiUser::create([
                                    'tenant_id' => $tenantId,
                                    'router_id' => $router?->id,
                                    'phone_number' => $voucher ? 'Vocha: ' . $username : 'Mteja wa Hotspot',
                                    'mikrotik_username' => $username,
                                    'package_id' => $pkgId,
                                    'mac_address' => ($mac && strpos($mac, '-address') === false && $mac !== '') ? $mac : null,
                                    'ip_address' => ($ip && $ip !== '') ? $ip : null,
                                    'is_online' => true,
                                    'data_used' => $bytesIn + $bytesOut,
                                    'session_time' => $uptime,
                                    'activated_at' => now(),
                                    'expires_at' => now()->addHours(24),
                                ]);
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::warning("[MIKROTIK SYNC] Could not auto-register active user {$username}: " . $e->getMessage());
                            }
                        }
                    }
                }
            }
        }

        $token = $request->query('token');
        $router = $token ? \App\Models\Router::withoutGlobalScopes()->where('sync_token', $token)->first() : null;

        // Always sync the offline state: any user not in activeUsernames is offline for this router
        $offlineQuery = \App\Models\WifiUser::withoutGlobalScopes()
            ->where('is_online', true)
            ->whereNotIn('mikrotik_username', $activeUsernames);

        if ($router) {
            $offlineQuery->where(function($q) use ($router) {
                $q->where('router_id', $router->id)
                  ->orWhereNull('router_id');
            });
        }
        $offlineQuery->update(['is_online' => false]);

        \Illuminate\Support\Facades\Log::info("[MIKROTIK SYNC] Active users synced from Hotspot", [
            'router_id' => $router?->id,
            'active_count' => count($activeUsernames),
        ]);

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Wall-clock session expiry: MikroTik polls this endpoint on a schedule.
     * Returns a RouterOS script that removes users whose expires_at has passed.
     * MikroTik should run this script every ~5 minutes via Scheduler.
     */
    public function expiredUsers(Request $request)
    {
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();
        \Illuminate\Support\Facades\Cache::put('mikrotik_last_sync_' . $tenantId, now(), 60);

        // Find users whose validity has expired
        $expired = \App\Models\WifiUser::withoutGlobalScopes()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->whereNull('removed_at') // not yet removed from MikroTik
            ->get();

        if ($expired->isEmpty()) {
            return response(':log info "Hakuna vikao vilivyoisha muda";', 200)
                ->header('Content-Type', 'text/plain');
        }

        $script = ":log info \"Kuondoa watumizi walioisha muda...\"; \n";
        $removedIds = [];

        foreach ($expired as $user) {
            $username = $user->mikrotik_username;
            $removedIds[] = $user->id;

            // 1. Remove from hotspot user list entirely so they cannot re-login
            $script .= "do { /ip hotspot user remove [find name=\"{$username}\"]; } on-error={};\n";
            // 2. Remove from active hotspot sessions (kick them offline immediately)
            $script .= "do { /ip hotspot active remove [find user=\"{$username}\"]; } on-error={};\n";
            // 3. Remove from hotspot cookies to destroy persistent session
            $script .= "do { /ip hotspot cookie remove [find user=\"{$username}\"]; } on-error={};\n";
            if ($user->mac_address && strpos($user->mac_address, '$') === false && strpos($user->mac_address, '-address') === false) {
                // Also remove MAC-auth user, active session, and cookie
                $script .= "do { /ip hotspot user remove [find name=\"{$user->mac_address}\"]; } on-error={};\n";
                $script .= "do { /ip hotspot active remove [find user=\"{$user->mac_address}\"]; } on-error={};\n";
                $script .= "do { /ip hotspot cookie remove [find user=\"{$user->mac_address}\"]; } on-error={};\n";
                $script .= "do { /ip hotspot cookie remove [find mac-address=\"{$user->mac_address}\"]; } on-error={};\n";
            }

            \Illuminate\Support\Facades\Log::info("[EXPIRY] RouterOS removal script generated for expired user", [
                'username' => $username,
                'user_id' => $user->id,
                'subscription_expiry' => $user->expires_at->toDateTimeString(),
                'mac' => $user->mac_address,
            ]);
        }

        // Tell our server these users have been removed
        $idList = implode(',', $removedIds);
        $token = $request->query('token');
        $doneUrl = url("/api/mikrotik/expired-done?ids={$idList}" . ($token ? "&token={$token}" : ""));
        $modeParam = str_starts_with($doneUrl, 'https') ? 'mode=https check-certificate=no ' : '';
        $script .= "/tool fetch url=\"{$doneUrl}\" {$modeParam}keep-result=no;\n";
        $script .= ":log info \"Kuondoa kumekamilika. Watumizi {$expired->count()} wameondolewa.\"; \n";

        return response($script, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Mark expired users as removed after MikroTik has processed them.
     */
    public function markExpiredDone(Request $request)
    {
        $idsParam = $request->query('ids');
        if ($idsParam) {
            $ids = array_filter(array_map('trim', explode(',', $idsParam)));
            \App\Models\WifiUser::withoutGlobalScopes()->whereIn('id', $ids)->update([
                'removed_at'  => now(),
                'is_online'   => false,
            ]);
            \App\Models\Subscription::withoutGlobalScopes()->whereIn('wifi_user_id', $ids)->update([
                'status' => 'expired',
            ]);
            \App\Models\Voucher::withoutGlobalScopes()->whereIn('wifi_user_id', $ids)->update([
                'status' => 'expired',
            ]);
            \Illuminate\Support\Facades\Log::info("[EXPIRY] Subscriptions marked expired and users removed from MikroTik confirmed", [
                'count' => count($ids),
                'ids'   => $ids,
            ]);
        }
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Auto-Reconnect: MikroTik inaita endpoint hii baada ya kuwaka (reboot/umeme kurudi).
     * Inarudisha RouterOS script inayowarudisha wateja wote wenye vocha bado active.
     */
    public function reconnect(Request $request)
    {
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();
        \Illuminate\Support\Facades\Cache::put('mikrotik_last_sync_' . $tenantId, now(), 60);

        // Pata wateja wote wenye active subscription: hazijaisha muda NA hazijaondolewa
        $activeUsers = \App\Models\WifiUser::withoutGlobalScopes()
            ->whereNotNull('mikrotik_username')
            ->whereNull('removed_at')
            ->where('expires_at', '>', now())
            ->whereHas('subscriptions', function ($q) {
                $q->withoutGlobalScopes()->where('status', 'active')->where('expires_at', '>', now());
            })
            ->get();

        if ($activeUsers->isEmpty()) {
            return response(':log info "Hakuna wateja wa kurudisha - wote wameisha muda";', 200)
                ->header('Content-Type', 'text/plain');
        }

        \Illuminate\Support\Facades\Log::info("[AUTO RECONNECT] Bulk reconnect script generated for router recovery", [
            'active_count' => $activeUsers->count(),
            'token' => $request->query('token'),
        ]);

        $script = ":log info \"Auto-reconnect: Kurudisha wateja {$activeUsers->count()} wanaostahili...\"; \n";
        $ids = [];

        foreach ($activeUsers as $user) {
            $username = $user->mikrotik_username;
            $password = $user->mikrotik_password ?? $username;
            $ids[] = $user->id;

            $profile = 'default';
            if ($user->package) {
                $profile = $user->package->mikrotik_profile ?? 'default';
            }

            $safePhone = addslashes($user->phone_number ?? $username);
            $mac = $user->mac_address;

            // 1. Ensure voucher user exists in /ip hotspot user
            $addCmd = ":local prof \"{$profile}\"; :if ([:len [/ip hotspot user profile find name=\$prof]] = 0) do={ :set prof \"default\"; }; "
                . ":if ([:len [/ip hotspot user find name=\"{$username}\"]] = 0) do={ "
                . "do { /ip hotspot user add name=\"{$username}\" password=\"{$password}\" profile=\$prof comment=\"Active Sub: {$safePhone}\"; } "
                . "on-error={ do { /ip hotspot user add name=\"{$username}\" password=\"{$password}\" profile=\"default\" comment=\"Active Sub: {$safePhone}\"; } on-error={}; }; "
                . "};";

            // 2. Ensure MAC-Auth user exists in /ip hotspot user for instant transparent reconnect without captive portal
            if ($mac && strpos($mac, '$') === false && strpos($mac, '-address') === false && strlen($mac) >= 11) {
                $addCmd .= " :if ([:len [/ip hotspot user find name=\"{$mac}\"]] = 0) do={ "
                    . "do { /ip hotspot user add name=\"{$mac}\" password=\"\" profile=\$prof comment=\"Auto-Auth: {$safePhone}\"; } "
                    . "on-error={ do { /ip hotspot user add name=\"{$mac}\" password=\"\" profile=\"default\" comment=\"Auto-Auth: {$safePhone}\"; } on-error={}; }; "
                    . "};";
            }

            $script .= "{$addCmd}\n";
        }

        // Arifa server kwamba reconnect imefanyika
        $idList = implode(',', $ids);
        $token = $request->query('token');
        $doneUrl = url("/api/mikrotik/reconnect-done?ids={$idList}" . ($token ? "&token={$token}" : ""));
        $modeParam = str_starts_with($doneUrl, 'https') ? 'mode=https check-certificate=no ' : '';
        $script .= "/tool fetch url=\"{$doneUrl}\" {$modeParam}keep-result=no;\n";
        $script .= ":log info \"Auto-reconnect imekamilika. Wateja {$activeUsers->count()} wamerudishwa!\"; \n";

        return response($script, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * MikroTik inaita hii baada ya reconnect script kukamilika.
     * Tunaweza kutumia hii kwa logging au tracking.
     */
    public function markReconnected(Request $request)
    {
        $idsParam = $request->query('ids');
        if ($idsParam) {
            $ids = explode(',', $idsParam);
            \Illuminate\Support\Facades\Log::info("[AUTO RECONNECT] Bulk reconnect confirmed completed on router", [
                'count' => count($ids),
                'ids'   => $ids,
                'time'  => now()->toDateTimeString(),
            ]);
        }
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }
}

