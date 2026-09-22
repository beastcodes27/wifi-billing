@extends('superadmin.layouts.app')

@section('title', 'Manage Subscriptions')

@section('content')
<div class="header animate-fade-in">
    <div class="header-title">
        <h1>Subscriptions</h1>
        <p>Track all SaaS payments and expirations.</p>
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
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Business</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Plan</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Status</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Amount Paid</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border);">Valid Until</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $subscription)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                    <td style="padding: 1.25rem 1rem; font-weight: 600;">
                        {{ $subscription->tenant->business_name ?? 'Unknown' }}
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">Ref: {{ $subscription->reference_number }}</div>
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-main);">
                        {{ $subscription->plan->name ?? 'Unknown' }}
                    </td>
                    <td style="padding: 1.25rem 1rem;">
                        @if($subscription->status === 'active')
                            <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Active</span>
                        @elseif($subscription->status === 'expired')
                            <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Expired</span>
                        @else
                            <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">{{ ucfirst($subscription->status) }}</span>
                        @endif
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        Tsh {{ number_format($subscription->amount_paid) }}
                        <div style="font-size: 0.75rem;">{{ $subscription->payment_method }}</div>
                    </td>
                    <td style="padding: 1.25rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        {{ \Carbon\Carbon::parse($subscription->ends_at)->format('M d, Y') }}
                        @if(\Carbon\Carbon::parse($subscription->ends_at)->isPast())
                            <div style="font-size: 0.75rem; color: #ef4444;">Expired</div>
                        @else
                            <div style="font-size: 0.75rem; color: #10b981;">{{ \Carbon\Carbon::parse($subscription->ends_at)->diffForHumans() }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        No subscriptions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
