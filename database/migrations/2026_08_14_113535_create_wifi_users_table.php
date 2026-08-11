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
        Schema::create('wifi_users', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->index();
            $table->string('mac_address')->nullable();
            $table->string('mikrotik_username')->unique();
            $table->string('mikrotik_password')->nullable();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wifi_users');
    }
};
