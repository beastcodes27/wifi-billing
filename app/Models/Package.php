<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'name',
        'price',
        'duration_minutes',
        'mikrotik_profile',
        'description',
        'download_speed',
        'upload_speed',
        'data_quota_mb',
        'display_order',
        'is_active',
    ];

    public function getHumanDurationAttribute()
    {
        $minutes = (int) $this->duration_minutes;
        
        if ($minutes >= 43200 && $minutes % 43200 === 0) {
            $months = (int) ($minutes / 43200);
            return $months === 1 ? 'Mwezi 1 (Siku 30)' : "Miezi {$months}";
        } elseif ($minutes >= 10080 && $minutes % 10080 === 0) {
            $weeks = (int) ($minutes / 10080);
            return $weeks === 1 ? 'Wiki 1 (Siku 7)' : "Wiki {$weeks}";
        } elseif ($minutes >= 1440 && $minutes % 1440 === 0) {
            $days = (int) ($minutes / 1440);
            return $days === 1 ? 'Saa 24 (Siku 1)' : "Siku {$days}";
        } elseif ($minutes >= 60 && $minutes % 60 === 0) {
            return 'Saa ' . ($minutes / 60);
        } else {
            return 'Dk ' . $minutes;
        }
    }
}
