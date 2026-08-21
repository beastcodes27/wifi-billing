<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WifiUser extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'phone_number',
        'mac_address',
        'ip_address',
        'mikrotik_username',
        'mikrotik_password',
        'package_id',
        'router_id',
        'site_id',
        'payment_type',
        'expires_at',
        'activated_at',
        'is_online',
        'data_used',
        'session_time',
        'last_logout_at',
    ];

    protected $casts = [
        'is_online'      => 'boolean',
        'expires_at'     => 'datetime',
        'activated_at'   => 'datetime',
        'last_logout_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function package()
    {
        return $this->belongsTo(Package::class)->withoutGlobalScopes();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getDataUsedHumanAttribute(): string
    {
        $bytes = $this->data_used;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_online)  return 'online';
        if ($this->is_expired) return 'expired';
        if ($this->activated_at) return 'offline';
        return 'unused';
    }
}
