<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FallbackTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(\App\Services\TenantContext::class);
        if (!$context->getTenantId()) {
            $tenant = \App\Models\Tenant::find(1);
            if ($tenant) {
                $context->setTenant($tenant);
            }
        }
        return $next($request);
    }
}
