<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_users', function (Blueprint $table) {
            // Timestamp when user was removed from MikroTik after their time expired
            $table->timestamp('removed_at')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('wifi_users', function (Blueprint $table) {
            $table->dropColumn('removed_at');
        });
    }
};
