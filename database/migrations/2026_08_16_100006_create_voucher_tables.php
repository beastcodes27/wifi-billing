<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M6 — Create voucher_batches and vouchers tables.
     *
     * Design decisions:
     * - voucher_batches: Represents a group of vouchers printed together.
     *   Legacy batches (BATCH-xxx from palm_pesa_order_id) will be backfilled
     *   here but their original transaction values will NOT be changed.
     *
     * - vouchers: One record per voucher code. Links to:
     *   batch (optional) → wifi_user (network account) → transaction (payment)
     *   Tracks: who used it, when, on which router/site.
     *
     * - Existing transactions.mikrotik_voucher values will be backfilled
     *   into vouchers.code via a separate Artisan command (no live data changed here).
     *
     * - onDelete behaviour:
     *   batch_id: nullOnDelete — deleting a batch doesn't delete individual vouchers
     *   wifi_user_id: nullOnDelete — keep voucher record even if hotspot account cleaned up
     *   transaction_id: nullOnDelete — keep voucher record if transaction is cleared
     *   router_id / site_id: nullOnDelete — keep history if router/site removed
     */
    public function up(): void
    {
        // ── voucher_batches ──────────────────────────────────────────────────
        Schema::create('voucher_batches', function (Blueprint $table) {
            $table->id();

            // Unique reference (e.g. BATCH-6A7FF82AE036E — preserved from legacy data)
            $table->string('batch_reference', 60)->unique()
                  ->comment('Unique batch identifier. Preserved from legacy BATCH-xxx references.');

            $table->foreignId('package_id')
                  ->constrained('packages')
                  ->restrictOnDelete()
                  ->comment('Package all vouchers in this batch are for.');

            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin who generated the batch.');

            $table->unsignedInteger('quantity')->default(1)
                  ->comment('Total number of vouchers in this batch.');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('package_id', 'vbatch_package_index');
            $table->index('created_at', 'vbatch_created_at_index');
        });

        // ── vouchers ─────────────────────────────────────────────────────────
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();

            // Batch this voucher belongs to (NULL = standalone/online voucher)
            $table->foreignId('batch_id')
                  ->nullable()
                  ->constrained('voucher_batches')
                  ->nullOnDelete()
                  ->comment('The batch this voucher was generated in. NULL = standalone voucher.');

            // The 5-6 digit code
            $table->string('code', 20)->unique()
                  ->comment('The voucher/hotspot login code. Must be unique system-wide.');

            // Linked network account
            $table->foreignId('wifi_user_id')
                  ->nullable()
                  ->constrained('wifi_users')
                  ->nullOnDelete()
                  ->comment('The hotspot account associated with this voucher.');

            // Linked payment
            $table->foreignId('transaction_id')
                  ->nullable()
                  ->constrained('transactions')
                  ->nullOnDelete()
                  ->comment('The transaction that generated this voucher.');

            // Status — string for flexibility
            // Controlled values: available, used, expired, cancelled
            $table->string('status', 20)->default('available')
                  ->comment('Voucher lifecycle: available, used, expired, cancelled');

            // When and where it was used
            $table->timestamp('used_at')->nullable()
                  ->comment('When the voucher was first activated/used.');

            $table->foreignId('router_id')
                  ->nullable()
                  ->constrained('routers')
                  ->nullOnDelete()
                  ->comment('Router where voucher was activated.');

            $table->foreignId('site_id')
                  ->nullable()
                  ->constrained('sites')
                  ->nullOnDelete()
                  ->comment('Site where voucher was activated.');

            $table->timestamps();

            // Indexes for lookups, reports, filtering
            $table->index('code', 'vouchers_code_index');
            $table->index('status', 'vouchers_status_index');
            $table->index('batch_id', 'vouchers_batch_index');
            $table->index('used_at', 'vouchers_used_at_index');
            $table->index('router_id', 'vouchers_router_index');
            $table->index('site_id', 'vouchers_site_index');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('voucher_batches');
    }
};
