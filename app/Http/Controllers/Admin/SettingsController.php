<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings');
    }

    public function mikrotikStatus()
    {
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();
        $lastSync = \Illuminate\Support\Facades\Cache::get('mikrotik_last_sync_' . $tenantId);
        $isConnected = false;
        $message = 'Haijaunganishwa';

        if ($lastSync) {
            $diffSeconds = now()->diffInSeconds($lastSync);
            if ($diffSeconds < 30) {
                $isConnected = true;
                $message = 'Imeunganishwa (Safi)';
            } else {
                $message = 'Mawasiliano yamekatika (Mwisho: ' . $lastSync->diffForHumans() . ')';
            }
        }

        return response()->json([
            'connected' => $isConnected,
            'message' => $message,
            'last_sync' => $lastSync ? $lastSync->format('Y-m-d H:i:s') : null
        ]);
    }

    public function resetSales(Request $request)
    {
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId();
        $includeVouchers = $request->boolean('include_vouchers');

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if ($tenantId) {
            \App\Models\Transaction::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            \App\Models\Expense::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            \App\Models\AppNotification::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();

            if ($includeVouchers) {
                \App\Models\WifiUser::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
                \App\Models\VoucherBatch::withoutGlobalScopes()->where('tenant_id', $tenantId)->delete();
            }
        } else {
            \App\Models\Transaction::truncate();
            \App\Models\Expense::truncate();
            \App\Models\AppNotification::truncate();

            if ($includeVouchers) {
                \App\Models\WifiUser::truncate();
                \App\Models\VoucherBatch::truncate();
            }
        }

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('admin.dashboard')->with('success', 'Takwimu za mapato zimewekwa upya! Dashibodi sasa inasoma Tsh 00.');
    }

    public function setSales(Request $request)
    {
        $tenantId = app(\App\Services\TenantContext::class)->getTenantId() ?: 1;
        $targetAmount = (float) $request->input('target_amount', 10000);

        if ($targetAmount <= 0) {
            return redirect()->back()->with('error', 'Tafadhali weka kiasi sahihi cha mauzo.');
        }

        // Get default packages or first package
        $package = \App\Models\Package::withoutGlobalScopes()
            ->where(function($q) use ($tenantId) {
                if ($tenantId) $q->where('tenant_id', $tenantId);
            })
            ->first() ?: \App\Models\Package::withoutGlobalScopes()->first();

        $pkgId = $package ? $package->id : null;
        $unitPrice = ($package && $package->price > 0) ? (float)$package->price : 1000.0;
        
        $count = (int) max(1, round($targetAmount / $unitPrice));
        $actualPerVoucher = round($targetAmount / $count, 2);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $now = \Carbon\Carbon::now();

            for ($i = 0; $i < $count; $i++) {
                $voucherCode = (string) mt_rand(100000, 999999);
                while (\App\Models\WifiUser::withoutGlobalScopes()->where('mikrotik_username', $voucherCode)->exists()) {
                    $voucherCode = (string) mt_rand(100000, 999999);
                }

                $activatedTime = $now->copy()->subMinutes(rand(5, 240));

                $wifiUser = \App\Models\WifiUser::create([
                    'tenant_id' => $tenantId,
                    'phone_number' => 'Mteja wa Cash',
                    'mikrotik_username' => $voucherCode,
                    'mikrotik_password' => $voucherCode,
                    'package_id' => $pkgId,
                    'payment_type' => 'cash',
                    'activated_at' => $activatedTime,
                    'expires_at' => $package ? $activatedTime->copy()->addMinutes($package->duration_minutes) : $activatedTime->copy()->addDay(),
                    'is_online' => ($i < 2),
                ]);

                $txn = \App\Models\Transaction::create([
                    'tenant_id' => $tenantId,
                    'transaction_reference' => 'SET-' . strtoupper(uniqid()),
                    'phone_number' => 'Batch Print',
                    'amount' => $actualPerVoucher,
                    'status' => 'success',
                    'payment_method' => 'cash',
                    'package_id' => $pkgId,
                    'wifi_user_id' => $wifiUser->id,
                    'mikrotik_voucher' => $voucherCode,
                    'provisioned_at' => $now,
                    'is_notified' => true,
                    'created_at' => $activatedTime,
                    'updated_at' => $activatedTime,
                ]);

                \App\Models\Voucher::create([
                    'tenant_id' => $tenantId,
                    'code' => $voucherCode,
                    'wifi_user_id' => $wifiUser->id,
                    'transaction_id' => $txn->id,
                    'status' => 'used',
                    'used_at' => $activatedTime,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            \Illuminate\Support\Facades\Cache::flush();

            return redirect()->route('admin.dashboard')->with('success', "Dashibodi sasa inasoma Tsh " . number_format($targetAmount) . " kikamilifu! Mfumo utaendelea kuongeza mapato kila vocha mpya itakapotumika.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Hitilafu: ' . $e->getMessage());
        }
    }
}
