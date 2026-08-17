<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M8 — Create audit_logs table.
     *
     * Records all critical admin actions for traceability.
     *
     * Security decisions:
     * - old_values / new_values: JSON fields. Application layer MUST sanitize
     *   these before storing — passwords, API keys, and secrets must NEVER
     *   appear here. Use an AuditLogger helper/trait that strips sensitive fields.
     *
     * - No updated_at: audit records are immutable once written.
     *
     * - No soft deletes: audit logs should never be soft-deleted.
     *   They can be archived after a retention period, but never removed silently.
     *
     * Tracked actions (examples):
     *   customer.created, customer.updated, customer.deleted, customer.suspended
     *   payment.created, payment.refunded, payment.manual_adjustment
     *   subscription.created, subscription.renewed, subscription.cancelled
     *   voucher.generated, voucher.used, voucher.cancelled
     *   package.created, package.updated, package.deleted
     *   router.created, router.updated, router.connection_tested
     *   admin.login, admin.logout, admin.login_failed
     *   settings.updated
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action (NULL = system-initiated action, e.g. expiry cleanup)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin who performed the action. NULL = system/automated action.');

            // Action identifier — dot-notation e.g. "customer.created", "payment.refunded"
            $table->string('action', 60)
                  ->comment('Action identifier in dot-notation: module.verb');

            // Module for grouping/filtering
            $table->string('module', 30)
                  ->comment('e.g. customers, payments, subscriptions, vouchers, routers, admin');

            // The ID of the affected record (e.g. customer_id=15)
            $table->unsignedBigInteger('record_id')->nullable()
                  ->comment('ID of the affected record.');

            // The model/class of the affected record (for polymorphic lookups)
            $table->string('record_type', 80)->nullable()
                  ->comment('e.g. App\\Models\\Customer — for polymorphic record links.');

            // State before and after (MUST NOT contain passwords or secrets)
            $table->json('old_values')->nullable()
                  ->comment('Record state before action. Sensitive fields stripped at app layer.');
            $table->json('new_values')->nullable()
                  ->comment('Record state after action. Sensitive fields stripped at app layer.');

            // Request context
            $table->string('ip_address', 45)->nullable()
                  ->comment('IP address of the request. Supports IPv6.');
            $table->text('user_agent')->nullable();

            // Human-readable summary for display
            $table->text('description')->nullable()
                  ->comment('Human-readable summary of the action for display in audit UI.');

            // Immutable timestamp — no updated_at
            $table->timestamp('created_at')->useCurrent();

            // Indexes for filtering/reporting
            $table->index('user_id', 'audit_user_index');
            $table->index('action', 'audit_action_index');
            $table->index('module', 'audit_module_index');
            $table->index(['record_type', 'record_id'], 'audit_record_index');
            $table->index('created_at', 'audit_created_at_index');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
