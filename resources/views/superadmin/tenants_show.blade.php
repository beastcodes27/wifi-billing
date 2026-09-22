@extends('superadmin.layouts.app')

@section('title', 'Business Profile')

@push('styles')
<style>
    .profile-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--glass-border);
        margin-bottom: 2rem;
    }
    
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    
    .stat-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid var(--glass-border);
        padding: 1.5rem;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .stat-card .label {
        color: var(--text-muted);
        font-size: 0.85rem;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .stat-card .value {
        color: var(--text-main);
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .info-section {
        background: rgba(255,255,255,0.02);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.03);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .info-value {
        color: var(--text-main);
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="header animate-fade-in">
    <div class="header-title">
        <h1><a href="{{ route('superadmin.tenants.index') }}" style="color: var(--text-muted); text-decoration: none;"><i class="fas fa-arrow-left" style="font-size: 1.2rem; margin-right: 0.5rem;"></i> Tenants</a> / Profile</h1>
        <p>Viewing details for {{ $tenant->business_name }}</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <form action="{{ route('superadmin.tenants.impersonate', $tenant) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                <i class="fas fa-sign-in-alt"></i> Login As
            </button>
        </form>
        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Business
        </a>
    </div>
</div>

<div class="glass-card animate-fade-in delay-1" style="max-width: 1000px;">
    
    <div class="profile-header">
        <div class="profile-avatar">
            {{ substr($tenant->business_name, 0, 1) }}
        </div>
        <div>
            <h2 style="font-size: 1.8rem; margin: 0 0 0.2rem 0; color: var(--text-main);">{{ $tenant->business_name }}</h2>
            <div style="display: flex; gap: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                <span>ID: #{{ $tenant->id }}</span>
                <span>•</span>
                <span>Joined {{ $tenant->created_at->format('M d, Y') }}</span>
                <span>•</span>
                @if($tenant->status === 'active')
                    <span style="color: #10b981; font-weight: 600;"><i class="fas fa-circle" style="font-size: 0.6rem; vertical-align: middle;"></i> Active</span>
                @else
                    <span style="color: #ef4444; font-weight: 600;"><i class="fas fa-circle" style="font-size: 0.6rem; vertical-align: middle;"></i> Suspended</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--text-main);">Business Performance</h3>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Total Revenue</div>
            <div class="value" style="color: #3b82f6;">Tsh {{ number_format($revenue) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Total Customers</div>
            <div class="value" style="color: #8b5cf6;">{{ number_format($customersCount) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Active Sessions</div>
            <div class="value" style="color: #10b981;">{{ number_format($activeVouchersCount) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Routers Connected</div>
            <div class="value" style="color: #f59e0b;">{{ number_format($routersCount) }}</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        
        <!-- Admin Accounts -->
        <div>
            <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--text-main);">Administrator Accounts</h3>
            <div class="info-section">
                @forelse($tenant->adminUsers as $admin)
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $admin->name }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $admin->email }}</div>
                        </div>
                    </div>
                @empty
                    <div style="color: var(--text-muted); text-align: center; padding: 1rem;">No admin users found.</div>
                @endforelse
            </div>
        </div>

        <!-- Subscription Info -->
        <div>
            <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--text-main);">Current SaaS Plan</h3>
            <div class="info-section">
                @if($tenant->currentSubscription && $tenant->currentSubscription->plan)
                    <div class="info-row">
                        <span class="info-label">Plan Name</span>
                        <span class="info-value" style="color: #8b5cf6;">{{ $tenant->currentSubscription->plan->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            @if($tenant->currentSubscription->status === 'active')
                                <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem;">Active</span>
                            @else
                                <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem;">{{ ucfirst($tenant->currentSubscription->status) }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Expires On</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($tenant->currentSubscription->ends_at)->format('d M, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Routers Limit</span>
                        <span class="info-value">{{ $tenant->currentSubscription->plan->max_routers ?: 'Unlimited' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Customers Limit</span>
                        <span class="info-value">{{ $tenant->currentSubscription->plan->max_customers ?: 'Unlimited' }}</span>
                    </div>
                @else
                    <div style="color: var(--text-muted); text-align: center; padding: 2rem;">
                        <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i><br>
                        No active subscription found.
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
