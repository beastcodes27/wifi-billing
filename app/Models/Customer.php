<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use \App\Traits\BelongsToTenant;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function wifiUsers()
    {
        return $this->hasMany(WifiUser::class);
    }

    /** Most recent / active hotspot account */
    public function activeWifiUser()
    {
        return $this->hasOne(WifiUser::class)->latestOfMany();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->orderByDesc('created_at');
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ledgers()
    {
        return $this->hasMany(CustomerLedger::class)->orderByDesc('created_at');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTotalDebtAttribute(): float
    {
        return (float) $this->ledgers()->where('type', 'debt')->sum('amount');
    }

    public function getTotalPaidLedgerAttribute(): float
    {
        return (float) $this->ledgers()->where('type', 'payment')->sum('amount');
    }

    public function getLedgerBalanceAttribute(): float
    {
        return $this->total_debt - $this->total_paid_ledger;
    }

    /** Total amount paid across all successful transactions */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->transactions()
            ->where('status', 'success')
            ->sum('amount');
    }

    /** Total data used across all wifi accounts in MB */
    public function getTotalDataUsedMbAttribute(): float
    {
        return (float) $this->wifiUsers()->sum('data_used') / (1024 * 1024);
    }

    /** Is any wifi account currently online */
    public function getIsOnlineAttribute(): bool
    {
        return $this->wifiUsers()->where('is_online', true)->exists();
    }

    /** Current package from most recent wifi user */
    public function getCurrentPackageAttribute()
    {
        return optional($this->wifiUsers()->with('package')->latest()->first())->package;
    }

    /** Expiry from most recent wifi user */
    public function getExpiresAtAttribute()
    {
        return optional($this->wifiUsers()->latest()->first())->expires_at;
    }

    /** Status badge colour */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'    => '#10b981',
            'expired'   => '#f59e0b',
            'suspended' => '#ef4444',
            'blocked'   => '#6b7280',
            default     => '#6b7280',
        };
    }

    /** Status badge background */
    public function getStatusBgAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'rgba(16,185,129,0.1)',
            'expired'   => 'rgba(245,158,11,0.1)',
            'suspended' => 'rgba(239,68,68,0.1)',
            'blocked'   => 'rgba(107,114,128,0.1)',
            default     => 'rgba(107,114,128,0.1)',
        };
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('phone_number', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
