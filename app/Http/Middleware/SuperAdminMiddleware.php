<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_super_admin) {
            // Because SuperAdmin works across the entire platform, we should NOT apply the TenantScope.
            // But wait, the TenantMiddleware runs first and sets TenantContext if the super admin has a tenant_id (which they do, 1).
            // This is actually fine, they can view Tenant 1 data when they use regular Admin routes.
            // But for SuperAdmin routes, they might need to see ALL tenants.
            // We can disable the TenantScope temporarily or just let controllers use `withoutGlobalScopes()`.
            // For now, let's just authorize access.
            return $next($request);
        }

        abort(403, 'Unauthorized. Super Admin access only.');
    }
}
