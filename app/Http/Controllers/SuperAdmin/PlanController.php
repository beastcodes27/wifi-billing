<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = \App\Models\SaasPlan::latest()->get();
        return view('superadmin.plans', compact('plans'));
    }

    public function create()
    {
        return view('superadmin.plans_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_routers' => 'required|integer|min:1',
            'max_customers' => 'required|integer|min:1',
            'billing_cycle_months' => 'required|integer|min:1',
            'has_auto_payment' => 'boolean',
        ]);

        $validated['has_auto_payment'] = $request->has('has_auto_payment');

        \App\Models\SaasPlan::create($validated);

        return redirect()->route('superadmin.plans.index')->with('success', 'SaaS Plan created successfully!');
    }

    public function edit(\App\Models\SaasPlan $plan)
    {
        return view('superadmin.plans_edit', compact('plan'));
    }

    public function update(Request $request, \App\Models\SaasPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_routers' => 'required|integer|min:1',
            'max_customers' => 'required|integer|min:1',
            'billing_cycle_months' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'has_auto_payment' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['has_auto_payment'] = $request->has('has_auto_payment');

        $plan->update($validated);

        return redirect()->route('superadmin.plans.index')->with('success', 'SaaS Plan updated successfully!');
    }

    public function destroy(\App\Models\SaasPlan $plan)
    {
        $plan->update(['is_active' => false]);
        return back()->with('success', 'SaaS Plan deactivated successfully!');
    }
}
