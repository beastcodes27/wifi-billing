<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
    ];

    public function adminUsers()
    {
        // Users table does not have a global scope currently, but if it did, 
        // we might need to use withoutGlobalScopes() if calling from superadmin.
        // For now, this standard relationship will work since the User model 
        // isn't scoped by default to tenant_id on auth.
        return $this->hasMany(User::class);
    }

    public function saasSubscriptions()
    {
        return $this->hasMany(SaasSubscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(SaasSubscription::class)->latest('ends_at');
    }

    /**
     * The subscription granting access right now (active or in-trial, not expired).
     */
    public function activeSubscription(): ?SaasSubscription
    {
        return $this->saasSubscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->where('ends_at', '>', now())
            ->orderByDesc('ends_at')
            ->first();
    }

    public function hasActiveAccess(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function isOnTrial(): bool
    {
        $sub = $this->activeSubscription();

        return $sub !== null && $sub->status === 'trial';
    }

    public function trialEndsAt()
    {
        $sub = $this->activeSubscription();

        return ($sub && $sub->status === 'trial') ? $sub->ends_at : null;
    }
}
