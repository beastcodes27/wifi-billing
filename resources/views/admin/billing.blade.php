@extends('layouts.app')

@section('title', 'Billing ya Mfumo')

@section('content')

{{-- Header --}}
<div class="page-header-compact">
    <div>
        <h2><i class="fas fa-shield-alt" style="color:var(--primary-blue);"></i> Billing ya Mfumo</h2>
        <p>Usajili na leseni ya MedBando</p>
    </div>
</div>

@if(session('success'))
<div class="banking-card mb-3" style="background:#ECFDF5; border-color:#A7F3D0; color:#065F46; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.6rem; border-radius:10px;">
    <i class="fas fa-check-circle" style="color:#059669; font-size:1rem;"></i>
    <span style="font-weight:600; font-size:0.85rem;">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="banking-card mb-3" style="background:#FEF2F2; border-color:#FECACA; color:#991B1B; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.6rem; border-radius:10px;">
    <i class="fas fa-exclamation-circle" style="color:var(--danger); font-size:1rem;"></i>
    <span style="font-weight:600; font-size:0.85rem;">{{ session('error') }}</span>
</div>
@endif

@php
    $tenant = app(\App\Services\TenantContext::class)->getTenant();
    $subscription = $subscription ?? ($tenant ? ($tenant->activeSubscription() ?: $tenant->currentSubscription) : null);
    $plan = $subscription ? $subscription->plan : null;
    $isActive = $subscription && in_array($subscription->status, ['active', 'trial']) && now()->lessThan($subscription->ends_at);
    $isTrial = $subscription && $subscription->status === 'trial';
@endphp

{{-- Current Subscription Status Card --}}
<div class="banking-card p-3 p-md-4 mb-3" style="background:{{ $isActive ? '#F0FDF4' : '#FEF2F2' }}; border-color:{{ $isActive ? '#BBF7D0' : '#FECACA' }}; border-radius:12px;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width:50px; height:50px; border-radius:12px; background:{{ $isActive ? 'rgba(22,163,74,0.15)' : 'rgba(220,38,38,0.15)' }}; color:{{ $isActive ? 'var(--success)' : 'var(--danger)' }}; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                <i class="fas {{ $isActive ? 'fa-shield-alt' : 'fa-exclamation-triangle' }}"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-status {{ $isActive ? 'badge-success' : 'badge-danger' }}">
                        {{ $isTrial ? 'Kipindi cha Majaribio (Trial)' : ($isActive ? 'Active Subscription' : 'Usajili Umeisha / Haufanyi Kazi') }}
                    </span>
                    <span style="font-weight:800; font-size:1.15rem; color:var(--text-main);">
                        {{ $plan ? $plan->name : 'Hakuna Kifurushi' }}
                    </span>
                </div>
                <div style="font-size:0.85rem; color:var(--text-muted); margin-top:4px;">
                    @if($isTrial)
                        Unatumia kipindi cha majaribio cha bure. Lipia kifurushi kabla ya muda kuisha ili huduma zisikwame.
                    @elseif($isActive)
                        Kifurushi chako kipo hewani na huduma zote za WiFi zinaendelea kufanya kazi kikamilifu.
                    @else
                        Tafadhali chagua kifurushi hapa chini ili kuendelea kutumia mfumo bila kukwama.
                    @endif
                </div>
            </div>
        </div>

        @if($isActive)
        <div style="background:#FFFFFF; border:1px solid var(--border-color); padding:0.75rem 1.4rem; border-radius:10px; text-align:right;">
            <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.5px;">{{ $isTrial ? 'Trial Inaisha' : 'Mwisho wa Matumizi' }}</div>
            <div style="font-size:1.1rem; font-weight:800; color:var(--text-main);">
                {{ \Carbon\Carbon::parse($subscription->ends_at)->format('d M, Y') }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- SaaS Plans Grid --}}
