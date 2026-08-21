<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'site_id', 
        'name', 
        'router_type', 
        'management_ip', 
        'notes',
        'api_host',
        'api_username',
        'api_password_encrypted',
        'api_port',
        'status',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    protected static function booted()
    {
        static::creating(function ($router) {
            if (empty($router->sync_token)) {
                $router->sync_token = \Illuminate\Support\Str::random(32);
            }
        });
    }
}
