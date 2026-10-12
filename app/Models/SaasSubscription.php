<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasSubscription extends Model
{
    protected $fillable = [
        'tenant_id',
        'saas_plan_id',
        'amount_paid',
        'payment_method',
        'reference_number',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(SaasPlan::class, 'saas_plan_id');
    }

    public function isActive()
    {
        return in_array($this->status, ['active', 'trial'], true)
            && $this->ends_at
            && $this->ends_at->isFuture();
    }

    public function isTrial()
    {
        return $this->status === 'trial';
    }

    /**
     * Mark the subscription as active and extend the tenant access.
     */
    public function activate(): void
    {
        $months = optional($this->plan)->billing_cycle_months ?: 1;

        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonths($months),
        ]);

        if ($this->tenant) {
            $this->tenant->update(['status' => 'active']);
        }
    }

    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
