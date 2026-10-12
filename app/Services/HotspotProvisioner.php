<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Models\WifiUser;
use Illuminate\Support\Facades\Log;

/**
 * Provisions a hotspot voucher for a paid transaction (idempotent).
 */
class HotspotProvisioner
{
    /**
     * @return string|null The voucher code, or null if already provisioned.
     */
    public function provision(Transaction $transaction): ?string
    {
        if (! empty($transaction->mikrotik_voucher)) {
            return $transaction->mikrotik_voucher;
        }

        $tenantId = $transaction->tenant_id ?: 1;

        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            app(TenantContext::class)->setTenant($tenant);
        }

        $package = $transaction->package;

        // Link or auto-create the customer.
        $customer = null;
        if (! empty($transaction->phone_number)) {
            $customer = Customer::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('phone_number', $transaction->phone_number)
                ->first();

            if (! $customer) {
                $customer = Customer::create([
                    'tenant_id' => $tenantId,
                    'phone_number' => $transaction->phone_number,
                    'name' => 'Mteja ' . substr($transaction->phone_number, -4),
                    'status' => 'active',
                ]);
            }

            $transaction->customer_id = $customer->id;
        }

        $deviceNotes = [];
        if (! empty($transaction->notes)) {
            $deviceNotes = json_decode($transaction->notes, true) ?: [];
        }

        $durationMinutes = $package ? $package->duration_minutes : 60;
        $expiresAt = now()->addMinutes($durationMinutes);
        $voucherCode = (string) mt_rand(100000, 999999);

        $wifiUser = WifiUser::create([
            'tenant_id' => $tenantId,
            'customer_id' => $customer?->id,
            'phone_number' => $transaction->phone_number,
            'mac_address' => $deviceNotes['mac'] ?? null,
            'ip_address' => $deviceNotes['ip'] ?? null,
            'mikrotik_username' => $voucherCode,
            'mikrotik_password' => $voucherCode,
            'package_id' => $package?->id,
            'payment_type' => $transaction->payment_gateway ?: 'sonicpesa',
            'expires_at' => $expiresAt,
        ]);

        if ($package && $customer) {
            Subscription::create([
                'tenant_id' => $tenantId,
                'customer_id' => $customer->id,
                'wifi_user_id' => $wifiUser->id,
                'package_id' => $package->id,
                'transaction_id' => $transaction->id,
                'type' => 'new',
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => $expiresAt,
                'notes' => 'Auto-Provision',
            ]);
        }

        Voucher::create([
            'tenant_id' => $tenantId,
            'code' => $voucherCode,
            'wifi_user_id' => $wifiUser->id,
            'transaction_id' => $transaction->id,
            'status' => 'available',
        ]);

        $transaction->wifi_user_id = $wifiUser->id;
        $transaction->mikrotik_voucher = $voucherCode;
        $transaction->status = 'success';
        $transaction->save();

        Log::info('[PROVISION] Hotspot voucher provisioned', [
            'transaction_id' => $transaction->id,
            'voucher' => $voucherCode,
            'phone' => $transaction->phone_number,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);

        return $voucherCode;
    }
}
