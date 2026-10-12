<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Super Admins bypass the paywall if they are NOT impersonating someone
        if ($user && $user->is_super_admin && !session()->has('impersonated_tenant_id')) {
            return $next($request);
        }

        $tenant = app(\App\Services\TenantContext::class)->getTenant();

        if ($tenant) {
            // Access is granted for an active OR in-trial, unexpired subscription.
            $subscription = $tenant->activeSubscription();

            if (! $subscription) {
                // Pages a blocked tenant may still reach while they renew.
                $allowed = $request->routeIs(
                    'admin.billing',
                    'admin.billing.pay',
                    'admin.billing.processing',
                    'admin.billing.status',
                    'admin.dashboard',
                    'admin.profile',
                );

                if (! $allowed) {
                    return redirect()->route('admin.billing')->with('error', 'Tafadhali chagua na kulipia kifurushi kwanza ili uendelee kutumia mfumo.');
                }
            }
        }

        return $next($request);
    }
}
