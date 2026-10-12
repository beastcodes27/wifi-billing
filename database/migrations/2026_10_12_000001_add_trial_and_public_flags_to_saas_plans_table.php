<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add SaaS trial + public-visibility flags to plans.
     */
    public function up(): void
    {
        Schema::table('saas_plans', function (Blueprint $table) {
            $table->integer('trial_days')->default(30)->after('billing_cycle_months');
            $table->boolean('is_public')->default(true)->after('is_active');
        });

        // The system-owner plan must never be offered to self-registering businesses.
        DB::table('saas_plans')
            ->where('name', 'Unlimited (System Owner)')
            ->update(['is_public' => false, 'trial_days' => 0]);
    }

    public function down(): void
    {
        Schema::table('saas_plans', function (Blueprint $table) {
            $table->dropColumn(['trial_days', 'is_public']);
        });
    }
};
