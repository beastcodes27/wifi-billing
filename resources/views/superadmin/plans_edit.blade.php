@extends('superadmin.layouts.app')

@section('title', 'Edit SaaS Plan')

@section('content')
<div class="header animate-fade-in">
    <div class="header-title">
        <h1><a href="{{ route('superadmin.plans.index') }}" style="color: var(--text-muted); text-decoration: none;"><i class="fas fa-arrow-left" style="font-size: 1.2rem; margin-right: 0.5rem;"></i> SaaS Plans</a> / Edit Plan</h1>
        <p>Update pricing tier limits and details.</p>
    </div>
</div>

<div class="glass-card animate-fade-in delay-1" style="max-width: 800px;">
    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(239, 68, 68, 0.2);">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">Plan Details</h3>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Plan Name</label>
            <input type="text" name="name" value="{{ old('name', $plan->name) }}" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); color: var(--text-main); font-family: 'Outfit', sans-serif;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Price (Tsh)</label>
                <input type="number" name="price" value="{{ old('price', $plan->price) }}" required min="0" style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); color: var(--text-main); font-family: 'Outfit', sans-serif;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Billing Cycle (Months)</label>
                <input type="number" name="billing_cycle_months" value="{{ old('billing_cycle_months', $plan->billing_cycle_months) }}" required min="1" style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); color: var(--text-main); font-family: 'Outfit', sans-serif;">
            </div>
        </div>

        <h3 style="margin-bottom: 1.5rem; margin-top: 2.5rem; font-size: 1.2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">Resource Limits</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Max Routers allowed</label>
                <input type="number" name="max_routers" value="{{ old('max_routers', $plan->max_routers) }}" required min="1" style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); color: var(--text-main); font-family: 'Outfit', sans-serif;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Max Wi-Fi Customers allowed</label>
                <input type="number" name="max_customers" value="{{ old('max_customers', $plan->max_customers) }}" required min="1" style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); color: var(--text-main); font-family: 'Outfit', sans-serif;">
            </div>
        </div>

        <h3 style="margin-bottom: 1.5rem; margin-top: 2.5rem; font-size: 1.2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">Features</h3>
        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; color: var(--text-main);">
                <input type="checkbox" name="has_auto_payment" value="1" {{ old('has_auto_payment', $plan->has_auto_payment) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #8b5cf6;">
                <span>Has Auto Payment (SonicPesa integration for self-service)</span>
            </label>
        </div>

        <div style="margin-bottom: 2.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-main); font-size: 0.95rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--accent-primary);">
                Plan is Active (Available for new businesses)
            </label>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
