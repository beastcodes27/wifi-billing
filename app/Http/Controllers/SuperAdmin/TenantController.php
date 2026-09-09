<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = \App\Models\Tenant::with('currentSubscription.plan')->latest()->get();
        return view('superadmin.tenants', compact('tenants'));
    }

    public function create()
    {
        $plans = \App\Models\SaasPlan::where('is_active', true)->get();
        return view('superadmin.tenants_create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6',
            'saas_plan_id' => 'required|exists:saas_plans,id',
        ]);

        // Create Tenant
        $tenant = \App\Models\Tenant::create([
            'business_name' => $validated['business_name'],
            'status' => 'active',
        ]);

        // Create Admin User for the Tenant
        \App\Models\User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => bcrypt($validated['admin_password']),
            'tenant_id' => $tenant->id,
            'is_super_admin' => false,
        ]);

        // Create SaaS Subscription
        $plan = \App\Models\SaasPlan::find($validated['saas_plan_id']);
        \App\Models\SaasSubscription::create([
            'tenant_id' => $tenant->id,
            'saas_plan_id' => $plan->id,
            'amount_paid' => $plan->price,
            'payment_method' => 'Cash',
            'reference_number' => 'NEW-TENANT',
            'starts_at' => now(),
            'ends_at' => now()->addMonths($plan->billing_cycle_months),
            'status' => 'active',
        ]);

        return redirect()->route('superadmin.tenants.index')->with('success', 'Business added successfully!');
    }

    public function show(\App\Models\Tenant $tenant)
    {
        // Load relationships
        $tenant->load(['adminUsers', 'currentSubscription.plan']);
        
        // Calculate stats for this specific tenant using withoutGlobalScopes to query across tenants,
        // but manually filtering by this tenant_id
        $routersCount = \App\Models\Router::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)->where('tenant_id', $tenant->id)->count();
        $customersCount = \App\Models\Customer::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)->where('tenant_id', $tenant->id)->count();
        
        // Revenue (Transactions: Online + Activated Cash Vouchers)
        $revenue = \App\Models\Transaction::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->leftJoin('wifi_users', 'transactions.wifi_user_id', '=', 'wifi_users.id')
            ->where('transactions.tenant_id', $tenant->id)
            ->where('transactions.status', 'success')
            ->where(function($q) {
                $q->where(function($onlineQ) {
                    $onlineQ->where('transactions.payment_method', '!=', 'cash')
                            ->whereNotIn('transactions.phone_number', ['Batch Print', 'Mteja wa Cash']);
                })
                ->orWhereNotNull('wifi_users.activated_at');
            })
            ->sum('transactions.amount');
            
        // Active Vouchers
        $activeVouchersCount = \App\Models\WifiUser::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('tenant_id', $tenant->id)
            ->where('is_online', true)
            ->count();

        return view('superadmin.tenants_show', compact('tenant', 'routersCount', 'customersCount', 'revenue', 'activeVouchersCount'));
    }

    public function edit(\App\Models\Tenant $tenant)
    {
        return view('superadmin.tenants_edit', compact('tenant'));
    }

    public function update(Request $request, \App\Models\Tenant $tenant)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'status' => 'required|in:active,suspended',
        ]);

        $tenant->update($validated);

        return redirect()->route('superadmin.tenants.index')->with('success', 'Business updated successfully!');
    }

    public function destroy(\App\Models\Tenant $tenant)
    {
        // Suspend the tenant
        $tenant->update(['status' => 'suspended']);
        return back()->with('success', 'Business suspended successfully!');
    }

    public function impersonate(\App\Models\Tenant $tenant)
    {
        session()->put('impersonated_tenant_id', $tenant->id);
        return redirect()->route('admin.dashboard')->with('success', "You are now impersonating {$tenant->business_name}.");
    }

    public function leaveImpersonation()
    {
        session()->forget('impersonated_tenant_id');
        return redirect()->route('superadmin.dashboard')->with('success', 'Returned to Super Admin Dashboard.');
    }
}
