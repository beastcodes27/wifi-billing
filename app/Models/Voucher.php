<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'batch_id',
        'code',
        'wifi_user_id',
        'transaction_id',
        'status',
        'used_at',
        'router_id',
        'site_id',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(VoucherBatch::class, 'batch_id');
    }

    public function wifiUser()
    {
        return $this->belongsTo(WifiUser::class, 'wifi_user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function router()
    {
        return $this->belongsTo(Router::class, 'router_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}
