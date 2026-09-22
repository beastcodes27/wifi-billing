@extends('superadmin.layouts.app')

@section('title', 'Sajili Biashara / Mteja Mpya')

@section('content')
<div class="header animate-fade-in">
    <div class="header-title">
        <h1><a href="{{ route('superadmin.tenants.index') }}" style="color: var(--text-muted); text-decoration: none;"><i class="fas fa-arrow-left" style="font-size: 1.2rem; margin-right: 0.5rem;"></i> Wateja</a> / Sajili Mteja Mpya</h1>
        <p>Sajili biashara mpya ya WiFi na mtengenezee akaunti ya kuingia kwenye mfumo.</p>
    </div>
</div>

<div class="banking-card animate-fade-in delay-1" style="max-width: 800px; padding: 1.75rem 2rem;">
    @if ($errors->any())
        <div style="background: #FEF2F2; color: #DC2626; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #FECACA;">
            <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.88rem; font-weight: 600;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.tenants.store') }}" method="POST" autocomplete="off">
        @csrf
        
        <h3 style="margin-bottom: 1.25rem; font-size: 1.05rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-building" style="color: var(--primary-blue);"></i> Taarifa za Biashara
        </h3>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.4rem; color: var(--text-main); font-weight: 700; font-size: 0.85rem;">Jina la Biashara (WiFi Brand / Company)</label>
            <input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="Mfano: CityNet Hotspot, Sayuni WiFi..." required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: #FFFFFF; color: var(--text-main); font-size: 0.9rem;">
        </div>

        <h3 style="margin-bottom: 1.25rem; margin-top: 2rem; font-size: 1.05rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-user-shield" style="color: #10B981;"></i> Akaunti ya Msimamizi (Admin wa Mteja)
        </h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; margin-bottom: 0.4rem; color: var(--text-main); font-weight: 700; font-size: 0.85rem;">Jina Kamili la Admin</label>
                <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="Mfano: Juma Rashid" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: #FFFFFF; color: var(--text-main); font-size: 0.9rem;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.4rem; color: var(--text-main); font-weight: 700; font-size: 0.85rem;">Barua Pepe ya Kuingilia (Email)</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@citynet.co.tz" required autocomplete="new-email" style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: #FFFFFF; color: var(--text-main); font-size: 0.9rem;">
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.4rem; color: var(--text-main); font-weight: 700; font-size: 0.85rem;">Nenosiri la Awali (Password)</label>
            <input type="password" name="admin_password" required minlength="6" placeholder="Kima cha chini herufi 6" autocomplete="new-password" style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: #FFFFFF; color: var(--text-main); font-size: 0.9rem;">
        </div>

        <h3 style="margin-bottom: 1.25rem; margin-top: 2rem; font-size: 1.05rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-cube" style="color: #F59E0B;"></i> Kifurushi cha SaaS (Mpango wa Malipo)
        </h3>
        
        <div style="margin-bottom: 2rem;">
            <label style="display: block; margin-bottom: 0.4rem; color: var(--text-main); font-weight: 700; font-size: 0.85rem;">Chagua Kifurushi</label>
            <select name="saas_plan_id" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: #FFFFFF; color: var(--text-main); font-size: 0.9rem;">
                <option value="" disabled selected>-- Chagua Kifurushi cha Mfumo --</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }} (Tsh {{ number_format($plan->price) }} / Miezi {{ $plan->billing_cycle_months }})</option>
                @endforeach
            </select>
            <p style="color: #64748B; font-size: 0.78rem; margin-top: 0.4rem;"><i class="fas fa-info-circle" style="color: var(--primary-blue);"></i> Akaunti hii itafunguliwa na kuwa hai moja kwa moja mara tu utakapoisajili.</p>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary">Ghairi</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Sajili Mteja Sasa
            </button>
        </div>
    </form>
</div>
@endsection
