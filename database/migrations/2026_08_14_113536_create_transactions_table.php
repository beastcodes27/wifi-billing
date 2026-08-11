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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();
            $table->string('phone_number');
            $table->decimal('amount', 10, 2);
            $table->string('status'); // pending, success, failed
            $table->foreignId('package_id')->constrained();
            $table->foreignId('wifi_user_id')->nullable()->constrained();
            $table->text('palm_pesa_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
