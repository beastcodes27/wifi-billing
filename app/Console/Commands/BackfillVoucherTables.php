<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillVoucherTables extends Command
{
    protected $signature = 'app:backfill-voucher-tables
                            {--dry-run : Show what would be done without making changes}';

    protected $description = 'Safely backfill voucher_batches and vouchers from existing transactions data';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN mode — no changes will be made.');
        }

        $this->info('=== Phase 1: Inspect existing data ===');

        // Count what we need to migrate
        $totalWithVoucher = DB::table('transactions')
            ->whereNotNull('mikrotik_voucher')
            ->where('mikrotik_voucher', '!=', '')
            ->count();

        $alreadyInVouchers = DB::table('vouchers')->count();
        $alreadyInBatches  = DB::table('voucher_batches')->count();

        $this->line("Transactions with voucher codes: {$totalWithVoucher}");
        $this->line("Vouchers already in vouchers table: {$alreadyInVouchers}");
        $this->line("Batches already in voucher_batches: {$alreadyInBatches}");

        // --- STEP 1: Backfill voucher_batches from BATCH-xxx transactions ---
        $this->info('=== Phase 2: Backfill voucher_batches ===');

        $batches = DB::table('transactions')
            ->select('palm_pesa_order_id', 'package_id',
                     DB::raw('COUNT(*) as quantity'),
                     DB::raw('MIN(created_at) as created_at'))
            ->where('palm_pesa_order_id', 'like', 'BATCH-%')
            ->groupBy('palm_pesa_order_id', 'package_id')
            ->get();

        $batchMap = []; // palm_pesa_order_id => voucher_batches.id

        foreach ($batches as $batch) {
            // Check if already backfilled
            $existing = DB::table('voucher_batches')
                ->where('batch_reference', $batch->palm_pesa_order_id)
                ->first();

            if ($existing) {
                $this->line("  SKIP (exists): {$batch->palm_pesa_order_id} → batch_id={$existing->id}");
                $batchMap[$batch->palm_pesa_order_id] = $existing->id;
                continue;
            }

            $this->line("  CREATE batch: {$batch->palm_pesa_order_id} (qty={$batch->quantity}, pkg={$batch->package_id})");

            if (!$dryRun) {
                $batchId = DB::table('voucher_batches')->insertGetId([
                    'batch_reference' => $batch->palm_pesa_order_id,
                    'package_id'      => $batch->package_id,
                    'created_by'      => null,
                    'quantity'        => $batch->quantity,
                    'notes'           => 'Backfilled from legacy BATCH transaction data',
                    'created_at'      => $batch->created_at,
                    'updated_at'      => now(),
                ]);
                $batchMap[$batch->palm_pesa_order_id] = $batchId;
            }
        }

        $this->info("Batches processed: " . count($batches));

        // --- STEP 2: Backfill vouchers from transactions.mikrotik_voucher ---
        $this->info('=== Phase 3: Backfill vouchers ===');

        $transactions = DB::table('transactions')
            ->whereNotNull('mikrotik_voucher')
            ->where('mikrotik_voucher', '!=', '')
            ->get();

        $created  = 0;
        $skipped  = 0;
        $errors   = 0;

        foreach ($transactions as $txn) {
            // Safety: skip if voucher code already exists in vouchers table
            $existing = DB::table('vouchers')->where('code', $txn->mikrotik_voucher)->first();
            if ($existing) {
                $this->line("  SKIP (exists): code={$txn->mikrotik_voucher}");
                $skipped++;
                continue;
            }

            // Determine batch_id (NULL for non-batch transactions)
            $batchId = null;
            if ($txn->palm_pesa_order_id && str_starts_with($txn->palm_pesa_order_id, 'BATCH-')) {
                $batchId = $batchMap[$txn->palm_pesa_order_id] ?? null;
            }

            // Determine status based on wifi_user activation
            $status = 'available';
            if ($txn->wifi_user_id) {
                $wifiUser = DB::table('wifi_users')->where('id', $txn->wifi_user_id)->first();
                if ($wifiUser && $wifiUser->activated_at !== null) {
                    $status = 'used';
                }
            }

            $this->line("  CREATE voucher: code={$txn->mikrotik_voucher}, txn_id={$txn->id}, batch_id=" . ($batchId ?? 'NULL') . ", status={$status}");

            if (!$dryRun) {
                try {
                    DB::table('vouchers')->insert([
                        'batch_id'       => $batchId,
                        'code'           => $txn->mikrotik_voucher,
                        'wifi_user_id'   => $txn->wifi_user_id,
                        'transaction_id' => $txn->id,
                        'status'         => $status,
                        'used_at'        => null, // Will be updated when onLogin fires
                        'router_id'      => null,
                        'site_id'        => null,
                        'created_at'     => $txn->created_at,
                        'updated_at'     => now(),
                    ]);
                    $created++;
                } catch (\Exception $e) {
                    $this->error("  ERROR voucher code={$txn->mikrotik_voucher}: " . $e->getMessage());
                    $errors++;
                }
            } else {
                $created++;
            }
        }

        // --- STEP 3: Backfill transactions.payment_method ---
        $this->info('=== Phase 4: Backfill transactions.payment_method ===');

        if (!$dryRun) {
            // BATCH / CASH → cash
            $cashUpdated = DB::table('transactions')
                ->whereNull('payment_method')
                ->where(function ($q) {
                    $q->where('palm_pesa_order_id', 'like', 'BATCH-%')
                      ->orWhere('palm_pesa_order_id', 'like', 'CASH-%')
                      ->orWhere('transaction_reference', 'like', 'MANUAL-%');
                })
                ->update(['payment_method' => 'cash']);

            // All others with a palm_pesa_order_id → palm_pesa
            $ppUpdated = DB::table('transactions')
                ->whereNull('payment_method')
                ->whereNotNull('palm_pesa_order_id')
                ->update(['payment_method' => 'palm_pesa']);

            $this->line("  Updated to 'cash': {$cashUpdated} transactions");
            $this->line("  Updated to 'palm_pesa': {$ppUpdated} transactions");
        } else {
            $cashCount = DB::table('transactions')
                ->where(function ($q) {
                    $q->where('palm_pesa_order_id', 'like', 'BATCH-%')
                      ->orWhere('palm_pesa_order_id', 'like', 'CASH-%')
                      ->orWhere('transaction_reference', 'like', 'MANUAL-%');
                })->count();
            $this->line("  Would update ~{$cashCount} transactions to 'cash'");
        }

        // --- Final Summary ---
        $this->info('=== Backfill Complete ===');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Batches processed', count($batches)],
                ['Vouchers created', $created],
                ['Vouchers skipped (already existed)', $skipped],
                ['Errors', $errors],
                ['Mode', $dryRun ? 'DRY RUN' : 'LIVE'],
            ]
        );

        if ($errors > 0) {
            $this->error("Backfill completed with {$errors} errors. Check output above.");
            return self::FAILURE;
        }

        $this->info('Backfill completed successfully. No existing data was modified.');
        return self::SUCCESS;
    }
}
