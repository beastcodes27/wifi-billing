<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'billing_cycle_months',
        'max_routers',
        'max_customers',
        'is_active',
        'is_public',
        'trial_days',
        'has_auto_payment',
    ];

    protected $casts = [
        'has_auto_payment' => 'boolean',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(SaasSubscription::class);
    }

    /**
     * Plans that may be self-selected by businesses during registration.
     */
    public function scopePubliclySelectable($query)
    {
        return $query->where('is_active', true)->where('is_public', true);
    }
}
