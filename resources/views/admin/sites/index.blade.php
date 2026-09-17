@extends('layouts.app')

@section('title', 'Maeneo (Sites)')

@section('content')
<style>
    /* Sites Module Styling */
    .sites-wrapper {
        width: 100%;
        margin: 0 auto;
        padding-bottom: 3rem;
    }

    .sites-top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 0.75rem;
    }

    .sites-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .sites-header-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.1);
        color: #6366F1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .sites-header-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.2;
    }

    .sites-header-sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0.15rem 0 0 0;
    }

    .btn-create-site {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.6rem 1.15rem;
        background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%);
        color: #FFFFFF !important;
        border-radius: 12px;
        font-size: 0.84rem;
        font-weight: 800;
        box-shadow: 0 3px 10px rgba(255, 51, 0, 0.28);
        border: none;
        cursor: pointer;
        transition: all var(--transition);
        white-space: nowrap;
    }

    .btn-create-site:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(255, 51, 0, 0.38);
    }

    /* KPI Grid */
    .sites-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .site-kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: var(--shadow-xs);
    }

    .site-kpi-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .site-kpi-icon.indigo { background: rgba(99, 102, 241, 0.1); color: #6366F1; }
    .site-kpi-icon.orange { background: rgba(255, 51, 0, 0.1); color: #FF3300; }
    .site-kpi-icon.green  { background: rgba(16, 185, 129, 0.1); color: #10B981; }

    .site-kpi-number {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    .site-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-top: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Main Container */
    .sites-main-panel {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-xs);
        overflow: hidden;
    }

    .sites-toolbar {
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid var(--border-color);
        background: #FAFAFC;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .sites-title-group {
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .sites-panel-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .sites-panel-title i {
        color: #6366F1;
    }

    .sites-count-badge {
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--text-secondary);
        background: #F1F5F9;
        border: 1px solid var(--border-color);
        padding: 0.15rem 0.55rem;
        border-radius: var(--radius-pill);
    }

    .sites-search-box {
        position: relative;
        min-width: 220px;
    }

    .sites-search-input {
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

    .sites-search-input:focus {
        border-color: #6366F1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .sites-search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.8rem;
        color: var(--text-muted);
        pointer-events: none;
    }

    /* Mobile Site Cards */
    .site-mobile-card {
        padding: 1rem 1.05rem;
        border-bottom: 1px solid var(--border-color);
        background: #FFFFFF;
    }

    .site-mobile-card:last-child {
        border-bottom: none;
    }

    .site-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.55rem;
    }

    .site-avatar-info {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        min-width: 0;
        flex: 1;
    }

    .site-icon-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.08);
        color: #6366F1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.98rem;
        flex-shrink: 0;
    }

    .site-card-name {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        line-height: 1.25;
    }

    .site-loc-tag {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .site-router-badge {
        font-size: 0.74rem;
        font-weight: 800;
        color: #4338CA;
        background: #EEF2FF;
        border: 1px solid #C7D2FE;
        padding: 0.2rem 0.6rem;
        border-radius: var(--radius-pill);
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .site-desc-text {
        font-size: 0.78rem;
        color: var(--text-secondary);
        margin: 0.35rem 0 0.65rem 0;
        line-height: 1.4;
    }

    .site-action-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 0.45rem;
        padding-top: 0.45rem;
    }

    @media (max-width: 768px) {
        .sites-wrapper {
            padding-bottom: 5rem !important;
        }
        .sites-header-sub {
            display: none !important;
        }
        .sites-header-title {
            font-size: 1.15rem !important;
        }
        .sites-kpi-grid {
            gap: 0.45rem !important;
            margin-bottom: 0.75rem !important;
        }
        .site-kpi-card {
            padding: 0.65rem 0.7rem !important;
            border-radius: 12px !important;
            gap: 0.45rem !important;
        }
        .site-kpi-icon {
            width: 30px !important;
            height: 30px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
        }
        .site-kpi-number {
            font-size: 1.05rem !important;
        }
        .site-kpi-label {
            font-size: 0.66rem !important;
        }
        .sites-toolbar {
            padding: 0.75rem 0.9rem !important;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem !important;
        }
        .sites-search-box {
            min-width: 100% !important;
        }
    }
</style>

<div class="sites-wrapper">

    {{-- Top Header --}}
    <div class="sites-top-header">
        <div class="sites-title-wrap">
            <div class="sites-header-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
                <h1 class="sites-header-title">Maeneo (Sites)</h1>
                <p class="sites-header-sub">Minara na vituo vya Wi-Fi</p>
            </div>
        </div>
        <button onclick="document.getElementById('addSiteModal').style.display='flex'" class="btn-create-site">
            <i class="fas fa-plus"></i>
            <span>Site Mpya</span>
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.65rem; border-radius:12px; margin-bottom:0.85rem; font-size:0.85rem; font-weight:700;">
        <i class="fas fa-check-circle" style="color:#059669; font-size:1rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- KPI Overview Grid --}}
    <div class="sites-kpi-grid">
        <div class="site-kpi-card">
            <div class="site-kpi-icon indigo">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div>
                <div class="site-kpi-number">{{ count($sites) }}</div>
                <div class="site-kpi-label">Maeneo Yote</div>
            </div>
        </div>

        <div class="site-kpi-card">
            <div class="site-kpi-icon orange">
                <i class="fas fa-network-wired"></i>
            </div>
            <div>
                <div class="site-kpi-number">{{ $sites->sum('routers_count') }}</div>
                <div class="site-kpi-label">Routers Zilizopo</div>
            </div>
        </div>

        <div class="site-kpi-card">
            <div class="site-kpi-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="site-kpi-number">{{ $sites->where('routers_count', '>', 0)->count() }}</div>
                <div class="site-kpi-label">Maeneo Yenye Vifaa</div>
            </div>
        </div>
    </div>

    {{-- Sites Panel --}}
    <div class="sites-main-panel">
        <div class="sites-toolbar">
            <div class="sites-title-group">
                <h2 class="sites-panel-title">
                    <i class="fas fa-broadcast-tower"></i>
                    <span>Orodha ya Maeneo</span>
                </h2>
                <span class="sites-count-badge" id="siteCountBadge">{{ count($sites) }} maeneo</span>
            </div>
            <div class="sites-search-box">
                <i class="fas fa-search sites-search-icon"></i>
                <input type="text" id="siteSearchInput" class="sites-search-input" placeholder="Tafuta jina la eneo au mtaa..." onkeyup="filterSites()">
            </div>
        </div>

        {{-- Desktop Table View --}}
        <div class="table-responsive d-none d-md-block" style="margin:0;">
            <table class="table-banking">
                <thead>
                    <tr>
                        <th>Jina la Site</th>
                        <th>Eneo (Location)</th>
                        <th style="text-align:center;">Routers</th>
                        <th>Maelezo</th>
                        <th style="text-align:right;">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="sitesTableBody">
                    @forelse($sites as $site)
                    <tr class="site-row-item" data-search="{{ strtolower($site->name . ' ' . ($site->location ?? '') . ' ' . ($site->description ?? '')) }}">
                        <td style="font-weight:800; color:var(--text-main); font-size:0.92rem;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div class="site-icon-avatar" style="width:32px; height:32px; font-size:0.85rem;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <span>{{ $site->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span style="color:var(--text-muted); font-size:0.85rem; font-weight:600;">
                                <i class="fas fa-compass" style="font-size:0.75rem; margin-right:3px;"></i> {{ $site->location ?? '—' }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span class="site-router-badge">
                                <i class="fas fa-server" style="font-size:0.7rem;"></i> {{ $site->routers_count }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted); font-size:0.82rem; max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $site->description ?? '—' }}
                        </td>
                        <td style="text-align:right;">
                            <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" style="display:inline;" onsubmit="return confirm('Una uhakika unataka kufuta site hii? Routers zote za hapa zitafutwa pia.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action-icon btn-action-delete" style="width:32px; height:32px; border-radius:8px; border:1px solid #FEE2E2; background:#FFF5F5; color:#EF4444; cursor:pointer;" title="Futa Site">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:3.5rem 1rem;">
                            <div style="color:var(--text-muted);">
                                <i class="fas fa-map-marked-alt" style="font-size:2.6rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                                <div style="font-weight:800; font-size:1.05rem; color:var(--text-main); margin-bottom:0.3rem;">Hakuna Site Iliyoongezwa</div>
                                <div style="font-size:0.85rem;">Bofya "Site Mpya" kusajili kituo chako cha kwanza.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card Feed --}}
        <div class="d-md-none" id="sitesMobileFeed">
            @forelse($sites as $site)
            <div class="site-mobile-card site-card-item" data-search="{{ strtolower($site->name . ' ' . ($site->location ?? '') . ' ' . ($site->description ?? '')) }}">
                <div class="site-card-top">
                    <div class="site-avatar-info">
                        <div class="site-icon-avatar">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3 class="site-card-name">{{ $site->name }}</h3>
                            @if($site->location)
                            <div class="site-loc-tag">
                                <i class="fas fa-compass"></i>
                                <span>{{ $site->location }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <span class="site-router-badge">
                        <i class="fas fa-server"></i> {{ $site->routers_count }}
                    </span>
                </div>

                @if($site->description)
                    <p class="site-desc-text">{{ $site->description }}</p>
                @endif

                <div class="site-action-row">
                    <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" style="margin:0;" onsubmit="return confirm('Una uhakika unataka kufuta site hii? Routers zote za hapa zitafutwa pia.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="display:inline-flex; align-items:center; gap:0.35rem; padding:0.35rem 0.75rem; background:#FFF5F5; border:1px solid #FECACA; color:#EF4444; border-radius:8px; font-size:0.75rem; font-weight:700; cursor:pointer;">
                            <i class="fas fa-trash-alt"></i> Futa Site
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:3rem 1rem; color:var(--text-muted);">
                <i class="fas fa-map-marked-alt" style="font-size:2.2rem; opacity:0.3; margin-bottom:0.75rem; display:block;"></i>
                <div style="font-weight:800; color:var(--text-main);">Hakuna Site Bado</div>
            </div>
            @endforelse
        </div>

        <div id="noSiteResults" style="display:none; text-align:center; padding:2.5rem 1rem; color:var(--text-muted);">
            <i class="fas fa-search" style="font-size:1.75rem; opacity:0.3; margin-bottom:0.6rem; display:block;"></i>
            <div style="font-weight:700; color:var(--text-main); font-size:0.92rem;">Hakuna site inayolingana na utafutaji wako</div>
        </div>
    </div>
</div>

{{-- Add Site Modal --}}
<div id="addSiteModal" class="mobile-modal-backdrop" onclick="if(event.target===this) this.style.display='none'">
    <div class="mobile-modal-dialog">
        <div style="padding:1.2rem 1.4rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; background:#FAFAFC;">
            <h3 style="margin:0; font-size:1.05rem; font-weight:800; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-plus-circle" style="color:#6366F1;"></i> Ongeza Site Mpya
            </h3>
            <button type="button" onclick="document.getElementById('addSiteModal').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
        </div>
        
        <form action="{{ route('admin.sites.store') }}" method="POST" style="padding:1.4rem;">
            @csrf
            <div style="margin-bottom:1.1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:700; color:var(--text-main);">Jina la Site <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" required class="form-control" style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color);" placeholder="Mfano: Mbezi Mwisho">
            </div>
            
            <div style="margin-bottom:1.1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:700; color:var(--text-main);">Eneo (Location)</label>
                <input type="text" name="location" class="form-control" style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color);" placeholder="Mfano: Mtaa / Wilaya">
            </div>

            <div style="margin-bottom:1.4rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:700; color:var(--text-main);">Maelezo (Notes)</label>
                <textarea name="description" rows="2" class="form-control" style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); resize:vertical;" placeholder="Maelezo ya ziada kuhusu eneo hili"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
                <button type="button" onclick="document.getElementById('addSiteModal').style.display='none'" class="btn-banking btn-secondary" style="flex:1; max-width:120px; justify-content:center; border-radius:10px; font-weight:700;">Ghairi</button>
                <button type="submit" class="btn-create-site" style="flex:2; justify-content:center; border-radius:10px;">
                    <i class="fas fa-save"></i> Hifadhi Site
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterSites() {
        const query = document.getElementById('siteSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.site-row-item');
        const cards = document.querySelectorAll('.site-card-item');
        const noResults = document.getElementById('noSiteResults');
        const badge = document.getElementById('siteCountBadge');

        let visibleCount = 0;
        rows.forEach(r => {
            const data = r.getAttribute('data-search') || '';
            if (data.includes(query)) {
                r.style.display = '';
                visibleCount++;
            } else {
                r.style.display = 'none';
            }
        });
        cards.forEach(c => {
            const data = c.getAttribute('data-search') || '';
            if (data.includes(query)) {
                c.style.display = '';
            } else {
                c.style.display = 'none';
            }
        });
        if (badge) badge.innerText = visibleCount + ' maeneo';
        if (noResults) {
            if (visibleCount === 0 && (rows.length > 0 || cards.length > 0)) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
    }
</script>
@endsection

