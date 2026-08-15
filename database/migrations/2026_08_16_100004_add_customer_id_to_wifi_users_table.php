<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M4 — Add customer relationship and network context to wifi_users.
     *
     * Design decisions:
     * - customer_id: nullable — existing 62 cash vouchers remain unlinked (safe).
     *   New vouchers created from a customer purchase will be linked.
     * - router_id, site_id: nullable — existing records have no router context yet.
     * - payment_type: tracks how this wifi account was funded.
     * - No softDeletes on wifi_users — deletion handled via MikroTik cleanup command
     *   (nullifying mikrotik_username already marks removed accounts).
     * - No cascade delete on customer_id — if customer is soft-deleted,
     *   their wifi_users remain for history.
     */
    public function up(): void
    {
        Schema::table('wifi_users', function (Blueprint $table) {
            // Link to the customer who owns this hotspot account.
            // NULL is valid for existing cash vouchers and for accounts
            // not yet matched to a customer.
            $table->foreignId('customer_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('customers')
                  ->nullOnDelete()   // Soft-deleted customer → keep wifi_user
                  ->comment('The customer who owns this hotspot account. NULL = unlinked cash voucher.');

            // Which router this account is provisioned on
            $table->foreignId('router_id')
                  ->nullable()
                  ->after('package_id')
                  ->constrained('routers')
                  ->nullOnDelete()
                  ->comment('Router where this account is/was provisioned.');

            // Which site (denormalized for fast reporting — router FK also covers this)
            $table->foreignId('site_id')
                  ->nullable()
                  ->after('router_id')
                  ->constrained('sites')
                  ->nullOnDelete()
                  ->comment('Site context — kept in sync with router.site_id.');

            // How this wifi account was funded
            // Controlled values: online, cash, manual
            $table->string('payment_type', 20)->default('cash')
                  ->after('site_id')
                  ->comment('Controlled values: online (Palm Pesa etc), cash, manual');

            // Indexes for common dashboard queries
            $table->index('customer_id', 'wifi_users_customer_index');
            $table->index('router_id', 'wifi_users_router_index');
            $table->index('site_id', 'wifi_users_site_index');
            $table->index('payment_type', 'wifi_users_payment_type_index');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('wifi_users', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['router_id']);
            $table->dropForeign(['site_id']);
            $table->dropIndex('wifi_users_customer_index');
            $table->dropIndex('wifi_users_router_index');
            $table->dropIndex('wifi_users_site_index');
            $table->dropIndex('wifi_users_payment_type_index');
            $table->dropColumn(['customer_id', 'router_id', 'site_id', 'payment_type']);
        });
    }
};
