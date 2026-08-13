<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M2 — Add commercial/API fields to routers table.
     * Non-destructive: only ADD columns.
     * 
     * api_password: stored encrypted via Laravel's encrypt()/decrypt().
     *               NEVER stored or returned as plain text.
     * status: string (not enum) for future flexibility.
     */
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            // RouterOS API connection details
            $table->string('api_host')->nullable()->after('management_ip')
                  ->comment('RouterOS API hostname or IP (may differ from management_ip)');
            $table->unsignedSmallInteger('api_port')->default(8728)->after('api_host')
                  ->comment('RouterOS API port (default 8728, TLS: 8729)');
            $table->string('api_username')->nullable()->after('api_port');
            // Encrypted — use encrypt()/decrypt() in application layer
            $table->text('api_password_encrypted')->nullable()->after('api_username')
                  ->comment('Stored encrypted via Laravel Crypt. Never plain text.');

            // Router status — string not enum, for easy future expansion
            // Controlled values: online, offline, unknown, maintenance
            $table->string('status', 30)->default('unknown')->after('api_password_encrypted')
                  ->comment('Controlled values: online, offline, unknown, maintenance');
            $table->index('status', 'routers_status_index');

            // Timestamps for monitoring
            $table->timestamp('last_seen_at')->nullable()->after('status')
                  ->comment('Last successful API connection timestamp');
            $table->timestamp('last_synced_at')->nullable()->after('last_seen_at')
                  ->comment('Last provisioning sync timestamp');
            $table->text('last_error')->nullable()->after('last_synced_at')
                  ->comment('Last connection error message for diagnostics');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropIndex('routers_status_index');
            $table->dropColumn([
                'api_host',
                'api_port',
                'api_username',
                'api_password_encrypted',
                'status',
                'last_seen_at',
                'last_synced_at',
                'last_error',
            ]);
        });
    }
};
