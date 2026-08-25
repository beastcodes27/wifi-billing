<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Services\TenantContext;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);

        $tenantId = $tenantContext->getTenantId();

        if ($tenantId) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        } else {
            // STRICT ISOLATION: If no tenant is set in context, block the query 
            // to prevent accidental data leakage across all tenants.
            // Use 0 as an invalid tenant_id to yield no results.
            $builder->where($model->getTable() . '.tenant_id', 0);
        }
    }
}
