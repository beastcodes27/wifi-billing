<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FallbackTenantMiddleware
{
    /**
     * Resolve the tenant for the public captive portal.
     *
     * Order: existing context -> router sync token -> first tenant (legacy default).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(\App\Services\TenantContext::class);

        if ($context->getTenantId()) {
            return $next($request);
        }

        // Per-tenant captive portal: the MikroTik login page links to /portal?token=<router sync_token>.
        $token = $request->route('token') ?? $request->query('token');
        if ($token) {
            $router = \App\Models\Router::withoutGlobalScopes()->where('sync_token', $token)->first();
            if ($router && $router->tenant_id) {
                $tenant = \App\Models\Tenant::find($router->tenant_id);
                if ($tenant) {
                    $context->setTenant($tenant);

                    return $next($request);
                }
            }
        }

        $tenant = \App\Models\Tenant::find(1);
        if ($tenant) {
            $context->setTenant($tenant);
        }

        return $next($request);
    }
}
