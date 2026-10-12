@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $totalWeekSales = array_sum($chartData ?? []);
@endphp

<style>
    /* FinTech Dashboard Header Card */
    .fintech-hero-card {
        background: linear-gradient(145deg, #181820 0%, #111116 100%);
        border-radius: 24px;
        padding: 1.35rem 1.25rem 1.15rem 1.25rem;
        color: #ffffff;
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .fintech-hero-card::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(255, 51, 0, 0.18) 0%, rgba(0,0,0,0) 70%);
        pointer-events: none;
    }
    
    /* Top Profile Row */
    .fintech-profile-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.15rem;
        position: relative;
        z-index: 2;
    }
    .fintech-user-pill {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        text-decoration: none;
        color: #ffffff;
    }
    .fintech-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FF3300, #ff6b3d);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(255, 51, 0, 0.35);
        border: 1.5px solid rgba(255, 255, 255, 0.2);
    }
    .fintech-username {
        font-size: 0.95rem;
        font-weight: 700;
        color: #f1f5f9;
        display: flex;
        align-items: center;
        gap: 4px;
        letter-spacing: -0.2px;
    }
    .fintech-username:hover {
        color: #ffffff;
    }
    .fintech-top-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .fintech-icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .fintech-icon-btn:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }

    /* Balance & Pay Row */
    .fintech-balance-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 1.15rem;
        gap: 0.75rem;
        position: relative;
        z-index: 2;
    }
    .fintech-amount-val {
        font-size: 2.15rem;
        font-weight: 900;
        line-height: 1.05;
        letter-spacing: -0.5px;
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
    }
    @media (max-width: 480px) {
        .fintech-amount-val {
            font-size: 1.85rem;
        }
    }
    .fintech-amount-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 0.35rem;
        white-space: nowrap;
    }
    .fintech-period-select {
        height: 38px;
        min-height: 38px;
        max-height: 38px;
        background: rgba(255, 255, 255, 0.14);
        border: 1.5px solid rgba(255, 255, 255, 0.28);
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 800;
        border-radius: 50px;
        padding: 0 32px 0 16px;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        font-family: inherit;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 13px;
    }
    .fintech-period-select:hover, .fintech-period-select:focus {
        background-color: rgba(255, 255, 255, 0.22);
        border-color: rgba(255, 255, 255, 0.45);
    }
    .fintech-period-select option {
        color: #0f172a;
        background: #ffffff;
    }
    .fintech-pay-cta {
        height: 38px;
        min-height: 38px;
        max-height: 38px;
        background: #FF3300;
        color: #ffffff;
        padding: 0 1.25rem;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(255, 51, 0, 0.35);
        box-sizing: border-box;
        border: 1.5px solid transparent;
        transition: transform 0.15s, opacity 0.15s;
        white-space: nowrap;
    }
    .fintech-pay-cta:active {
        transform: scale(0.96);
    }
    .fintech-pay-cta:hover {
        color: #ffffff;
        opacity: 0.95;
    }

    /* Sub-bar / Mini Strip */
    .fintech-substrip {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 0.65rem 0.5rem;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.25rem;
        position: relative;
        z-index: 2;
    }
    .fintech-subpill {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0.2rem 0.35rem;
        position: relative;
    }
    .fintech-subpill:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 20%;
        height: 60%;
        width: 1px;
        background: rgba(255, 255, 255, 0.08);
    }
    .fintech-subpill .subpill-icon {
        font-size: 0.8rem;
        margin-bottom: 0.15rem;
    }
    .fintech-subpill .subpill-title {
        font-size: 0.68rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 0.1rem;
    }
    .fintech-subpill .subpill-val {
        font-size: 0.85rem;
        font-weight: 800;
        color: #ffffff;
        font-family: monospace;
        letter-spacing: -0.3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    /* Services Section Header */
    .fintech-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding: 0 0.25rem;
    }
    .fintech-section-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.2px;
        margin: 0;
    }

    /* Services 3x2 Grid */
    .fintech-services-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.65rem;
        margin-bottom: 1.35rem;
    }
    @media (max-width: 480px) {
        .fintech-services-grid {
            gap: 0.5rem;
        }
    }
    .fintech-service-card {
        background: #FFFFFF;
        border-radius: 18px;
        padding: 0.95rem 0.5rem 0.85rem 0.5rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        position: relative;
    }
    .fintech-service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
        border-color: rgba(0, 0, 0, 0.1);
    }
    .fintech-service-card:active {
        transform: scale(0.97);
    }
    .fintech-sq-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #ffffff;
        margin-bottom: 0.45rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        flex-shrink: 0;
    }
    .fintech-sq-icon.blue { background: linear-gradient(135deg, #2563EB, #1D4ED8); }
    .fintech-sq-icon.teal { background: linear-gradient(135deg, #0D9488, #0F766E); }
    .fintech-sq-icon.orange { background: linear-gradient(135deg, #EA580C, #C2410C); }
    .fintech-sq-icon.slate { background: linear-gradient(135deg, #475569, #334155); }
    .fintech-sq-icon.purple { background: linear-gradient(135deg, #7C3AED, #6D28D9); }
    .fintech-sq-icon.navy { background: linear-gradient(135deg, #0284C7, #0369A1); }

    .fintech-service-name {
        font-size: 0.76rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.25;
        margin-bottom: 0.15rem;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .fintech-service-sub {
        font-size: 0.65rem;
        font-weight: 600;
        color: #94a3b8;
        line-height: 1;
    }
</style>

{{-- ======================================================== --}}
{{-- 1. FINTECH DARK HERO HEADER CARD                         --}}
{{-- ======================================================== --}}
<div class="fintech-hero-card">
    {{-- Top Profile Bar --}}
    <div class="fintech-profile-bar">
        <a href="{{ route('admin.profile') }}" class="fintech-user-pill">
            <div class="fintech-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
            </div>
            <div class="fintech-username">
                {{ auth()->user()->name ?? 'Administrator' }}
                <i class="fas fa-chevron-right" style="font-size: 0.68rem; opacity: 0.6; margin-left: 2px;"></i>
            </div>
        </a>
        <div class="fintech-top-actions">
            @if(auth()->user() && auth()->user()->is_super_admin)
            <a href="{{ route('superadmin.dashboard') }}" class="fintech-icon-btn" title="Admin Mkuu (SaaS Control)" style="background: rgba(124, 58, 237, 0.25); color: #C4B5FD; border-color: rgba(124, 58, 237, 0.4);">
                <i class="fas fa-crown"></i>
            </a>
            @endif
            <a href="{{ route('admin.notifications.index') }}" class="fintech-icon-btn" title="Kituo cha Taarifa">
                <i class="fas fa-bell" style="color: #FBBF24;"></i>
            </a>
            <a href="{{ route('admin.live_sessions') }}" class="fintech-icon-btn" title="Live NOC">
                <i class="fas fa-satellite-dish" style="color: #34D399;"></i>
            </a>
            <a href="{{ route('admin.settings') }}" class="fintech-icon-btn" title="Mipangilio">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>

    {{-- Main Balance & Pay CTA Button --}}
    <div class="fintech-balance-row">
        <div>
            <div class="fintech-amount-val" id="heroMainAmount" title="Jumla Kuu">
                Tsh {{ $netTotalRevenue > 0 ? number_format($netTotalRevenue) : '00' }}
            </div>
            <div class="fintech-amount-label">
                <span id="heroPeriodLabel">Jumla Kuu</span>
                <select id="balancePeriodFilter" onchange="switchBalancePeriod(this.value)" class="fintech-period-select" title="Chagua muda wa mapato">
                    <option value="total" selected>Jumla Yote</option>
                    <option value="today">Leo</option>
                    <option value="month">Mwezi Huu</option>
                </select>
            </div>
        </div>
        <a href="{{ route('admin.vouchers.index') }}" class="fintech-pay-cta">
            <i class="fas fa-plus-circle"></i> Vocha
        </a>
    </div>

    {{-- Inset Floating 3-Column Strip --}}
    <div class="fintech-substrip">
        <div class="fintech-subpill">
            <i class="fas fa-money-bill-wave subpill-icon" style="color: #34D399;"></i>
            <span class="subpill-title">Cash Droo</span>
            <span class="subpill-val" id="heroCashVal" style="color: #34D399;">Tsh {{ $netCashRevenue > 0 ? number_format($netCashRevenue) : '00' }}</span>
        </div>
        <div class="fintech-subpill">
            <i class="fas fa-mobile-alt subpill-icon" style="color: #38BDF8;"></i>
            <span class="subpill-title">Online (SonicPesa)</span>
            <span class="subpill-val" id="heroOnlineVal" style="color: #38BDF8;">Tsh {{ $onlineRevenue > 0 ? number_format($onlineRevenue) : '00' }}</span>
        </div>
        <div class="fintech-subpill">
            <i class="fas fa-arrow-down subpill-icon" style="color: #F87171;"></i>
            <span class="subpill-title">Matumizi</span>
            <span class="subpill-val" id="heroExpenseVal" style="color: #F87171;">-Tsh {{ $totalExpenses > 0 ? number_format($totalExpenses) : '00' }}</span>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 2. SERVICES SECTION (3x2 APP GRID)                       --}}
{{-- ======================================================== --}}
<div class="fintech-section-header">
    <h3 class="fintech-section-title">Huduma za Mtandao</h3>
    <span style="font-size: 0.78rem; font-weight: 700; color: #0284C7;">WiFi Services</span>
</div>

<div class="fintech-services-grid">
    {{-- Service 1: Wateja Hewani --}}
    <a href="{{ route('admin.live_sessions') }}" class="fintech-service-card">
        <div class="fintech-sq-icon blue">
            <i class="fas fa-headset"></i>
        </div>
        <div class="fintech-service-name">Wateja Hewani</div>
        <div class="fintech-service-sub">{{ $activeSessions }} Live / {{ $activeSubscribers }} Wenye Bando</div>
    </a>

    {{-- Service 2: Vocha Stoo --}}
    <a href="{{ route('admin.vouchers.index') }}" class="fintech-service-card">
        <div class="fintech-sq-icon teal">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="fintech-service-name">Vocha Stoo</div>
        <div class="fintech-service-sub">{{ number_format($unusedVouchersCount) }} Zipo</div>
    </a>

    {{-- Service 3: MikroTik Routers --}}
    <a href="{{ route('admin.routers.index') }}" class="fintech-service-card">
        <div class="fintech-sq-icon orange">
            <i class="fas fa-network-wired"></i>
        </div>
        <div class="fintech-service-name">MikroTik</div>
        <div class="fintech-service-sub">{{ $totalRouters }} {{ $totalRouters == 1 ? 'Router' : 'Routers' }}</div>
    </a>

    {{-- Service 4: Vifurushi --}}
    <a href="{{ route('admin.packages.index') }}" class="fintech-service-card">
        <div class="fintech-sq-icon slate">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="fintech-service-name">Vifurushi</div>
        <div class="fintech-service-sub">{{ count($packages) }} Vipo</div>
    </a>

    {{-- Service 5: Matumizi --}}
    <a href="{{ route('admin.expenses.index') }}" class="fintech-service-card">
        <div class="fintech-sq-icon purple">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="fintech-service-name">Matumizi</div>
        <div class="fintech-service-sub">Leo: Tsh {{ $expensesToday > 0 ? number_format($expensesToday) : '00' }}</div>
    </a>

    {{-- Service 6: Miamala --}}
    <a href="{{ route('admin.transactions') }}" class="fintech-service-card">
        <div class="fintech-sq-icon navy">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="fintech-service-name">Miamala</div>
        <div class="fintech-service-sub">Tazama Yote</div>
    </a>
</div>

{{-- ======================================================== --}}
{{-- 4. REVENUE TREND (SPACIOUS FULL-WIDTH CHART)             --}}
{{-- ======================================================== --}}
<div class="banking-card p-3 p-md-4 mb-3" style="border-radius: 20px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 32px; height: 32px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 0.88rem;">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0;">Mwenendo wa Mauzo (Siku 7)</h3>
        </div>
        <span class="badge-status badge-info" style="font-size: 0.75rem; font-family: monospace; border-radius: 20px; padding: 3px 10px;">
            Wiki Hii: Tsh {{ $totalWeekSales > 0 ? number_format($totalWeekSales) : '00' }}
        </span>
    </div>
    <div style="position: relative; height: 220px; width: 100%;">
        <canvas id="salesChart"></canvas>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 5. LIVE RECENT TRANSACTIONS STREAM                       --}}
{{-- ======================================================== --}}
<div class="banking-card p-0" style="border-radius: 20px; overflow: hidden; margin-bottom: 1.5rem; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div style="padding: 0.85rem 1.15rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #FAFAFC;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 30px; height: 30px; border-radius: 9px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                <i class="fas fa-receipt"></i>
            </div>
            <span style="font-weight: 800; font-size: 0.92rem; color: var(--text-main);">Miamala ya Hivi Karibuni</span>
            <span class="wifi-status-chip" style="font-size: 0.65rem; padding: 2px 7px;">
                <span class="live-pulse-dot"></span> Live
            </span>
        </div>
        <a href="{{ route('admin.transactions') }}" style="font-size: 0.78rem; font-weight: 700; color: #0284C7; text-decoration: none;">
            Tazama Yote <i class="fas fa-chevron-right" style="font-size: 0.65rem;"></i>
        </a>
    </div>

    {{-- Transaction List --}}
    <div id="recent-transactions-container" style="display: flex; flex-direction: column;">
        @forelse($recentTransactions as $i => $txn)
            @php
                $isCash = in_array($txn->phone_number, ['Batch Print', 'Mteja wa Cash', 'Mauzo ya Vocha (Cash)']) || $txn->payment_method === 'cash';
                $phoneLabel = $isCash
                    ? ($txn->phone_number == 'Batch Print' ? 'Vocha (Batch)' : ($txn->phone_number == 'Mauzo ya Vocha (Cash)' || $txn->phone_number == 'Mteja wa Cash' ? 'Vocha (Cash)' : $txn->phone_number . ' (Cash)'))
                    : $txn->phone_number;
                $pkgName = $txn->package ? $txn->package->name : 'WiFi Kifurushi';
                $voucherBadge = $txn->mikrotik_voucher ? ' • ' . $txn->mikrotik_voucher : '';
            @endphp
            <div style="padding: 0.65rem 1rem; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0; flex: 1;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $isCash ? '#FFFBEB' : '#EFF6FF' }}; color: {{ $isCash ? '#D97706' : '#2563EB' }}; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0;">
                        <i class="{{ $isCash ? 'fas fa-ticket-alt' : 'fas fa-mobile-alt' }}"></i>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $phoneLabel }}
                        </div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $pkgName }}{{ $voucherBadge }} &bull; {{ $txn->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                <div style="text-align: right; flex-shrink: 0;">
                    <div style="font-weight: 800; font-size: 0.92rem; color: var(--success); font-family: monospace;">
                        +Tsh {{ number_format($txn->amount) }}
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted);">
                <i class="fas fa-receipt" style="font-size: 1.8rem; opacity: 0.3; margin-bottom: 0.4rem; display: block;"></i>
                <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-main);">Bado Hakuna Miamala</div>
                <div style="font-size: 0.78rem;">Miamala itaonekana hapa moja kwa moja.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- Real-time Chart & Balance Switcher Initialization --}}
<script>
// Balance period switcher for Hero Card
window.switchBalancePeriod = function(period) {
    var data = {
        total: {
            amount: '{{ $netTotalRevenue > 0 ? number_format($netTotalRevenue) : "00" }}',
            label: 'Jumla Kuu',
            cash: 'Tsh {{ $netCashRevenue > 0 ? number_format($netCashRevenue) : "00" }}',
            online: 'Tsh {{ $onlineRevenue > 0 ? number_format($onlineRevenue) : "00" }}',
            expense: '-Tsh {{ $totalExpenses > 0 ? number_format($totalExpenses) : "00" }}'
        },
        today: {
            amount: '{{ $netRevenueToday > 0 ? number_format($netRevenueToday) : "00" }}',
            label: 'Leo',
            cash: 'Tsh {{ $netOfflineRevenueToday > 0 ? number_format($netOfflineRevenueToday) : "00" }}',
            online: 'Tsh {{ $onlineRevenueToday > 0 ? number_format($onlineRevenueToday) : "00" }}',
            expense: '-Tsh {{ $expensesToday > 0 ? number_format($expensesToday) : "00" }}'
        },
        month: {
            amount: '{{ $netRevenueMonth > 0 ? number_format($netRevenueMonth) : "00" }}',
            label: 'Mwezi Huu',
            cash: 'Tsh {{ $netOfflineRevenueMonth > 0 ? number_format($netOfflineRevenueMonth) : "00" }}',
            online: 'Tsh {{ $onlineRevenueMonth > 0 ? number_format($onlineRevenueMonth) : "00" }}',
            expense: '-Tsh {{ $expensesThisMonth > 0 ? number_format($expensesThisMonth) : "00" }}'
        }
    };

    var selected = data[period] || data.total;
    var amtEl = document.getElementById('heroMainAmount');
    var lblEl = document.getElementById('heroPeriodLabel');
    var cashEl = document.getElementById('heroCashVal');
    var onlineEl = document.getElementById('heroOnlineVal');
    var expEl = document.getElementById('heroExpenseVal');

    if (amtEl) amtEl.innerText = 'Tsh ' + selected.amount;
    if (lblEl) lblEl.innerText = selected.label;
    if (cashEl) cashEl.innerText = selected.cash;
    if (onlineEl) onlineEl.innerText = selected.online;
    if (expEl) expEl.innerText = selected.expense;
};

document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('salesChart');
    if (!ctx) return;

    var chartLabels = {!! json_encode($chartLabels) !!};
    var chartData = {!! json_encode($chartData) !!};

    var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(2, 132, 199, 0.3)');
    gradient.addColorStop(1, 'rgba(2, 132, 199, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Mauzo (Tsh)',
                data: chartData,
                borderColor: '#0284C7',
                borderWidth: 2.5,
                backgroundColor: gradient,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#FFFFFF',
                pointBorderColor: '#0284C7',
                pointBorderWidth: 2,
                pointRadius: 3.5,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#0284C7',
                pointHoverBorderColor: '#FFFFFF',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0F172A',
                    titleColor: '#94A3B8',
                    bodyColor: '#38BDF8',
                    titleFont: { family: 'Outfit', size: 11, weight: '600' },
                    bodyFont: { family: 'monospace', size: 13, weight: '800' },
                    padding: 8,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Tsh ' + Number(context.raw).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        color: '#94A3B8',
                        font: { family: 'Outfit', size: 11, weight: '600' }
                    }
                },
                y: {
                    grid: {
                        color: '#F1F5F9',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#94A3B8',
                        font: { family: 'Outfit', size: 11, weight: '600' },
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                            return value;
                        }
                    }
                }
            }
        }
    });

    // Realtime polling for recent transactions
    setInterval(function() {
        fetch('{{ route("admin.dashboard.recent_transactions") }}')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data || !data.transactions || data.transactions.length === 0) return;
                var container = document.getElementById('recent-transactions-container');
                if (!container) return;
                
                var html = '';
                data.transactions.slice(0, 5).forEach(function(txn) {
                    var isCash = txn.phone_number === 'Batch Print' || txn.phone_number === 'Mteja wa Cash' || txn.phone_number === 'Mauzo ya Vocha (Cash)' || txn.payment_method === 'cash';
                    var phoneLabel = isCash
                        ? (txn.phone_number === 'Batch Print' ? 'Vocha (Batch Print)' : (txn.phone_number === 'Mauzo ya Vocha (Cash)' || txn.phone_number === 'Mteja wa Cash' ? 'Vocha (Cash)' : txn.phone_number))
                        : txn.phone_number;
                    var pkg = txn.package_name || 'WiFi Kifurushi';
                    var voucherBadge = txn.mikrotik_voucher ? ' • ' + txn.mikrotik_voucher : '';
                    var avatarIcon = isCash ? 'fas fa-ticket-alt' : 'fas fa-mobile-alt';
                    var avatarColor = isCash ? '#FFFBEB' : '#EFF6FF';
                    var iconColor = isCash ? '#D97706' : '#2563EB';
                    
                    html += '<div style="padding: 0.65rem 1rem; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">' +
                        '<div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0; flex: 1;">' +
                            '<div style="width: 32px; height: 32px; border-radius: 8px; background: ' + avatarColor + '; color: ' + iconColor + '; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0;">' +
                                '<i class="' + avatarIcon + '"></i>' +
                            '</div>' +
                            '<div style="min-width: 0; flex: 1;">' +
                                '<div style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + phoneLabel + '</div>' +
                                '<div style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + pkg + voucherBadge + ' &bull; ' + txn.created_at_human + '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div style="text-align: right; flex-shrink: 0;">' +
                            '<div style="font-weight: 800; font-size: 0.92rem; color: var(--success); font-family: monospace;">+Tsh ' + txn.amount_formatted + '</div>' +
                        '</div>' +
                    '</div>';
                });
                container.innerHTML = html;
            })
            .catch(function(err) {
                console.warn('Dashboard sync:', err);
            });
    }, 15000);
});
</script>

@endsection
