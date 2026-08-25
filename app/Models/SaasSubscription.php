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
        return $this->status === 'active' && $this->ends_at->isFuture();
    }
}
