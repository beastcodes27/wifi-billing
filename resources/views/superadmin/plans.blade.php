@extends('superadmin.layouts.app')

@section('title', 'Manage SaaS Plans')

@section('content')
<div class="header animate-fade-in">
    <div class="header-title">
        <h1>SaaS Plans</h1>
        <p>Define your pricing tiers and limits.</p>
    </div>
    <div>
        <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Plan
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16,185,129,0.1); color: #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid rgba(16,185,129,0.2);">
        {{ session('success') }}
    </div>
@endif

<div class="glass-card animate-fade-in delay-1" style="padding: 0;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Plan Name</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Price</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Duration</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Limits</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border); text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                    <td style="padding: 1.25rem 1rem; font-weight: 600;">
                        {{ $plan->name }}
                        @if(!$plan->is_active)
                            <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; margin-left: 0.5rem;">Inactive</span>
                        @endif
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-main);">
                        Tsh {{ number_format($plan->price) }}
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        {{ $plan->billing_cycle_months }} Months
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        {{ $plan->max_routers }} Routers<br>
                        {{ $plan->max_customers }} Customers
                    </td>
                    <td style="padding: 1.25rem 1rem; text-align: right;">
                        <a href="{{ route('superadmin.plans.edit', $plan) }}" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1); padding: 0.4rem 0.8rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem; margin-right: 0.5rem;"><i class="fas fa-edit"></i> Edit</a>
                        <form action="{{ route('superadmin.plans.destroy', $plan) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to deactivate this plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 0.4rem 0.8rem; border-radius: 6px; border: none; font-size: 0.8rem; cursor: pointer;"><i class="fas fa-ban"></i> Deactivate</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        No plans found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
