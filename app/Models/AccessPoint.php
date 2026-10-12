<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessPoint extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'site_id',
        'name',
        'brand',
        'model',
        'serial_number',
        'device_key',
        'mac_address',
        'management_ip',
        'adoption_status',
        'adoption_method',
        'scan_confidence',
        'scan_raw',
        'notes',
        'adopted_at',
        'last_seen_at',
    ];

    protected $casts = [
        'scan_raw' => 'array',
        'scan_confidence' => 'float',
        'adopted_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function isAdopted(): bool
    {
        return $this->adoption_status === 'adopted';
    }
}
