<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\WifiUser;
use App\Models\Transaction;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SetDashboardSalesCommand extends Command
{
    protected $signature = 'app:set-sales {amount=10000} {--tenant=1}';
    protected $description = 'Set dashboard sales/revenue baseline by generating activated voucher transactions';

    public function handle()
    {
        $amount = (float) $this->argument('amount');
        $tenantId = (int) $this->option('tenant');

        if ($amount <= 0) {
            $this->error('Amount must be greater than 0.');
            return 1;
        }

        $package = Package::withoutGlobalScopes()
            ->where(function($q) use ($tenantId) {
                if ($tenantId) $q->where('tenant_id', $tenantId);
            })
            ->first() ?: Package::withoutGlobalScopes()->first();

        $pkgId = $package ? $package->id : null;
        $unitPrice = ($package && $package->price > 0) ? (float)$package->price : 1000.0;
        
        $count = (int) max(1, round($amount / $unitPrice));
        $actualPerVoucher = round($amount / $count, 2);

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            for ($i = 0; $i < $count; $i++) {
                $voucherCode = (string) mt_rand(100000, 999999);
                while (WifiUser::withoutGlobalScopes()->where('mikrotik_username', $voucherCode)->exists()) {
                    $voucherCode = (string) mt_rand(100000, 999999);
                }

                $activatedTime = $now->copy()->subMinutes(rand(5, 240));

                $wifiUser = WifiUser::create([
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

                $txn = Transaction::create([
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

                Voucher::create([
                    'tenant_id' => $tenantId,
                    'code' => $voucherCode,
                    'wifi_user_id' => $wifiUser->id,
                    'transaction_id' => $txn->id,
                    'status' => 'used',
                    'used_at' => $activatedTime,
                ]);
            }

            DB::commit();
            \Illuminate\Support\Facades\Cache::flush();

            $this->info("Successfully set dashboard sales to Tsh " . number_format($amount) . " ({$count} vouchers generated).");
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Failed: ' . $e->getMessage());
            return 1;
        }
    }
}
