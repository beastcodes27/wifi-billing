<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M7 — Add commercial/extensible fields to transactions table.
     *
     * CRITICAL: No existing columns are removed or changed.
     * The existing Palm Pesa payment flow must continue working unchanged.
     *
     * Architecture — Extensible Payment Gateway:
     * - payment_method: identifies which gateway/method processed the payment.
     *   String field (not enum) so new gateways can be added without migration.
     *   Controlled values: palm_pesa, mpesa, airtel, halo, mixx, cash, manual, other
     * - payment_gateway: optional free-text gateway name for future integrations.
     * - gateway_transaction_id: the gateway's own transaction ID (for reconciliation).
     *
     * Each gateway is identified by payment_method. Core billing logic remains unchanged.
     * To add a new gateway, create a new Gateway class implementing a PaymentGateway interface.
     * No core table changes required.
     *
     * FK behaviour:
     * - customer_id: nullOnDelete — transaction history survives customer soft-delete.
     * - router_id, site_id: nullOnDelete — context preserved even if infra changes.
     * - created_by: nullOnDelete — manual payments keep record even if admin removed.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // ── Payment Gateway Architecture ──────────────────────────────────
            // Identifies which payment method was used.
            // String (not enum) for future gateway additions without migrations.
            // Controlled values: palm_pesa, mpesa, airtel, halo, mixx, cash, manual, other
            $table->string('payment_method', 30)->nullable()->after('status')
                  ->comment('Gateway: palm_pesa, mpesa, airtel, halo, mixx, cash, manual, other');

            // Optional free-text gateway name (for future extensibility)
            $table->string('payment_gateway', 60)->nullable()->after('payment_method')
                  ->comment('Human-readable gateway name. Used for future gateways.');

            // The gateway\'s own transaction/order reference (for reconciliation)
            $table->string('gateway_transaction_id', 100)->nullable()->after('payment_gateway')
                  ->comment('The payment gateway\'s own transaction ID for reconciliation.');

            // ── Relational Context ────────────────────────────────────────────
            // Link to customer (NULL for cash/anonymous transactions)
            $table->foreignId('customer_id')
                  ->nullable()
                  ->after('wifi_user_id')
                  ->constrained('customers')
                  ->nullOnDelete()
                  ->comment('The customer who made this payment. NULL = anonymous/cash.');

            // Router and site context for multi-site revenue reporting
            $table->foreignId('router_id')
                  ->nullable()
                  ->after('customer_id')
                  ->constrained('routers')
                  ->nullOnDelete()
                  ->comment('Router context for site-based revenue reporting.');

            $table->foreignId('site_id')
                  ->nullable()
                  ->after('router_id')
                  ->constrained('sites')
                  ->nullOnDelete()
                  ->comment('Site context for site-based revenue reporting.');

            // Who created this for manual/cash payments (NULL = auto-created from gateway)
            $table->foreignId('created_by')
                  ->nullable()
                  ->after('site_id')
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin who created manual/cash transaction. NULL = gateway-initiated.');

            // Admin notes (e.g. reason for manual adjustment, refund reason)
            $table->text('notes')->nullable()->after('created_by');

            // ── Indexes ───────────────────────────────────────────────────────
            $table->index('payment_method', 'txn_payment_method_index');
            $table->index('customer_id', 'txn_customer_index');
            $table->index('gateway_transaction_id', 'txn_gateway_txn_id_index');
            $table->index('router_id', 'txn_router_index');
            $table->index('site_id', 'txn_site_index');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['router_id']);
            $table->dropForeign(['site_id']);
            $table->dropForeign(['created_by']);
            $table->dropIndex('txn_payment_method_index');
            $table->dropIndex('txn_customer_index');
            $table->dropIndex('txn_gateway_txn_id_index');
            $table->dropIndex('txn_router_index');
            $table->dropIndex('txn_site_index');
            $table->dropColumn([
                'payment_method',
                'payment_gateway',
                'gateway_transaction_id',
                'customer_id',
                'router_id',
                'site_id',
                'created_by',
                'notes',
            ]);
        });
    }
};
