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
            $subscription = $tenant->currentSubscription;

            if (!$subscription || $subscription->status !== 'active' || now()->greaterThan($subscription->ends_at)) {
                // Ignore if already on the billing or profile pages
                if (!$request->routeIs('admin.billing') && !$request->routeIs('admin.billing.pay') && !$request->routeIs('admin.billing.processing') && !$request->routeIs('admin.billing.status') && !$request->routeIs('admin.dashboard') && !$request->routeIs('admin.profile')) {
                    return redirect()->route('admin.billing')->with('error', 'Tafadhali chagua na kulipia kifurushi kwanza ili uendelee kutumia mfumo.');
                }
            }
        }

        return $next($request);
    }
}
