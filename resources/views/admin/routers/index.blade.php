@extends('layouts.app')

@section('title', 'Routers')

@section('content')
<style>
    /* Routers Module Design System */
    .routers-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding-bottom: 3rem;
    }

    /* Top Page Header */
    .routers-top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 0.75rem;
    }

    .routers-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .routers-header-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(255, 51, 0, 0.1);
        color: #FF3300;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .routers-header-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.2;
    }

    .routers-header-sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0.15rem 0 0 0;
    }

    .btn-create-router {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.6rem 1.15rem;
        background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%);
        color: #FFFFFF !important;
        border-radius: 12px;
        font-size: 0.84rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 3px 10px rgba(255, 51, 0, 0.28);
        transition: all var(--transition);
        white-space: nowrap;
        border: none;
    }

    .btn-create-router:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(255, 51, 0, 0.38);
        color: #FFFFFF !important;
    }

    .btn-create-router:active {
        transform: scale(0.97);
    }

    /* KPI Stats Grid */
    .routers-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .router-kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: var(--shadow-xs);
        transition: transform var(--transition), box-shadow var(--transition);
    }

    .router-kpi-card:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .kpi-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .kpi-icon-orange {
        background: rgba(255, 51, 0, 0.1);
        color: #FF3300;
    }

    .kpi-icon-green {
        background: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }

    .kpi-icon-indigo {
        background: rgba(99, 102, 241, 0.1);
        color: #6366F1;
    }

    .kpi-info-box {
        min-width: 0;
        flex: 1;
    }

    .kpi-number {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    .kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-top: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Main Routers Panel */
    .routers-main-panel {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-xs);
        overflow: hidden;
    }

    .panel-toolbar {
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid var(--border-color);
        background: #FAFAFC;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .panel-title-group {
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .panel-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .panel-title i {
        color: #FF3300;
    }

    .panel-count-badge {
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--text-secondary);
        background: #F1F5F9;
        border: 1px solid var(--border-color);
        padding: 0.15rem 0.55rem;
        border-radius: var(--radius-pill);
    }

    .panel-search-box {
        position: relative;
        min-width: 220px;
    }

    .panel-search-input {
        width: 100%;
        padding: 0.45rem 0.8rem 0.45rem 2.1rem;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: #FFFFFF;
        font-size: 0.82rem;
        color: var(--text-main);
        outline: none;
        transition: all var(--transition);
    }

    .panel-search-input:focus {
        border-color: #FF3300;
        box-shadow: 0 0 0 3px rgba(255, 51, 0, 0.12);
    }

    .panel-search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.8rem;
        color: var(--text-muted);
        pointer-events: none;
    }

    /* Mobile Feed Cards */
    .routers-mobile-feed {
        display: flex;
        flex-direction: column;
    }

    .router-mobile-card {
        padding: 1rem 1.05rem;
        border-bottom: 1px solid var(--border-color);
        background: #FFFFFF;
        transition: background 0.15s ease;
    }

    .router-mobile-card:last-child {
        border-bottom: none;
    }

    .router-mobile-card:active {
        background: #F8FAFC;
    }

    .router-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.65rem;
    }

    .router-avatar-info {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        min-width: 0;
        flex: 1;
    }

    .router-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 51, 0, 0.08);
        color: #FF3300;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.98rem;
        flex-shrink: 0;
    }

    .router-text-block {
        min-width: 0;
        flex: 1;
    }

    .router-card-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.25;
    }

    .router-site-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
    }

    .router-site-pill i {
        color: #6366F1;
        font-size: 0.7rem;
    }

    /* Status Badges */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 0.2rem 0.6rem;
        border-radius: var(--radius-pill);
        font-size: 0.74rem;
        font-weight: 800;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .status-pill-active {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #065F46;
    }

    .status-pill-inactive {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
    }

    .pulse-dot-green {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 6px #10B981;
        display: inline-block;
    }

    .dot-red {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #EF4444;
        display: inline-block;
    }

    /* Specs Strip */
    .router-specs-strip {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.45rem;
        background: #F8FAFC;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.45rem 0.75rem;
        margin-bottom: 0.65rem;
    }

    .spec-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.76rem;
        color: var(--text-secondary);
    }

    .spec-item i {
        color: var(--text-muted);
        font-size: 0.74rem;
    }

    .spec-item code {
        font-family: 'Fira Code', 'Courier New', monospace;
        font-weight: 700;
        color: #0F172A;
        font-size: 0.78rem;
    }

    .spec-separator {
        color: var(--border-color);
        font-size: 0.8rem;
    }

    /* Action Buttons Row */
    .router-actions-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.45rem;
        padding-top: 0.45rem;
    }

    .btn-action-script {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.42rem 0.85rem;
        background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%);
        color: #FFFFFF !important;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(255, 51, 0, 0.25);
        transition: all var(--transition);
        white-space: nowrap;
    }

    .btn-action-script:active {
        transform: scale(0.96);
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        text-decoration: none;
        transition: all var(--transition);
        border: 1px solid var(--border-color);
        background: #FFFFFF;
        cursor: pointer;
    }

    .btn-action-edit {
        color: var(--text-secondary);
    }

    .btn-action-edit:hover {
        background: #F1F5F9;
        color: var(--text-main);
        border-color: #CBD5E1;
    }

    .btn-action-delete {
        color: #EF4444;
        background: #FFF5F5;
        border-color: #FEE2E2;
    }

    .btn-action-delete:hover {
        background: #FEF2F2;
        color: #DC2626;
        border-color: #FECACA;
    }

    /* Desktop Table */
    .routers-desktop-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .routers-desktop-table th {
        background: #FAFAFC;
        padding: 0.75rem 1.15rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1px solid var(--border-color);
        text-align: left;
    }

    .routers-desktop-table td {
        padding: 0.9rem 1.15rem;
        font-size: 0.85rem;
        color: var(--text-main);
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        background: #FFFFFF;
        transition: background 0.15s ease;
    }

    .routers-desktop-table tr:hover td {
        background: #F8FAFC;
    }

    .routers-desktop-table tr:last-child td {
        border-bottom: none;
    }

    /* Empty State */
    .routers-empty-state {
        text-align: center;
        padding: 3.5rem 1.5rem;
        color: var(--text-muted);
    }

    .empty-state-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: rgba(255, 51, 0, 0.08);
        color: #FF3300;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 0.35rem 0;
    }

    .empty-state-text {
        font-size: 0.86rem;
        color: var(--text-muted);
        max-width: 420px;
        margin: 0 auto 1.25rem auto;
        line-height: 1.45;
    }

    /* Mobile Edge-to-Edge Responsiveness */
    @media (max-width: 768px) {
        .routers-wrapper {
            padding-bottom: 5rem !important;
        }

        .routers-top-header {
            margin-bottom: 0.75rem !important;
        }

        .routers-header-title {
            font-size: 1.15rem !important;
        }

        .routers-header-sub {
            display: none !important;
        }

        .btn-create-router {
            padding: 0.5rem 0.85rem !important;
            font-size: 0.8rem !important;
            border-radius: 10px !important;
        }

        .routers-kpi-grid {
            gap: 0.45rem !important;
            margin-bottom: 0.75rem !important;
        }

        .router-kpi-card {
            padding: 0.65rem 0.7rem !important;
            border-radius: 12px !important;
            gap: 0.45rem !important;
        }

        .kpi-icon-box {
            width: 30px !important;
            height: 30px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
        }

        .kpi-number {
            font-size: 1.05rem !important;
        }

        .kpi-label {
            font-size: 0.66rem !important;
        }

        .panel-toolbar {
            padding: 0.75rem 0.9rem !important;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem !important;
        }

        .panel-search-box {
            min-width: 100% !important;
        }

        .panel-search-input {
            padding-top: 0.55rem;
            padding-bottom: 0.55rem;
        }

        .routers-main-panel {
            border-radius: 14px !important;
        }

        .router-mobile-card {
            padding: 0.85rem 0.9rem !important;
        }
    }
