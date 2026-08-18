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
        Schema::create('saas_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('saas_plan_id')->constrained('saas_plans')->onDelete('restrict');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_method')->nullable(); // e.g. Mpesa, TigoPesa, Cash
            $table->string('reference_number')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->timestamps();
        });

        // Insert Default Unlimited Plan for the Owner (Tenant #1)
        $planId = \Illuminate\Support\Facades\DB::table('saas_plans')->insertGetId([
            'name' => 'Unlimited (System Owner)',
            'description' => 'Unlimited access for the original system owner.',
            'price' => 0,
            'billing_cycle_months' => 1200, // 100 years
            'max_routers' => 999999,
            'max_customers' => 999999,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Subscribe Tenant #1
        $tenant = \Illuminate\Support\Facades\DB::table('tenants')->find(1);
        if ($tenant) {
            \Illuminate\Support\Facades\DB::table('saas_subscriptions')->insert([
                'tenant_id' => 1,
                'saas_plan_id' => $planId,
                'amount_paid' => 0,
                'payment_method' => 'System',
                'reference_number' => 'OWNER-LIFETIME',
                'starts_at' => now(),
                'ends_at' => now()->addYears(100),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saas_subscriptions');
    }
};
