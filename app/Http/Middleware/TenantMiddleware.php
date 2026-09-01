<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantContext;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If it's a logged-in user in the admin panel
        if (auth()->check()) {
            $user = auth()->user();
            
            // Assume the user has a tenant_id
            $targetTenantId = $user->tenant_id;

            // Override if Super Admin is impersonating another tenant
            if ($user->is_super_admin && session()->has('impersonated_tenant_id')) {
                $targetTenantId = session('impersonated_tenant_id');
            } elseif (!$targetTenantId && $user->is_super_admin) {
                // If super admin has no explicit tenant_id, default to the active WiFi tenant in use
                $firstTenant = \App\Models\Tenant::first();
                $targetTenantId = $firstTenant ? $firstTenant->id : 1;
            }

            if ($targetTenantId) {
                // Fetch the tenant (using withoutGlobalScopes to avoid infinite loops if User model is already scoped)
                $tenant = \App\Models\Tenant::find($targetTenantId);
                if ($tenant) {
                    app(TenantContext::class)->setTenant($tenant);
                }
            }
        }

        return $next($request);
    }
}
