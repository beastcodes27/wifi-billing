<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M1 — Add commercial fields to packages table.
     * Non-destructive: only ADD columns, nothing removed or changed.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Human-readable description shown in portal & admin
            $table->text('description')->nullable()->after('mikrotik_profile');

            // Speed limits (stored as strings like "10Mbps" to match MikroTik profiles)
            $table->string('download_speed', 50)->nullable()->after('description')
                  ->comment('e.g. 10Mbps — mirrors MikroTik profile speed limit');
            $table->string('upload_speed', 50)->nullable()->after('download_speed')
                  ->comment('e.g. 5Mbps');

            // Data quota in MB. NULL = Unlimited.
            $table->unsignedBigInteger('data_quota_mb')->nullable()->after('upload_speed')
                  ->comment('NULL means unlimited data');

            // Display order in portal (lower = shown first)
            $table->unsignedSmallInteger('display_order')->default(0)->after('data_quota_mb')
                  ->comment('Sort order for portal listing');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'download_speed',
                'upload_speed',
                'data_quota_mb',
                'display_order',
            ]);
        });
    }
};
