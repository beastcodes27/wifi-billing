<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M5 — Create subscriptions table.
     *
     * Architecture: Event-based subscription history.
     *
     * Each row = one subscription EVENT in a customer's billing history.
     * The "current" subscription = latest active record per customer.
     * History = ALL records for that customer ordered by created_at.
     *
     * This supports the full lifecycle:
     *   new → renewal → extension → package_change → cancellation → expiry
     *
     * Expiry Calculation Rule (configurable via SubscriptionService):
     *   Default: new_expiry = MAX(current_expiry, NOW()) + package_duration
     *   i.e. renewals STACK on top of existing remaining time.
     *
     * IMPORTANT: Renewals do NOT overwrite previous subscription rows.
     * A new row is inserted for each event, preserving full history.
     *
     * Example:
     *   id=1, type=new,    package=A, status=expired  → Subscription #1 expired
     *   id=2, type=renewal, package=A, status=expired → Subscription #2 renewed & expired
     *   id=3, type=package_change, package=B, status=active → Changed to package B
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // The customer this subscription belongs to
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->cascadeOnDelete()
                  ->comment('The subscribing customer.');

            // The hotspot account linked to this subscription
            $table->foreignId('wifi_user_id')
                  ->nullable()
                  ->constrained('wifi_users')
                  ->nullOnDelete()
                  ->comment('The MikroTik hotspot account for this subscription period.');

            // The package purchased
            $table->foreignId('package_id')
                  ->constrained('packages')
                  ->restrictOnDelete()
                  ->comment('Package purchased. Restricted delete to preserve billing history.');

            // The payment that funded this subscription (nullable for manual/admin-created)
            $table->foreignId('transaction_id')
                  ->nullable()
                  ->constrained('transactions')
                  ->nullOnDelete()
                  ->comment('The payment transaction that funded this subscription. NULL = admin-created.');

            // Event type — each renewal/change creates a new row
            // Controlled values: new, renewal, extension, package_change, cancellation, expiry
            $table->string('type', 30)->default('new')
                  ->comment('Subscription event: new, renewal, extension, package_change, cancellation, expiry');

            // Status of THIS subscription event row
            // Controlled values: active, expired, cancelled, suspended
            $table->string('status', 30)->default('active')
                  ->comment('Status of this subscription record: active, expired, cancelled, suspended');

            // Billing period
            $table->timestamp('starts_at')
                  ->comment('When this subscription period starts.');
            $table->timestamp('expires_at')->nullable()
                  ->comment('When this subscription period expires. NULL = no expiry (admin-set).');

            // Who created this record (NULL = auto-created from payment)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin who created/modified this subscription. NULL = auto from payment.');

            // Optional notes for this subscription event
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index('customer_id', 'subs_customer_index');
            $table->index('wifi_user_id', 'subs_wifi_user_index');
            $table->index('status', 'subs_status_index');
            $table->index('expires_at', 'subs_expires_at_index');
            $table->index(['customer_id', 'status'], 'subs_customer_status_index');
            $table->index(['customer_id', 'expires_at'], 'subs_customer_expiry_index');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
