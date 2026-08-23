<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'wifi_user_id',
        'package_id',
        'transaction_id',
        'type',
        'status',
        'starts_at',
        'expires_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function wifiUser()
    {
        return $this->belongsTo(WifiUser::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class)->withoutGlobalScopes();
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'new'            => '#3b82f6',
            'renewal'        => '#10b981',
            'extension'      => '#6366f1',
            'package_change' => '#f59e0b',
            'cancellation'   => '#ef4444',
            default          => '#6b7280',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'new'            => 'New',
            'renewal'        => 'Renewal',
            'extension'      => 'Extension',
            'package_change' => 'Package Change',
            'cancellation'   => 'Cancelled',
            default          => ucfirst($this->type),
        };
    }
}
