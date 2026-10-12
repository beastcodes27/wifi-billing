<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'transaction_reference',
        'palm_pesa_order_id',
        'phone_number',
        'amount',
        'status',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'is_notified',
        'mikrotik_voucher',
        'provisioned_at',
        'package_id',
        'wifi_user_id',
        'customer_id',
        'router_id',
        'site_id',
        'created_by',
        'notes',
        'palm_pesa_response',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'provisioned_at' => 'datetime',
        'is_notified'    => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function package()
    {
        return $this->belongsTo(Package::class)->withoutGlobalScopes();
    }

    public function wifiUser()
    {
        return $this->belongsTo(WifiUser::class);
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'success' => '#10b981',
            'pending' => '#f59e0b',
            'failed'  => '#ef4444',
            'refunded'=> '#6366f1',
            default   => '#6b7280',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match ($this->status) {
            'success' => 'rgba(16,185,129,0.1)',
            'pending' => 'rgba(245,158,11,0.1)',
            'failed'  => 'rgba(239,68,68,0.1)',
            'refunded'=> 'rgba(99,102,241,0.1)',
            default   => 'rgba(107,114,128,0.1)',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'sonicpesa' => 'SonicPesa',
            'palm_pesa' => 'Palm Pesa',
            'fimipay'   => 'FimiPay',
            'mpesa'     => 'M-Pesa',
            'airtel'    => 'Airtel Money',
            'halo'      => 'HaloPesa',
            'mixx'      => 'Mixx by Yas',
            'cash'      => 'Cash',
            'manual'    => 'Manual',
            default     => $this->payment_method ?? 'Unknown',
        };
    }
}
