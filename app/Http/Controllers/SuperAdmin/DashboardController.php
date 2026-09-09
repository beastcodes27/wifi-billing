<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantsCount = \App\Models\Tenant::count();
        $plansCount = \App\Models\SaasPlan::count();
        
        $activeSubscriptions = \App\Models\SaasSubscription::where('status', 'active')
            ->where('ends_at', '>', now())
            ->count();
            
        $mrr = \App\Models\SaasSubscription::where('status', 'active')
            ->where('ends_at', '>', now())
            ->join('saas_plans', 'saas_subscriptions.saas_plan_id', '=', 'saas_plans.id')
            ->sum('saas_plans.price');

        // Total Platform Stats (Aggregating across all tenants: Online + Activated Cash Vouchers)
        $totalPlatformRevenue = \App\Models\Transaction::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where('transactions.status', 'success')
            ->where(function($q) {
                $q->where(function($onlineQ) {
                    $onlineQ->where('transactions.payment_method', '!=', 'cash')
                            ->whereNotIn('transactions.phone_number', ['Batch Print', 'Mteja wa Cash']);
                })
                ->orWhereNotNull('wifi_users.activated_at');
            })
            ->sum('transactions.amount');
            
        $totalPlatformRouters = \App\Models\Router::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->count();

        // 1. Tenant Growth Chart Data (Last 6 Months)
        $months = collect();
        $tenantGrowth = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));
            $count = \App\Models\Tenant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $tenantGrowth->push($count);
        }

        // 2. SaaS Plan Distribution Chart Data
        $plans = \App\Models\SaasPlan::withCount('subscriptions')->get();
        $planNames = $plans->pluck('name');
        $planCounts = $plans->pluck('subscriptions_count');

        $recentTenants = \App\Models\Tenant::latest()->take(5)->get();
        
        $recentSubscriptions = \App\Models\SaasSubscription::with(['tenant', 'plan'])
            ->latest()
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'tenantsCount', 
            'plansCount', 
            'activeSubscriptions', 
            'mrr',
            'recentTenants',
            'recentSubscriptions',
            'totalPlatformRevenue',
            'totalPlatformRouters',
            'months',
            'tenantGrowth',
            'planNames',
            'planCounts'
        ));
    }
}
