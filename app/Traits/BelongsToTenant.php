<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Boot the trait to apply the global scope and set tenant_id on create.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new \App\Models\Scopes\TenantScope);

        static::creating(function ($model) {
            $tenantId = app(\App\Services\TenantContext::class)->getTenantId();
            if (!$model->tenant_id) {
                $model->tenant_id = $tenantId ?: 1;
            }
        });
    }

    /**
     * Get the tenant that owns the model.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
