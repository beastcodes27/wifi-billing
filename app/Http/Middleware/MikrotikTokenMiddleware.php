<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MikrotikTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token') ?? $request->query('token') ?? $request->header('X-Mikrotik-Token');
        $context = app(\App\Services\TenantContext::class);

        if ($token) {
            // Find router matching this token
            $router = \App\Models\Router::withoutGlobalScopes()->where('sync_token', $token)->first();

            if ($router) {
                // Keep router heartbeat updated
                $router->update([
                    'last_seen_at' => now(),
                    'last_synced_at' => now(),
                ]);

                $tenant = \App\Models\Tenant::find($router->tenant_id) ?: \App\Models\Tenant::first();
                if ($tenant) {
                    $context->setTenant($tenant);
                }
                return $next($request);
            }

            // Fallback if token is unrecognized: check if a router exists in the system
            \Illuminate\Support\Facades\Log::warning("[MIKROTIK TOKEN] Unrecognized sync token: " . substr($token, 0, 10) . "... Attempting fallback to active router.");
            $fallbackRouter = \App\Models\Router::withoutGlobalScopes()->first();
            if ($fallbackRouter) {
                $fallbackRouter->update([
                    'last_seen_at' => now(),
                    'last_synced_at' => now(),
                ]);
                $tenant = \App\Models\Tenant::find($fallbackRouter->tenant_id) ?: \App\Models\Tenant::first();
                if ($tenant) {
                    $context->setTenant($tenant);
                }
                return $next($request);
            }

            return response(':log error "Unauthorized. Invalid Token.";', 401)->header('Content-Type', 'text/plain');
        }

        // GRACE PERIOD: If no token is provided, fallback to active router/tenant #1
        $fallbackRouter = \App\Models\Router::withoutGlobalScopes()->first();
        if ($fallbackRouter) {
            $fallbackRouter->update([
                'last_seen_at' => now(),
                'last_synced_at' => now(),
            ]);
        }
        $tenant = \App\Models\Tenant::first();
        if ($tenant) {
            $context->setTenant($tenant);
        }

        return $next($request);
    }
}
