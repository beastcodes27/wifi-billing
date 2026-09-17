@extends('layouts.app')

@section('title', 'Wateja Hewani & Wenye Bando')

@section('content')

<style>
    /* Live Sessions Compact Styling */
    .live-wrapper {
        width: 100%;
        margin: 0 auto;
        padding-bottom: 5rem;
    }

    .live-top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.85rem;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .live-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .live-header-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(16, 185, 129, 0.12);
        color: #10B981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .live-pulse-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseGreen 1.6s infinite;
        vertical-align: middle;
        margin-right: 3px;
    }

    @keyframes pulseGreen {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 5px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .live-header-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.2;
    }

    .live-header-sub {
        font-size: 0.76rem;
        color: var(--text-muted);
        margin: 0.1rem 0 0 0;
    }

    .btn-sync-live {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.85rem;
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        color: var(--text-main);
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: var(--shadow-xs);
        transition: all 0.2s ease;
    }

    .btn-sync-live:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #FF3300;
    }

    /* Live KPI Cards (4 Column Grid) */
    .live-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        margin-bottom: 0.85rem;
    }

    .live-kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 0.65rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.55rem;
        box-shadow: var(--shadow-xs);
    }

    .live-kpi-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .live-kpi-icon.green  { background: rgba(16, 185, 129, 0.12); color: #10B981; }
    .live-kpi-icon.gray   { background: #F1F5F9; color: #64748B; }
    .live-kpi-icon.purple { background: rgba(124, 58, 237, 0.12); color: #7C3AED; }
    .live-kpi-icon.orange { background: rgba(255, 51, 0, 0.12); color: #FF3300; }

    .live-kpi-number {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .live-kpi-label {
        font-size: 0.66rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-top: 1px;
    }

    /* Filter Tabs */
    .filter-tab {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.38rem 0.75rem;
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 9px;
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .filter-tab:hover {
        background: #F8FAFC;
        color: var(--text-main);
    }
    .filter-tab.active {
        background: #0F172A;
        color: #FFFFFF;
        border-color: #0F172A;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
    }

    /* Time Badges */
    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 0.74rem;
        font-weight: 800;
        white-space: nowrap;
        font-family: monospace;
    }
    .time-badge.ok        { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
    .time-badge.warning   { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
    .time-badge.danger    { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
    .time-badge.unlimited { background: rgba(255, 51, 0, 0.08); color: #FF3300; border: 1px solid rgba(255, 51, 0, 0.2); }

    /* Main Container */
    .live-main-panel {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        box-shadow: var(--shadow-xs);
        overflow: hidden;
    }

    .live-panel-header {
        padding: 0.75rem 1rem;
        background: #F8FAFC;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .live-panel-title {
        font-size: 0.88rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    /* Mobile Session Cards */
    .live-mobile-feed {
        display: flex;
        flex-direction: column;
    }

    .live-user-card {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border-color);
        background: #FFFFFF;
        transition: background 0.15s ease;
    }

    .live-user-card:last-child {
        border-bottom: none;
    }

    .live-user-card.is-online {
        background: #FFFFFF;
    }

    .live-user-card.is-offline {
        background: #FAFAFC;
    }

    @media (max-width: 768px) {
        .live-wrapper {
            padding-bottom: 5rem !important;
        }
        .live-header-sub {
            display: none !important;
        }
        .live-header-title {
            font-size: 1.1rem !important;
        }
        .live-kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.4rem !important;
            margin-bottom: 0.75rem !important;
        }
        .live-kpi-card {
            padding: 0.55rem 0.65rem !important;
            border-radius: 10px !important;
            gap: 0.45rem !important;
        }
        .live-kpi-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.8rem !important;
            border-radius: 7px !important;
        }
        .live-kpi-number {
            font-size: 0.98rem !important;
        }
        .live-kpi-label {
            font-size: 0.62rem !important;
        }
    }
</style>

<div class="live-wrapper">

    {{-- Top Header --}}
    <div class="live-top-header">
        <div class="live-title-wrap">
            <div class="live-header-icon">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <div>
                <h1 class="live-header-title">Wateja Hewani &amp; Wenye Bando</h1>
                <p class="live-header-sub">Fuatilia wateja waliopo live sasa hivi na wenye vifurushi vinavyoendelea</p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.45rem;">
            <div id="lastUpdated" style="font-size: 0.74rem; color: var(--text-muted); background: #F8FAFC; border: 1px solid var(--border-color); padding: 0.35rem 0.65rem; border-radius: 7px;">
                <span class="live-pulse-dot"></span> Inakagua...
            </div>
            <button id="refreshBtn" onclick="fetchLiveData()" class="btn-sync-live" title="Sawazisha">
                <i class="fas fa-sync-alt"></i> Sawazisha
            </button>
        </div>
    </div>

    {{-- Live KPI Cards Grid (4 Cards) --}}
    <div class="live-kpi-grid">
        <div class="live-kpi-card">
            <div class="live-kpi-icon green">
                <i class="fas fa-wifi"></i>
            </div>
            <div style="min-width: 0;">
                <div class="live-kpi-number" id="kpi-live-count" style="color: #10B981;">0</div>
                <div class="live-kpi-label">Live Hewani</div>
            </div>
        </div>

        <div class="live-kpi-card">
            <div class="live-kpi-icon gray">
                <i class="fas fa-power-off"></i>
            </div>
            <div style="min-width: 0;">
                <div class="live-kpi-number" id="kpi-offline-count" style="color: #64748B;">0</div>
                <div class="live-kpi-label">Hayupo Live</div>
            </div>
        </div>

        <div class="live-kpi-card">
            <div class="live-kpi-icon purple">
                <i class="fas fa-layer-group"></i>
            </div>
            <div style="min-width: 0;">
                <div class="live-kpi-number" id="kpi-total-count" style="color: #7C3AED;">0</div>
                <div class="live-kpi-label">Jumla Wenye Bando</div>
            </div>
        </div>

        <div class="live-kpi-card">
            <div class="live-kpi-icon orange">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <div style="min-width: 0;">
                <div class="live-kpi-number" id="kpi-data-count">0 MB</div>
                <div class="live-kpi-label">Data Jumla</div>
            </div>
        </div>
    </div>

    {{-- Quick Filter Tabs --}}
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
            <button onclick="setFilter('all')" id="filter-all" class="filter-tab active">
                <i class="fas fa-users"></i> Wote (<span id="cnt-all">0</span>)
            </button>
            <button onclick="setFilter('live')" id="filter-live" class="filter-tab">
                <span class="live-pulse-dot"></span> Live Hewani (<span id="cnt-live">0</span>)
            </button>
            <button onclick="setFilter('offline')" id="filter-offline" class="filter-tab">
                <i class="fas fa-power-off"></i> Hayupo Live (<span id="cnt-offline">0</span>)
            </button>
        </div>
        <div style="font-size: 0.74rem; color: var(--text-muted); font-weight: 600;">
            <i class="fas fa-info-circle"></i> Inaboreshwa kila sekunde 5
        </div>
    </div>

    {{-- Main Panel --}}
    <div class="live-main-panel">
        <div class="live-panel-header">
            <div class="live-panel-title">
                <i class="fas fa-stream" style="color: #FF3300;"></i>
                <span id="panel-title-text">Orodha ya Wateja</span>
            </div>
            <span id="session-count-badge" class="badge-status badge-success" style="font-size: 0.7rem; padding: 0.15rem 0.55rem;">
                0 Wateja
            </span>
        </div>

        {{-- Desktop Table View --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table-banking" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 0.7rem 0.9rem; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; width: 35px;">#</th>
                        <th style="padding: 0.7rem 0.9rem; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Mteja / Vocha</th>
                        <th style="padding: 0.7rem 0.9rem; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Kifurushi</th>
                        <th style="padding: 0.7rem 0.9rem; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Kifaa / IP</th>
                        <th style="padding: 0.7rem 0.9rem; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Data</th>
                        <th style="padding: 0.7rem 0.9rem; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Alianza</th>
                        <th style="padding: 0.7rem 0.9rem; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Muda Uliobaki</th>
                        <th style="padding: 0.7rem 0.9rem; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: center;">Hali ya Sasa</th>
                    </tr>
                </thead>
                <tbody id="liveSessionsBody">
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2.5rem 1rem;">
                            <div style="color: var(--text-muted);">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: #F1F5F9; color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin: 0 auto 0.75rem auto;">
                                    <i class="fas fa-users-slash"></i>
                                </div>
                                <div style="font-weight: 800; font-size: 0.92rem; color: var(--text-main); margin-bottom: 0.2rem;">Hakuna wateja wenye bando hai</div>
                                <div style="font-size: 0.78rem;">Wateja wakijiunga au wakiwa na bando wataonekana hapa moja kwa moja.</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card Feed --}}
        <div class="d-md-none" id="liveSessionsMobileList">
            <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #F1F5F9; color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin: 0 auto 0.65rem auto;">
                    <i class="fas fa-users-slash"></i>
                </div>
                <div style="font-weight: 800; font-size: 0.92rem; color: var(--text-main);">Hakuna wateja</div>
                <div style="font-size: 0.78rem; margin-top: 0.2rem;">Wateja wataonekana hapa.</div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    var _allData = [];
    var _activeFilter = 'all'; // 'all' | 'live' | 'offline'

    function setFilter(filter) {
        _activeFilter = filter;
        document.querySelectorAll('.filter-tab').forEach(function(el) { el.classList.remove('active'); });
        var targetBtn = document.getElementById('filter-' + filter);
        if (targetBtn) targetBtn.classList.add('active');
        renderFilteredData();
    }

    function formatTimeLeft(seconds) {
        seconds = Math.max(0, Math.floor(Number(seconds) || 0));
        if (seconds <= 0) return 'Imeisha';
        var d = Math.floor(seconds / 86400);
        var h = Math.floor((seconds % 86400) / 3600);
        var m = Math.floor((seconds % 3600) / 60);
        var s = Math.floor(seconds % 60);
        if (d > 0) return d + 'd ' + String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
        return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
    }

    function getTimeClass(seconds, origClass) {
        if (origClass === 'unlimited') return 'unlimited';
        if (seconds <= 0)    return 'danger';
        if (seconds < 600)   return 'danger';
        if (seconds < 3600)  return 'warning';
        return 'ok';
    }

    function getTimeIcon(cls) {
        if (cls === 'danger')    return '<i class="fas fa-exclamation-triangle"></i>';
        if (cls === 'warning')   return '<i class="fas fa-hourglass-half"></i>';
        if (cls === 'unlimited') return '<i class="fas fa-infinity"></i>';
        return '<i class="fas fa-clock"></i>';
    }

    // Client-side countdown tick every 1s
    setInterval(function() {
        _allData.forEach(function(s, idx) {
            if (s.time_left_class === 'unlimited') return;
            var currentSecs = Math.max(0, Math.floor(Number(s.time_left_seconds) || 0));
            if (currentSecs > 0) currentSecs--;
            s.time_left_seconds = currentSecs;
            var cls  = getTimeClass(currentSecs, s.time_left_class);
            var content = getTimeIcon(cls) + ' ' + formatTimeLeft(currentSecs);

            var el = document.getElementById('tl-' + s.id);
            if (el) {
                el.className = 'time-badge ' + cls;
                el.innerHTML = content;
            }

            var elMob = document.getElementById('tl-mob-' + s.id);
            if (elMob) {
                elMob.className = 'time-badge ' + cls;
                elMob.innerHTML = content;
            }
        });
    }, 1000);

    function buildRow(session, idx) {
        var cls  = session.time_left_class || 'ok';
        var secs = session.time_left_seconds || 0;
        var icon = getTimeIcon(cls);
        var timeDisplay = (secs <= 0) ? 'Imeisha' : formatTimeLeft(secs);

        var expiresInfo = session.expires_at
            ? '<div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 2px;">' + session.expires_at + '</div>'
            : '';

        var statusBadge = session.is_live
            ? '<span class="badge-status badge-success" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; font-weight: 800;"><span class="live-pulse-dot"></span> Live Hewani</span>'
            : '<span class="badge-status" style="font-size: 0.72rem; padding: 0.2rem 0.55rem; font-weight: 700; background: #F1F5F9; color: #64748B; border: 1px solid #CBD5E1;"><i class="fas fa-power-off" style="margin-right: 3px;"></i> Hayupo Live</span>';

        var rowBg = session.is_live ? 'background: #FFFFFF;' : 'background: #FAFBFD;';

        return '<tr style="border-bottom: 1px solid var(--border-color); ' + rowBg + '">'
            + '<td style="padding: 0.75rem 0.9rem; color: var(--text-muted); font-weight: 700; font-size: 0.76rem;">' + (idx + 1) + '</td>'
            + '<td style="padding: 0.75rem 0.9rem;">'
            +   '<div style="font-weight: 800; color: var(--text-main); font-size: 0.86rem;">' + session.phone + '</div>'
            +   (session.voucher && session.phone.indexOf(session.voucher) === -1 ? '<div style="font-size: 0.7rem; color: var(--text-muted); font-family: monospace;">Vocha: ' + session.voucher + '</div>' : '')
            + '</td>'
            + '<td style="padding: 0.75rem 0.9rem;"><span style="background: rgba(255, 51, 0, 0.08); color: #FF3300; padding: 0.15rem 0.45rem; border-radius: 5px; font-weight: 700; font-size: 0.72rem;">' + session.package + '</span></td>'
            + '<td style="padding: 0.75rem 0.9rem;">'
            +   '<div style="color: var(--text-main); font-weight: 600; font-size: 0.8rem;">' + session.device + '</div>'
            +   (session.ip && session.ip !== 'N/A' ? '<div style="color: ' + (session.is_live ? '#10B981' : 'var(--text-muted)') + '; font-family: monospace; font-size: 0.72rem; font-weight: 700;">IP: ' + session.ip + '</div>' : '')
            + '</td>'
            + '<td style="padding: 0.75rem 0.9rem;"><span style="color: var(--text-main); font-weight: 800; font-size: 0.82rem; font-family: monospace;">' + session.data_used + '</span></td>'
            + '<td style="padding: 0.75rem 0.9rem;">'
            +   '<div style="color: var(--text-main); font-weight: 600; font-size: 0.8rem;">' + session.activated_at + '</div>'
            +   '<div style="color: var(--text-muted); font-size: 0.7rem;">' + session.activated_human + '</div>'
            + '</td>'
            + '<td style="padding: 0.75rem 0.9rem;">'
            +   '<span class="time-badge ' + cls + '" id="tl-' + session.id + '">' + icon + ' ' + timeDisplay + '</span>'
            +   expiresInfo
            + '</td>'
            + '<td style="padding: 0.75rem 0.9rem; text-align: center;">'
            +   statusBadge
            + '</td>'
            + '</tr>';
    }

    function buildMobileCard(session, idx) {
        var cls  = session.time_left_class || 'ok';
        var secs = session.time_left_seconds || 0;
        var icon = getTimeIcon(cls);
        var timeDisplay = (secs <= 0) ? 'Imeisha' : formatTimeLeft(secs);

        var statusBadge = session.is_live
            ? '<span class="badge-status badge-success" style="font-size: 0.65rem; padding: 0.12rem 0.45rem; font-weight: 800;"><span class="live-pulse-dot"></span> Live</span>'
            : '<span class="badge-status" style="font-size: 0.65rem; padding: 0.12rem 0.45rem; background: #F1F5F9; color: #64748B; border: 1px solid #CBD5E1;"><i class="fas fa-power-off"></i> Hayupo Live</span>';

        var cardClass = session.is_live ? 'live-user-card is-online' : 'live-user-card is-offline';
        var iconBg = session.is_live ? 'background: rgba(16, 185, 129, 0.12); color: #10B981;' : 'background: #F1F5F9; color: #64748B;';
        var iconSym = session.is_live ? 'fa-wifi' : 'fa-power-off';

        var deviceSub = session.is_live
            ? (session.ip && session.ip !== 'N/A' ? 'IP: ' + session.ip : session.device)
            : 'Hajaunganishwa mtandaoni kwa sasa';

        return '<div class="' + cardClass + '">'
            + '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">'
            +   '<div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">'
            +     '<div style="width: 32px; height: 32px; border-radius: 8px; ' + iconBg + ' display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 800; flex-shrink: 0;">'
            +       '<i class="fas ' + iconSym + '"></i>'
            +     '</div>'
            +     '<div style="min-width: 0;">'
            +       '<div style="font-weight: 800; font-size: 0.88rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + session.phone + '</div>'
            +       '<div style="font-size: 0.72rem; color: var(--text-muted);">' + deviceSub + '</div>'
            +     '</div>'
            +   '</div>'
            +   '<div>'
            +     statusBadge
            +   '</div>'
            + '</div>'
            + '<div style="background: #F8FAFC; padding: 0.45rem 0.7rem; border-radius: 8px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; font-size: 0.76rem; margin-bottom: 0.4rem;">'
            +   '<span style="font-weight: 700; color: var(--text-secondary);"><i class="fas fa-layer-group" style="margin-right: 3px; color: #FF3300;"></i>' + session.package + '</span>'
            +   '<span style="font-weight: 800; color: var(--text-main); font-family: monospace;">Data: ' + session.data_used + '</span>'
            + '</div>'
            + '<div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.74rem;">'
            +   '<span style="color: var(--text-muted);"><i class="fas fa-calendar-alt" style="margin-right: 2px;"></i>' + session.activated_human + '</span>'
            +   '<span class="time-badge ' + cls + '" id="tl-mob-' + session.id + '">' + icon + ' ' + timeDisplay + '</span>'
            + '</div>'
            + '</div>';
    }

    function renderFilteredData() {
        const tbody = document.getElementById('liveSessionsBody');
        const mobList = document.getElementById('liveSessionsMobileList');
        const badge = document.getElementById('session-count-badge');

        var filtered = _allData;
        if (_activeFilter === 'live') {
            filtered = _allData.filter(function(s) { return s.is_live; });
        } else if (_activeFilter === 'offline') {
            filtered = _allData.filter(function(s) { return !s.is_live; });
        }

        if (badge) {
            badge.innerText = filtered.length + ' Wateja';
        }

        if (filtered.length === 0) {
            var msg = _activeFilter === 'live'
                ? 'Hakuna wateja waliopo live mtandaoni kwa sasa.'
                : (_activeFilter === 'offline' ? 'Hakuna wateja walio nje ya mtandao kwa sasa.' : 'Hakuna wateja wenye vifurushi vinavyoendelea.');

            var emptyHtml = '<div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">'
                + '<div style="width: 44px; height: 44px; border-radius: 50%; background: #F1F5F9; color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin: 0 auto 0.65rem auto;"><i class="fas fa-users-slash"></i></div>'
                + '<div style="font-weight: 800; font-size: 0.92rem; color: var(--text-main); margin-bottom: 0.2rem;">' + msg + '</div>'
                + '<div style="font-size: 0.78rem; color: var(--text-muted);">Wateja wakijiunga au wakiwa na bando wataonekana hapa.</div>'
                + '</div>';

            if (tbody) tbody.innerHTML = '<tr><td colspan="8">' + emptyHtml + '</td></tr>';
            if (mobList) mobList.innerHTML = emptyHtml;
            return;
        }

        if (tbody) {
            tbody.innerHTML = filtered.map(function(session, idx) {
                return buildRow(session, idx);
            }).join('');
        }

        if (mobList) {
            mobList.innerHTML = filtered.map(function(session, idx) {
                return buildMobileCard(session, idx);
            }).join('');
        }
    }

    function fetchLiveData() {
        const btn = document.getElementById('refreshBtn');
        const icon = btn ? btn.querySelector('.fa-sync-alt') : null;
        const kpiLive = document.getElementById('kpi-live-count');
        const kpiOffline = document.getElementById('kpi-offline-count');
        const kpiTotal = document.getElementById('kpi-total-count');
        const kpiData = document.getElementById('kpi-data-count');
        const cntAll = document.getElementById('cnt-all');
        const cntLive = document.getElementById('cnt-live');
        const cntOffline = document.getElementById('cnt-offline');

        if (icon) icon.classList.add('fa-spin');

        fetch('{{ route("admin.live_sessions.data") }}')
            .then(function(res) {
                if (!res.ok) throw new Error('Server error');
                return res.json();
            })
            .then(function(data) {
                if (icon) icon.classList.remove('fa-spin');

                _allData = data || [];

                var liveCount = 0;
                var offlineCount = 0;
                _allData.forEach(function(s) {
                    if (s.is_live) liveCount++;
                    else offlineCount++;
                });

                var now = new Date();
                var timeStr = now.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                document.getElementById('lastUpdated').innerHTML =
                    '<span class="live-pulse-dot"></span> <strong>' + timeStr + '</strong> &bull; <strong>' + liveCount + '</strong> Live / <strong>' + _allData.length + '</strong> Jumla';

                if (kpiLive) kpiLive.innerText = liveCount;
                if (kpiOffline) kpiOffline.innerText = offlineCount;
                if (kpiTotal) kpiTotal.innerText = _allData.length;

                if (cntAll) cntAll.innerText = _allData.length;
                if (cntLive) cntLive.innerText = liveCount;
                if (cntOffline) cntOffline.innerText = offlineCount;

                renderFilteredData();
            })
            .catch(function(error) {
                if (icon) icon.classList.remove('fa-spin');
                const tbody = document.getElementById('liveSessionsBody');
                const mobList = document.getElementById('liveSessionsMobileList');
                const errHtml = '<div style="text-align: center; padding: 2rem 1rem; color: var(--danger);">'
                    + '<div style="font-weight: 700; margin-bottom: 0.25rem;"><i class="fas fa-exclamation-triangle"></i> Hitilafu ya Mawasiliano</div>'
                    + '<div style="font-size: 0.78rem; color: var(--text-muted);">' + error.message + '</div>'
                    + '</div>';
                if (tbody) tbody.innerHTML = '<tr><td colspan="8">' + errHtml + '</td></tr>';
                if (mobList) mobList.innerHTML = errHtml;
            });
    }

    document.addEventListener('DOMContentLoaded', fetchLiveData);
    setInterval(fetchLiveData, 5000);
</script>
@endpush

@endsection
