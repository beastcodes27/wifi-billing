<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\WifiUser;
use Illuminate\Support\Str;

class ManualVoucherController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'phone_number' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();
        $package = Package::withoutGlobalScopes()->find($request->package_id);
        
        // Generate unique 6 random digits
        $voucherCode = (string) mt_rand(100000, 999999);
        while (WifiUser::withoutGlobalScopes()->where('mikrotik_username', $voucherCode)->exists()) {
            $voucherCode = (string) mt_rand(100000, 999999);
        }
        
        $rawPhone = !empty($request->phone_number) ? trim($request->phone_number) : (!empty($request->phone) ? trim($request->phone) : null);
        $phone = $rawPhone ?: 'Mauzo ya Vocha (Cash)';

        $customer = null;
        if ($rawPhone) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            $customer = \App\Models\Customer::withoutGlobalScopes()
                ->where('phone_number', $cleanPhone)
                ->first();
            if (!$customer) {
                $customer = \App\Models\Customer::create([
                    'tenant_id' => $tenantId,
                    'name' => 'Mteja ' . substr($cleanPhone, -4),
                    'phone_number' => $cleanPhone,
                    'status' => 'active',
                ]);
            }
        }

        $expiresAt = now()->addMinutes($package->duration_minutes);

        // 1. Unda WifiUser
        $wifiUser = WifiUser::create([
            'tenant_id' => $tenantId,
            'customer_id' => $customer ? $customer->id : null,
            'phone_number' => $phone,
            'mikrotik_username' => $voucherCode,
            'mikrotik_password' => $voucherCode,
            'package_id' => $package->id,
            'payment_type' => 'cash',
            'expires_at' => $expiresAt,
            'activated_at' => now(),
            'is_online' => false,
        ]);

        // 2. Unda Transaction ya Mauzo ya Cash
        $transaction = Transaction::create([
            'tenant_id' => $tenantId,
            'customer_id' => $customer ? $customer->id : null,
            'transaction_reference' => 'MANUAL-' . strtoupper(uniqid()),
            'palm_pesa_order_id' => 'CASH-' . strtoupper(uniqid()),
            'phone_number' => $phone,
            'amount' => $package->price,
            'status' => 'success',
            'payment_method' => 'cash',
            'package_id' => $package->id,
            'wifi_user_id' => $wifiUser->id,
            'mikrotik_voucher' => $voucherCode,
        ]);

        // 3. Unda Active Subscription
        $fallbackCust = $customer ?: \App\Models\Customer::withoutGlobalScopes()->first();
        \App\Models\Subscription::create([
            'tenant_id' => $tenantId,
            'customer_id' => $fallbackCust ? $fallbackCust->id : 1,
            'wifi_user_id' => $wifiUser->id,
            'package_id' => $package->id,
            'transaction_id' => $transaction->id,
            'type' => 'new',
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => $expiresAt,
            'notes' => 'Mauzo ya Vocha (Manual Cash)'
        ]);

        // 4. Unda Voucher rekodi
        \App\Models\Voucher::create([
            'tenant_id' => $tenantId,
            'code' => $voucherCode,
            'wifi_user_id' => $wifiUser->id,
            'transaction_id' => $transaction->id,
            'status' => 'available',
        ]);

        \Illuminate\Support\Facades\Log::info("[PAYMENT SUCCESS] Manual cash payment recorded", [
            'transaction_reference' => $transaction->transaction_reference,
            'amount' => $package->price,
            'phone' => $phone,
        ]);

        \Illuminate\Support\Facades\Log::info("[SUBSCRIPTION] Active subscription created for manual voucher", [
            'username' => $voucherCode,
            'starts_at' => now()->toDateTimeString(),
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);

        \Illuminate\Support\Facades\Log::info("[PROVISION] Manual voucher queued for MikroTik Hotspot sync", [
            'username' => $voucherCode,
            'package_id' => $package->id,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);

        return redirect()->back()->with('voucher_success', "Vocha mpya imetengenezwa na kurekodiwa kikamilifu: <strong>{$voucherCode}</strong> (Tsh " . number_format($package->price) . ")");
    }
}
