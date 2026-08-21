<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = ['name', 'location', 'description'];

    public function routers()
    {
        return $this->hasMany(Router::class);
    }
}
