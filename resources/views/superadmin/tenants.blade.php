@extends('superadmin.layouts.app')

@section('title', 'Manage Tenants')

@section('content')
<div class="header animate-fade-in">
    <div class="header-title">
        <h1>Tenants (Businesses)</h1>
        <p>Manage all businesses using your SaaS platform.</p>
    </div>
    <div>
        <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Business
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
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Business Name</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Status</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Plan</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Joined</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border); text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                    <td style="padding: 1.25rem 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--text-muted); border: 1px solid var(--glass-border);">
                                {{ substr($tenant->business_name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.95rem;">{{ $tenant->business_name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">ID: #{{ $tenant->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.25rem 1rem;">
                        @if($tenant->status === 'active')
                            <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Active</span>
                        @else
                            <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">{{ ucfirst($tenant->status) }}</span>
                        @endif
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        {{ $tenant->currentSubscription->plan->name ?? 'None' }}
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        {{ $tenant->created_at->format('M d, Y') }}
                    </td>
                    <td style="padding: 1.25rem 1rem; text-align: right; display: flex; justify-content: flex-end; gap: 0.5rem; flex-wrap: nowrap;">
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1); padding: 0.4rem 0.8rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem; display: inline-flex; align-items: center;"><i class="fas fa-user-circle"></i> Profile</a>
                        <form action="{{ route('superadmin.tenants.impersonate', $tenant) }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="submit" style="color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 0.4rem 0.8rem; border-radius: 6px; border: none; font-size: 0.8rem; cursor: pointer; text-decoration: none;"><i class="fas fa-sign-in-alt"></i> Login As</button>
                        </form>
                        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1); padding: 0.4rem 0.8rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem; display: inline-flex; align-items: center;"><i class="fas fa-edit"></i> Edit</a>
                        <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to suspend this business?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 0.4rem 0.8rem; border-radius: 6px; border: none; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center;"><i class="fas fa-ban"></i> Suspend</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        No tenants found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
