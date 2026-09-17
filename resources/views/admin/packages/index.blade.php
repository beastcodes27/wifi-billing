@extends('layouts.app')

@section('title', 'Vifurushi')

@section('content')
<style>
    /* Packages Module Styling */
    .packages-wrapper {
        width: 100%;
        margin: 0 auto;
        padding-bottom: 3rem;
    }

    .packages-top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 0.75rem;
    }

    .packages-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .packages-header-icon {
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

    .packages-header-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.2;
    }

    .packages-header-sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0.15rem 0 0 0;
    }

    .btn-create-pkg {
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
        border: none;
        cursor: pointer;
        transition: all var(--transition);
        white-space: nowrap;
    }

    .btn-create-pkg:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(255, 51, 0, 0.38);
        color: #FFFFFF !important;
    }

    /* KPI Grid */
    .packages-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .pkg-kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: var(--shadow-xs);
    }

    .pkg-kpi-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .pkg-kpi-icon.orange { background: rgba(255, 51, 0, 0.1); color: #FF3300; }
    .pkg-kpi-icon.green  { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .pkg-kpi-icon.gray   { background: #F1F5F9; color: var(--text-muted); }

    .pkg-kpi-number {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    .pkg-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-top: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Main Container */
    .packages-main-panel {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-xs);
        overflow: hidden;
    }

    .packages-toolbar {
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid var(--border-color);
        background: #FAFAFC;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .packages-title-group {
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .packages-panel-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .packages-panel-title i {
        color: #FF3300;
    }

    .packages-count-badge {
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--text-secondary);
        background: #F1F5F9;
        border: 1px solid var(--border-color);
        padding: 0.15rem 0.55rem;
        border-radius: var(--radius-pill);
    }

    .packages-search-box {
        position: relative;
        min-width: 220px;
    }

    .packages-search-input {
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

    .packages-search-input:focus {
        border-color: #FF3300;
        box-shadow: 0 0 0 3px rgba(255, 51, 0, 0.12);
    }

    .packages-search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.8rem;
        color: var(--text-muted);
        pointer-events: none;
    }

    /* Mobile Package Cards */
    .package-mobile-card {
        padding: 1rem 1.05rem;
        border-bottom: 1px solid var(--border-color);
        background: #FFFFFF;
    }

    .package-mobile-card:last-child {
        border-bottom: none;
    }

    .pkg-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.55rem;
    }

    .pkg-title-wrap {
        min-width: 0;
        flex: 1;
    }

    .pkg-name {
        font-size: 0.96rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        line-height: 1.25;
    }

    .pkg-price-badge {
        font-size: 1.05rem;
        font-weight: 800;
        color: #FF3300;
        white-space: nowrap;
        text-align: right;
    }

    .pkg-tags-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.35rem;
    }

    .pkg-tag {
        font-size: 0.74rem;
        font-weight: 700;
        padding: 0.15rem 0.55rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .pkg-tag.duration {
        background: #F0FDF4;
        color: #166534;
        border: 1px solid #BBF7D0;
    }

    .pkg-tag.profile {
        background: #F8FAFC;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        font-family: 'Fira Code', monospace;
    }

    .pkg-tag.speed {
        background: #EFF6FF;
        color: #1E40AF;
        border: 1px solid #BFDBFE;
    }

    .pkg-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.55rem;
        margin-top: 0.55rem;
        border-top: 1px solid #F1F5F9;
    }

    @media (max-width: 768px) {
        .packages-wrapper {
            padding-bottom: 5rem !important;
        }
        .packages-header-sub {
            display: none !important;
        }
        .packages-header-title {
            font-size: 1.15rem !important;
        }
        .packages-kpi-grid {
            gap: 0.45rem !important;
            margin-bottom: 0.75rem !important;
        }
        .pkg-kpi-card {
            padding: 0.65rem 0.7rem !important;
            border-radius: 12px !important;
            gap: 0.45rem !important;
        }
        .pkg-kpi-icon {
            width: 30px !important;
            height: 30px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
        }
        .pkg-kpi-number {
            font-size: 1.05rem !important;
        }
        .pkg-kpi-label {
            font-size: 0.66rem !important;
        }
        .packages-toolbar {
            padding: 0.75rem 0.9rem !important;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem !important;
        }
        .packages-search-box {
            min-width: 100% !important;
        }
    }
</style>

<div class="packages-wrapper">

    {{-- Top Header --}}
    <div class="packages-top-header">
        <div class="packages-title-wrap">
            <div class="packages-header-icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div>
                <h1 class="packages-header-title">Vifurushi</h1>
                <p class="packages-header-sub">Viwango vya bei na muda wa Wi-Fi</p>
            </div>
        </div>
        <a href="{{ route('admin.packages.create') }}" class="btn-create-pkg">
            <i class="fas fa-plus"></i>
            <span>Kifurushi Kipya</span>
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
    <div class="packages-kpi-grid">
        <div class="pkg-kpi-card">
            <div class="pkg-kpi-icon orange">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="pkg-kpi-number">{{ $stats['total'] }}</div>
                <div class="pkg-kpi-label">Vifurushi Vyote</div>
            </div>
        </div>

        <div class="pkg-kpi-card">
            <div class="pkg-kpi-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="pkg-kpi-number">{{ $stats['active'] }}</div>
                <div class="pkg-kpi-label">Hewani (Active)</div>
            </div>
        </div>

        <div class="pkg-kpi-card">
            <div class="pkg-kpi-icon gray">
                <i class="fas fa-eye-slash"></i>
            </div>
            <div>
                <div class="pkg-kpi-number">{{ $stats['inactive'] }}</div>
                <div class="pkg-kpi-label">Zilizositishwa</div>
            </div>
        </div>
    </div>

    {{-- Main Packages Panel --}}
    <div class="packages-main-panel">
        <div class="packages-toolbar">
            <div class="packages-title-group">
                <h2 class="packages-panel-title">
                    <i class="fas fa-list-ul"></i>
                    <span>Orodha ya Vifurushi</span>
                </h2>
                <span class="packages-count-badge" id="pkgCountBadge">{{ $packages->total() }} vilivyopo</span>
            </div>
            <div class="packages-search-box">
                <i class="fas fa-search packages-search-icon"></i>
                <input type="text" id="pkgSearchInput" class="packages-search-input" placeholder="Tafuta jina, muda au bei..." onkeyup="filterPackages()">
            </div>
        </div>

        {{-- Desktop Table View --}}
        <div class="table-responsive d-none d-md-block" style="margin:0;">
            <table class="table-banking">
                <thead>
                    <tr>
                        <th style="width:50px; text-align:center;">#</th>
                        <th>Jina la Kifurushi</th>
                        <th>Muda</th>
                        <th>Bei</th>
                        <th>Profile & Speed</th>
                        <th style="text-align:center;">Hali (Status)</th>
                        <th style="text-align:right;">Vitendo</th>
                    </tr>
                </thead>
                <tbody id="packagesTableBody">
                    @forelse($packages as $package)
                    <tr class="pkg-row-item" data-search="{{ strtolower($package->name . ' ' . $package->human_duration . ' ' . $package->price . ' ' . ($package->mikrotik_profile ?? '')) }}">
                        <td style="text-align:center;">
                            <span style="font-weight:800; font-size:0.78rem; color:var(--text-muted);">
                                {{ $package->display_order }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:800; color:var(--text-main); font-size:0.92rem;">{{ $package->name }}</div>
                            @if($package->description)
                                <div style="font-size:0.76rem; color:var(--text-muted); max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:2px;">
                                    {{ $package->description }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="pkg-tag duration">
                                <i class="fas fa-clock"></i> {{ $package->human_duration }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:800; color:#FF3300; font-size:0.95rem;">
                                TSh {{ number_format($package->price) }}
                            </span>
                        </td>
                        <td>
                            <span class="pkg-tag profile">
                                {{ $package->mikrotik_profile ?? 'default' }}
                            </span>
                            @if($package->download_speed || $package->upload_speed)
                                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.3rem; display:flex; gap:0.5rem;">
                                    <span><i class="fas fa-arrow-down" style="color:#10B981;"></i> {{ $package->download_speed ?? '-' }}</span>
                                    <span><i class="fas fa-arrow-up" style="color:#F59E0B;"></i> {{ $package->upload_speed ?? '-' }}</span>
                                </div>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <form action="{{ route('admin.packages.toggle-status', $package) }}" method="POST">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; cursor:pointer;" title="Badili hali">
                                    @if($package->is_active)
                                        <span class="status-pill status-pill-active">
                                            <span class="pulse-dot-green"></span> Active
                                        </span>
                                    @else
                                        <span class="status-pill status-pill-inactive">
                                            <span class="dot-red"></span> Inactive
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:0.4rem; align-items:center;">
                                <a href="{{ route('admin.packages.edit', $package) }}" class="btn-action-icon btn-action-edit" title="Hariri">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Una uhakika unataka kufuta kifurushi hiki?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon btn-action-delete" title="Futa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:3.5rem 1rem;">
                            <div style="color:var(--text-muted);">
                                <i class="fas fa-boxes-stacked" style="font-size:2.6rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                                <div style="font-weight:800; font-size:1.05rem; color:var(--text-main); margin-bottom:0.3rem;">Hakuna Vifurushi Bado</div>
                                <div style="font-size:0.85rem;">Anza kwa kuweka vifurushi vya muda na bei kwa ajili ya wateja wako.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card Feed --}}
        <div class="d-md-none" id="packagesMobileFeed">
            @forelse($packages as $package)
            <div class="package-mobile-card pkg-card-item" data-search="{{ strtolower($package->name . ' ' . $package->human_duration . ' ' . $package->price . ' ' . ($package->mikrotik_profile ?? '')) }}">
                <div class="pkg-card-top">
                    <div class="pkg-title-wrap">
                        <h3 class="pkg-name">{{ $package->name }}</h3>
                        <div class="pkg-tags-row">
                            <span class="pkg-tag duration">
                                <i class="fas fa-clock"></i> {{ $package->human_duration }}
                            </span>
                            <span class="pkg-tag profile">
                                {{ $package->mikrotik_profile ?? 'default' }}
                            </span>
                        </div>
                    </div>
                    <div class="pkg-price-badge">
                        TSh {{ number_format($package->price) }}
                    </div>
                </div>

                @if($package->description)
                    <p style="font-size:0.78rem; color:var(--text-muted); margin:0.3rem 0 0.5rem 0;">{{ $package->description }}</p>
                @endif

                <div class="pkg-action-bar">
                    <form action="{{ route('admin.packages.toggle-status', $package) }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" style="background:none; border:none; padding:0; cursor:pointer;">
                            @if($package->is_active)
                                <span class="status-pill status-pill-active" style="font-size:0.72rem;">
                                    <span class="pulse-dot-green"></span> Active
                                </span>
                            @else
                                <span class="status-pill status-pill-inactive" style="font-size:0.72rem;">
                                    <span class="dot-red"></span> Inactive
                                </span>
                            @endif
                        </button>
                    </form>

                    <div style="display:flex; gap:0.4rem; align-items:center;">
                        <a href="{{ route('admin.packages.edit', $package) }}" class="btn-action-icon btn-action-edit" title="Hariri">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" style="margin:0;" onsubmit="return confirm('Una uhakika unataka kufuta kifurushi hiki?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action-icon btn-action-delete" title="Futa">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:3rem 1rem; color:var(--text-muted);">
                <i class="fas fa-boxes-stacked" style="font-size:2.2rem; opacity:0.3; margin-bottom:0.75rem; display:block;"></i>
                <div style="font-weight:800; color:var(--text-main);">Hakuna Vifurushi Bado</div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($packages->hasPages())
        <div style="padding:0.85rem 1.15rem; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem; background:#FAFAFC;">
            <div style="color:var(--text-muted); font-size:0.82rem;">
                Inaonyesha <strong>{{ $packages->firstItem() }}</strong>–<strong>{{ $packages->lastItem() }}</strong> kati ya <strong>{{ $packages->total() }}</strong>
            </div>
            <div>
                {{ $packages->links() }}
            </div>
        </div>
        @endif

        <div id="noPkgResults" style="display:none; text-align:center; padding:2.5rem 1rem; color:var(--text-muted);">
            <i class="fas fa-search" style="font-size:1.75rem; opacity:0.3; margin-bottom:0.6rem; display:block;"></i>
            <div style="font-weight:700; color:var(--text-main); font-size:0.92rem;">Hakuna kifurushi kinacholingana na utafutaji wako</div>
        </div>
    </div>
</div>

<script>
    function filterPackages() {
        const query = document.getElementById('pkgSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.pkg-row-item');
        const cards = document.querySelectorAll('.pkg-card-item');
        const noResults = document.getElementById('noPkgResults');
        const badge = document.getElementById('pkgCountBadge');

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
        if (badge) badge.innerText = visibleCount + ' vilivyopo';
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

