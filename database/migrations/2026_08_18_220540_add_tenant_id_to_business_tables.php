<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'sites',
            'routers',
            'packages',
            'customers',
            'wifi_users',
            'transactions',
            'vouchers',
            'voucher_batches',
            'subscriptions',
            'audit_logs',
            'customer_ledgers'
        ];

        $tenantId = DB::table('tenants')->where('business_name', 'Medstar Sys')->value('id');
        if (!$tenantId) {
            $tenantId = DB::table('tenants')->insertGetId([
                'business_name' => 'Medstar Sys',
                'owner_name'    => 'Admin',
                'status'        => 'active',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // 2. Add column and migrate data
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                });
                
                DB::table($tableName)->update(['tenant_id' => $tenantId]);
                
                // Now that data is populated, make it not null and add foreign key
                Schema::table($tableName, function (Blueprint $table) {
                    // Requires doctrine/dbal if using older Laravel, but let's try direct foreign key first
                    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                });
                
                // Set to not null natively
                DB::statement("ALTER TABLE `{$tableName}` MODIFY `tenant_id` BIGINT UNSIGNED NOT NULL;");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'sites',
            'routers',
            'packages',
            'customers',
            'wifi_users',
            'transactions',
            'vouchers',
            'voucher_batches',
            'subscriptions',
            'audit_logs',
            'customer_ledgers'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Drop foreign key first (naming convention: table_tenant_id_foreign)
                    // We must drop it by column in Laravel if we didn't specify name
                    $table->dropForeign([ 'tenant_id' ]);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};

