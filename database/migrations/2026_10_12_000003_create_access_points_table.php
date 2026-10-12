<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('brand')->default('TP-Link Omada'); // MikroTik, Ubiquiti UniFi, TP-Link Omada
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('device_key')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('management_ip')->nullable();
            $table->string('adoption_status')->default('pending'); // pending, adopted, failed
            $table->string('adoption_method')->nullable(); // manual, ai_scan, controller
            $table->decimal('scan_confidence', 5, 2)->nullable();
            $table->json('scan_raw')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('adopted_at')->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('serial_number');
            $table->index('adoption_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_points');
    }
};
