<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M3 — Create customers table.
     *
     * Customers are business/CRM entities (people who pay).
     * wifi_users are their network/hotspot accounts (linked in M4).
     *
     * Design decisions:
     * - phone_number: unique index but nullable-safe (checked in app layer)
     * - email: nullable, NO unique index (MySQL allows multiple NULLs with unique,
     *   but we avoid unique here for flexibility — app layer enforces uniqueness)
     * - status: string, not enum, for future expansion
     * - softDeletes: customers are never hard-deleted; payment/billing history preserved
     * - created_by: nullable FK to users — NULL means auto-created (e.g. first payment)
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name')->nullable()->comment('Full name — optional for voucher-only customers');
            $table->string('phone_number', 20)->nullable()
                  ->comment('Primary identifier. Normalized to 255XXXXXXXXX format.');
            $table->string('email')->nullable()
                  ->comment('Optional email. Not unique at DB level; app enforces uniqueness.');

            // Account status — string for flexibility
            // Controlled values: active, expired, suspended, blocked
            $table->string('status', 30)->default('active')
                  ->comment('Controlled values: active, expired, suspended, blocked');

            // Optional notes from admin
            $table->text('notes')->nullable();

            // Who created this customer record
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Admin who created the record. NULL = auto-created from payment.');

            // Soft delete — billing history is preserved even after "deletion"
            $table->softDeletes();
            $table->timestamps();

            // Indexes for common queries
            $table->index('phone_number', 'customers_phone_index');
            $table->index('status', 'customers_status_index');
            $table->index('created_at', 'customers_created_at_index');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
