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
        'has_auto_payment',
    ];

    protected $casts = [
        'has_auto_payment' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(SaasSubscription::class);
    }
}