<div class="mb-3">
    <h3 style="font-size:1.2rem; font-weight:700; color:var(--text-main); margin-bottom:0.4rem;">Chagua Kifurushi cha SaaS</h3>
    <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:1.5rem;">Lipia kifurushi moja kwa moja kupitia simu yako (M-Pesa, TigoPesa, Airtel Money, HaloPesa)</p>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1.25rem; margin-bottom:2rem;">
    @foreach($plans as $p)
    <div class="banking-card h-100 p-4 d-flex flex-column justify-content-between position-relative" style="border:2px solid {{ $p->has_auto_payment ? 'var(--primary-blue)' : 'var(--border-color)' }}; margin-bottom:0;">
        @if($p->has_auto_payment)
            <div style="position:absolute; top:-12px; right:20px; background:var(--primary-blue); color:white; padding:0.2rem 0.8rem; border-radius:20px; font-size:0.72rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px;">
                Inapendekezwa (Recommended)
            </div>
        @endif

        <div>
            <h4 style="font-size:1.2rem; font-weight:800; color:var(--text-main); margin-top:0; margin-bottom:0.5rem;">
                {{ $p->name }}
            </h4>
            <div style="display:flex; align-items:baseline; gap:0.3rem; margin-bottom:1.5rem;">
                <span style="font-size:1.75rem; font-weight:800; color:var(--deep-navy);">TSh {{ number_format($p->price) }}</span>
                <span style="font-size:0.85rem; color:var(--text-muted);">/ mwezi</span>
            </div>

            <ul style="list-style:none; padding:0; margin:0 0 1.5rem 0; font-size:0.875rem; color:var(--text-main);">
                <li style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.6rem;">
                    <i class="fas fa-check-circle" style="color:var(--success);"></i>
                    <span>Routers za MikroTik hadi <strong>{{ $p->max_routers }}</strong></span>
                </li>
                <li style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.6rem;">
                    <i class="fas fa-check-circle" style="color:var(--success);"></i>
                    <span>Wateja wa WiFi hadi <strong>{{ $p->max_customers }}</strong></span>
                </li>
                <li style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.6rem;">
                    <i class="fas fa-check-circle" style="color:var(--success);"></i>
                    <span>Uchapishaji wa Vocha (Batch &amp; Single)</span>
                </li>
                @if($p->has_auto_payment)
                <li style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.6rem;">
                    <i class="fas fa-check-circle" style="color:var(--primary-blue);"></i>
                    <strong>Malipo ya Kiotomatiki (SonicPesa)</strong>
                </li>
                <li style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.6rem;">
                    <i class="fas fa-check-circle" style="color:var(--primary-blue);"></i>
                    <span>Wateja kujihudumia (Hotspot Portal)</span>
                </li>
                @else
                <li style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.6rem; color:var(--text-muted); opacity:0.7;">
                    <i class="fas fa-times-circle" style="color:var(--danger);"></i>
                    <span>Bila Malipo ya Mtandaoni</span>
                </li>
                @endif
            </ul>
        </div>

        <form action="{{ route('admin.billing.pay') }}" method="POST">
            @csrf
            <input type="hidden" name="plan_id" value="{{ $p->id }}">
            <div class="mb-3">
                <label class="form-label" style="font-size:0.8rem; font-weight:600; color:var(--text-muted); margin-bottom:0.3rem;">Namba ya Kulipia:</label>
                <input type="tel" name="phone" placeholder="07XXXXXXXX au 2557XXXXXXXX" required class="form-control" style="border-radius:8px; padding:0.6rem 0.85rem; font-size:0.875rem; text-align:center; font-family:monospace; border:1px solid var(--border-color);">
            </div>
            <button type="submit" class="btn-banking btn-primary w-100 justify-content-center" style="padding:0.75rem;">
                <i class="fas fa-lock"></i> Lipia Kifurushi Sasa
            </button>
        </form>
    </div>
    @endforeach
</div>

@endsection
