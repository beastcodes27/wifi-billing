<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wifi_users', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('expires_at');
            $table->bigInteger('data_used')->default(0)->after('is_online')->comment('Bytes used');
            $table->string('session_time')->nullable()->after('data_used');
            $table->timestamp('last_logout_at')->nullable()->after('session_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wifi_users', function (Blueprint $table) {
            $table->dropColumn(['is_online', 'data_used', 'session_time', 'last_logout_at']);
        });
    }
};