</style>

<div class="routers-wrapper">

    {{-- Top Header --}}
    <div class="routers-top-header">
        <div class="routers-title-wrap">
            <div class="routers-header-icon">
                <i class="fas fa-network-wired"></i>
            </div>
            <div>
                <h1 class="routers-header-title">Routers</h1>
                <p class="routers-header-sub">Vifaa vya mtandao na MikroTik</p>
            </div>
        </div>
        <a href="{{ route('admin.routers.create') }}" class="btn-create-router">
            <i class="fas fa-plus"></i>
            <span>Ongeza Router</span>
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.65rem; border-radius:12px; margin-bottom:0.85rem; font-size:0.85rem; font-weight:700;">
        <i class="fas fa-check-circle" style="color:#059669; font-size:1rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- KPI Overview Grid --}}
    <div class="routers-kpi-grid">
        {{-- Card 1: Total Routers --}}
        <div class="router-kpi-card">
            <div class="kpi-icon-box kpi-icon-orange">
                <i class="fas fa-network-wired"></i>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-number">{{ $routers->count() }}</div>
                <div class="kpi-label">Routers Zote</div>
            </div>
        </div>

        {{-- Card 2: Active / Hewani --}}
        <div class="router-kpi-card">
            <div class="kpi-icon-box kpi-icon-green">
                <i class="fas fa-signal"></i>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-number">{{ $routers->where('status', 'active')->count() }}</div>
                <div class="kpi-label">Hewani</div>
            </div>
        </div>

        {{-- Card 3: Sites --}}
        <div class="router-kpi-card">
            <div class="kpi-icon-box kpi-icon-indigo">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="kpi-info-box">
                <div class="kpi-number">{{ $routers->pluck('site_id')->unique()->filter()->count() }}</div>
                <div class="kpi-label">Maeneo (Sites)</div>
            </div>
        </div>
    </div>

    {{-- Routers List Panel --}}
    <div class="routers-main-panel">
        {{-- Panel Header & Instant Search --}}
        <div class="panel-toolbar">
            <div class="panel-title-group">
                <h2 class="panel-title">
                    <i class="fas fa-server"></i>
                    <span>Vifaa vya Mtandao</span>
                </h2>
                <span class="panel-count-badge" id="routerCountBadge">{{ $routers->count() }} router(s)</span>
            </div>
            <div class="panel-search-box">
                <i class="fas fa-search panel-search-icon"></i>
                <input type="text" id="routerSearchInput" class="panel-search-input" placeholder="Tafuta jina, eneo au IP..." onkeyup="filterRouters()">
            </div>
        </div>

        @if($routers->isEmpty())
        {{-- Empty State --}}
        <div class="routers-empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-network-wired"></i>
            </div>
            <h3 class="empty-state-title">Hakuna Router Iliyosajiliwa</h3>
            <p class="empty-state-text">
                Sajili kifaa cha MikroTik ili kuanza kutoa huduma na kusawazisha vocha kwenye mtandao wako.
            </p>
            <a href="{{ route('admin.routers.create') }}" class="btn-create-router">
                <i class="fas fa-plus"></i>
                <span>Sajili Router ya Kwanza</span>
            </a>
        </div>
        @else

        {{-- Desktop Table View --}}
        <div class="d-none d-md-block table-responsive" style="margin:0;">
            <table class="routers-desktop-table">
                <thead>
                    <tr>
                        <th>Jina la Router</th>
                        <th>Site (Eneo)</th>
                        <th>Management IP</th>
                        <th>Aina ya Kifaa</th>
                        <th style="text-align:center;">Hali (Status)</th>
                        <th style="text-align:right;">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="routersTableBody">
                    @foreach($routers as $router)
                    <tr class="router-row-item" data-search="{{ strtolower($router->name . ' ' . ($router->site->name ?? '') . ' ' . $router->management_ip . ' ' . $router->router_type) }}">
                        <td>
                            <div style="display:flex; align-items:center; gap:0.65rem;">
                                <div class="router-icon-box">
                                    <i class="fas fa-network-wired"></i>
                                </div>
                                <div>
                                    <div style="font-weight:800; color:var(--text-main); font-size:0.92rem;">{{ $router->name }}</div>
                                    @if($router->notes)
                                        <div style="font-size:0.75rem; color:var(--text-muted); max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            {{ $router->notes }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="router-site-pill" style="font-size:0.8rem;">
                                <i class="fas fa-map-marker-alt"></i> {{ $router->site->name ?? 'Haijapangwa' }}
                            </span>
                        </td>
                        <td>
                            <code style="background:#F1F5F9; padding:0.25rem 0.55rem; border-radius:6px; font-size:0.8rem; border:1px solid var(--border-color); color:#0F172A; font-family:'Fira Code', monospace;">
                                {{ $router->management_ip ?: '0.0.0.0 (Auto-Sync)' }}
                            </code>
                        </td>
                        <td>
                            <span style="font-size:0.82rem; font-weight:700; color:var(--text-secondary);">
                                <i class="fas fa-microchip" style="color:var(--text-muted); font-size:0.75rem; margin-right:3px;"></i> {{ $router->router_type }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            @if($router->status === 'active')
                                <span class="status-pill status-pill-active">
                                    <span class="pulse-dot-green"></span> Hewani
                                </span>
                            @else
                                <span class="status-pill status-pill-inactive">
                                    <span class="dot-red"></span> Haipo
                                </span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:0.4rem; align-items:center;">
                                <a href="{{ route('admin.routers.provision', $router) }}" class="btn-action-script" title="Kodi ya Kuunganisha MikroTik">
                                    <i class="fas fa-terminal"></i>
                                    <span>Script</span>
                                </a>
                                <a href="{{ route('admin.routers.edit', $router) }}" class="btn-action-icon btn-action-edit" title="Hariri">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.routers.destroy', $router) }}" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Una uhakika unataka kufuta router hii?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon btn-action-delete" title="Futa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card Feed --}}
        <div class="d-md-none routers-mobile-feed" id="routersMobileFeed">
            @foreach($routers as $router)
            <div class="router-mobile-card router-card-item" data-search="{{ strtolower($router->name . ' ' . ($router->site->name ?? '') . ' ' . $router->management_ip . ' ' . $router->router_type) }}">
                {{-- Top Identity & Status --}}
                <div class="router-card-top">
                    <div class="router-avatar-info">
                        <div class="router-icon-box">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="router-text-block">
                            <h3 class="router-card-title">{{ $router->name }}</h3>
                            <div class="router-site-pill">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $router->site->name ?? 'Haijapangwa' }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        @if($router->status === 'active')
                            <span class="status-pill status-pill-active">
                                <span class="pulse-dot-green"></span> Hewani
                            </span>
                        @else
                            <span class="status-pill status-pill-inactive">
                                <span class="dot-red"></span> Haipo
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Specs Strip (IP & Hardware Type) --}}
                <div class="router-specs-strip">
                    <div class="spec-item">
                        <i class="fas fa-ethernet"></i>
                        <code>{{ $router->management_ip ?: 'Auto-Sync IP' }}</code>
                    </div>
                    <span class="spec-separator">•</span>
                    <div class="spec-item">
                        <i class="fas fa-microchip"></i>
                        <span>{{ $router->router_type }}</span>
                    </div>
                </div>

                {{-- Action Buttons Bar --}}
                <div class="router-actions-bar">
                    <a href="{{ route('admin.routers.provision', $router) }}" class="btn-action-script">
                        <i class="fas fa-terminal"></i>
                        <span>Script</span>
                    </a>
                    <a href="{{ route('admin.routers.edit', $router) }}" class="btn-action-icon btn-action-edit" title="Hariri">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.routers.destroy', $router) }}" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Una uhakika unataka kufuta router hii?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action-icon btn-action-delete" title="Futa">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        {{-- No Search Results State --}}
        <div id="noSearchResults" style="display:none; text-align:center; padding:2.5rem 1rem; color:var(--text-muted);">
            <i class="fas fa-search" style="font-size:1.75rem; opacity:0.3; margin-bottom:0.6rem; display:block;"></i>
            <div style="font-weight:700; color:var(--text-main); font-size:0.92rem;">Hakuna router inayolingana na utafutaji wako</div>
            <div style="font-size:0.78rem; margin-top:0.25rem;">Jaribu kuandika jina au IP tofauti.</div>
        </div>

        @endif
    </div>
</div>

<script>
    function filterRouters() {
        const query = document.getElementById('routerSearchInput').value.toLowerCase().trim();
        const desktopRows = document.querySelectorAll('.router-row-item');
        const mobileCards = document.querySelectorAll('.router-card-item');
        const noResults = document.getElementById('noSearchResults');
        const badge = document.getElementById('routerCountBadge');

        let visibleCount = 0;

        // Desktop
        desktopRows.forEach(row => {
            const data = row.getAttribute('data-search') || '';
            if (data.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Mobile
        mobileCards.forEach(card => {
            const data = card.getAttribute('data-search') || '';
            if (data.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        if (badge) {
            badge.innerText = visibleCount + ' router(s)';
        }

        if (noResults) {
            if (visibleCount === 0 && (desktopRows.length > 0 || mobileCards.length > 0)) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
    }
</script>
@endsection

