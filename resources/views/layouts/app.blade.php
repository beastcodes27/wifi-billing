<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover, interactive-widget=resizes-visual">
    <meta name="theme-color" content="#07162A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MedBando">
    <meta name="application-name" content="MedBando">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $tenantName = 'MedBando';
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        if ($tenant && !empty($tenant->business_name)) {
            $tenantName = strtoupper($tenant->business_name);
        }
        $brandInitials = substr($tenantName, 0, 2);
    @endphp
    <title>{{ $tenantName }} — MedBando Wi-Fi</title>

    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Chart.js for Smooth Performance Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- WiFi Billing Modern Design Utilities & Tailwind Engine -->
    <link rel="stylesheet" href="{{ asset('medbiz/app-B-8yuPLP.css') }}">

    <!-- Banking SaaS Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">

    <style>
        /* Modern Ultra-Compact Toast Notifications (Mobile & Desktop) */
        #toast-container {
            position: fixed;
            top: 12px;
            right: 16px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            pointer-events: none;
            max-width: 320px;
        }
        .toast-msg {
            pointer-events: auto;
            background: rgba(15, 23, 42, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: #FFFFFF;
            padding: 5px 10px 5px 6px;
            border-radius: 40px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.28);
            border: 1px solid rgba(255, 255, 255, 0.12);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            opacity: 0;
            transform: translateY(-10px) scale(0.96);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 100%;
            width: auto;
            font-size: 0.72rem;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
        }
        .toast-msg.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .toast-msg.hide {
            opacity: 0;
            transform: translateY(-8px) scale(0.94);
        }
        .toast-icon-wrap {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.62rem;
            flex-shrink: 0;
            color: #FFFFFF;
        }
        .toast-icon-wrap.success, .toast-icon-wrap.payment { background: #10B981; }
        .toast-icon-wrap.alert { background: #EF4444; }
        .toast-icon-wrap.voucher { background: #D97706; }
        .toast-icon-wrap.router { background: #6366F1; }
        .toast-body {
            flex: 1;
            min-width: 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
            padding-right: 2px;
            font-size: 0.72rem;
        }
        .toast-close-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.95rem;
            line-height: 1;
            padding: 0 2px;
            cursor: pointer;
            margin-left: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .toast-close-btn:hover {
            color: #FFFFFF;
        }

        @media (max-width: 768px) {
            #toast-container {
                top: 8px;
                right: 8px;
                left: auto;
                align-items: flex-end;
                width: auto;
                max-width: calc(100% - 16px);
            }
            .toast-msg {
                font-size: 0.7rem;
                padding: 4px 8px 4px 5px;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
            }
            .toast-body {
                max-width: 180px;
                font-size: 0.7rem;
            }
        }

        /* Notification Bell in Header */
        .bell-icon {
            position: relative;
            cursor: pointer;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            box-shadow: var(--shadow-xs);
            transition: all var(--transition);
        }
        .bell-icon:hover {
            color: var(--text-main);
            border-color: #CBD5E1;
            box-shadow: var(--shadow-sm);
        }
        .bell-icon.ringing {
            animation: ring 0.5s ease-in-out infinite alternate;
            color: var(--blue-primary);
        }
        @keyframes ring {
            0% { transform: rotate(-12deg); }
            100% { transform: rotate(12deg); }
        }
        .bell-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: white;
            border-radius: var(--radius-pill);
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            padding: 0 4px;
            border: 2px solid #FFFFFF;
        }
        .notif-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 290px;
            max-width: 90vw;
            background: #FFFFFF;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border-color);
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            text-align: left;
        }
        .notif-dropdown.show { display: flex; }
        .notif-header {
            padding: 0.6rem 0.85rem;
            border-bottom: 1px solid var(--border-light);
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--text-main);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--surface-soft);
        }
        .notif-body {
            max-height: 260px;
            overflow-y: auto;
        }
        .notif-item {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            gap: 8px;
            align-items: flex-start;
            transition: background var(--transition);
        }
        .notif-item:hover { background: var(--surface-soft); }
        .notif-item:last-child { border-bottom: none; }
        .notif-icon {
            width: 26px;
            height: 26px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.75rem;
        }
        .notif-icon.payment { background: var(--success-light); color: var(--success); }
        .notif-icon.alert { background: var(--danger-light); color: var(--danger); }
        .notif-icon.success { background: var(--blue-light); color: var(--blue-primary); }
        .notif-text {
            font-size: 0.74rem;
            color: var(--text-main);
            line-height: 1.25;
            font-weight: 500;
        }
        .notif-time {
            font-size: 0.65rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Desktop Collapse Button */
        .sidebar-toggle-btn {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: var(--shadow-xs);
        }
        .sidebar-toggle-btn:hover {
            color: var(--text-main);
            border-color: #CBD5E1;
            box-shadow: var(--shadow-sm);
        }

        @media (max-width: 768px) {
            .sidebar-toggle-btn { display: none !important; }
            .desktop-only-header { display: none !important; }
            .notif-dropdown {
                width: calc(100vw - 24px);
                max-width: 320px;
                right: -60px;
            }
        }
    </style>
</head>
<body>
    @if(session()->has('impersonated_tenant_id'))
        @php
            $impersonatedTenant = \App\Models\Tenant::find(session('impersonated_tenant_id'));
        @endphp
        @if($impersonatedTenant)
        <div style="background: #D97706; color: white; padding: 0.65rem 1.25rem; text-align: center; font-weight: 700; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; gap: 1rem; z-index: 100000; position: fixed; bottom: 0; left: 0; right: 0; width: 100%; box-shadow: 0 -4px 15px rgba(0,0,0,0.15);">
            <span><i class="fas fa-user-secret" style="margin-right: 0.5rem;"></i> You are impersonating <strong>{{ $impersonatedTenant->business_name }}</strong></span>
            <form action="{{ route('superadmin.tenants.leave_impersonation') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="background: white; color: #D97706; border: none; padding: 0.35rem 0.85rem; border-radius: 6px; font-weight: 800; font-size: 0.78rem; cursor: pointer; transition: all 0.2s;">
                    Return to Super Admin
                </button>
            </form>
        </div>
        @endif
    @endif

    <!-- Desktop Sidebar -->
    <aside class="sidebar">
        <!-- Brand Header -->
        <div class="sidebar-brand-wrapper">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <div class="brand-icon-box" style="padding: 4px; background: #FFFFFF; border: 1px solid rgba(0,0,0,0.08); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="MedBando Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="brand-details">
                    <span class="brand-text">{{ $tenantName }}</span>
                    <span class="brand-subtext">
                        <span class="live-pulse-dot"></span> MedBando Wi-Fi
                    </span>
                </div>
            </a>
        </div>

        <!-- Scrollable Navigation Menu -->
        <div class="sidebar-nav-scroll">
            <div class="sidebar-section-title">MUHTASARI</div>
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i> <span>Taarifa</span>
                        @php
                            $globalUnreadNotifs = \App\Models\AppNotification::where('is_read', false)->count();
                        @endphp
                        @if($globalUnreadNotifs > 0)
                            <span class="nav-pulse-badge" style="background: #EF4444; color: white;">{{ $globalUnreadNotifs > 99 ? '99+' : $globalUnreadNotifs }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">MTANDAO &amp; VIFAA</div>
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('admin.routers.index') }}" class="nav-link {{ request()->routeIs('admin.routers.*') ? 'active' : '' }}">
                        <i class="fas fa-network-wired"></i> <span>Routers</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.live_sessions') }}" class="nav-link {{ request()->routeIs('admin.live_sessions*') ? 'active' : '' }}">
                        <i class="fas fa-satellite-dish"></i> <span>Live Sessions</span>
                        <span class="nav-pulse-badge">LIVE</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.sites.index') }}" class="nav-link {{ request()->routeIs('admin.sites.*') ? 'active' : '' }}">
                        <i class="fas fa-map-marker-alt"></i> <span>Sites</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">WATEJA</div>
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> <span>Wateja</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.subscriptions.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                        <i class="fas fa-id-card"></i> <span>Vifurushi Wateja</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">MAUZO &amp; FEDHA</div>
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes-stacked"></i> <span>Vifurushi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.vouchers.index') }}" class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                        <i class="fas fa-ticket-alt"></i> <span>Vocha</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.transactions') }}" class="nav-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i> <span>Miamala</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.expenses.index') }}" class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> <span>Matumizi</span>
                    </a>
                </li>

                @php
                    $currentTenant = app(\App\Services\TenantContext::class)->getTenant();
                    $hasAutoPayment = $currentTenant && $currentTenant->currentSubscription && $currentTenant->currentSubscription->plan && $currentTenant->currentSubscription->plan->has_auto_payment;
                @endphp
                @if($hasAutoPayment)
                <li>
                    <a href="{{ route('admin.settings.payment') }}" class="nav-link {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
                        <i class="fas fa-wallet"></i> <span>Njia za Malipo</span>
                    </a>
                </li>
                @endif
            </ul>

            <div class="sidebar-section-title">TAKWIMU &amp; RIPOTI</div>
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> <span>Ripoti</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title">UTAWALA</div>
            <ul class="nav-menu">
                @if(auth()->user() && auth()->user()->is_super_admin)
                <li>
                    <a href="{{ route('superadmin.dashboard') }}" class="nav-link" style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.12), rgba(99, 102, 241, 0.12)); color: #7C3AED; font-weight: 700; border: 1px solid rgba(124, 58, 237, 0.25); border-radius: var(--radius-md); margin-bottom: 6px;">
                        <i class="fas fa-crown" style="color: #7C3AED;"></i> <span>Admin Mkuu (SaaS)</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <i class="fas fa-user-lock"></i> <span>Miunganisho</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h"></i> <span>Mipangilio</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i> <span>Profaili</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar Pinned Footer (Admin User & Logout) -->
        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <a href="{{ route('admin.profile') }}" class="sidebar-user-avatar" title="Profaili ya Msimamizi">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </a>
                <div class="sidebar-user-meta">
                    <a href="{{ route('admin.profile') }}" class="sidebar-user-name">{{ auth()->user()->name ?? 'Administrator' }}</a>
                    <span class="sidebar-user-role">Msimamizi wa Mfumo</span>
                </div>
                <div class="sidebar-footer-actions">
                    <a href="{{ route('admin.settings') }}" class="sidebar-footer-btn" title="Mipangilio">
                        <i class="fas fa-cog"></i>
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0; display: inline;">
                        @csrf
                        <button type="submit" class="sidebar-footer-btn logout-btn" title="Toka Kwenye Mfumo">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Cockpit -->
    <main class="main-content">
        <!-- Top Modern Banking & SaaS Header -->
        <header class="header">
            <div class="header-left">
                <!-- Desktop Sidebar Toggle -->
                <button class="sidebar-toggle-btn" id="desktopSidebarToggle" title="Badili Sidebar">
                    <i class="fas fa-bars-staggered"></i>
                </button>

                <!-- Page Title -->
                <div class="header-title-wrap">
                    <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                </div>

                <!-- Live System Indicator (Desktop) -->
                <div class="desktop-only-header">
                    <span class="system-status-pill">
                        <span class="live-pulse-dot"></span> System Online
                    </span>
                </div>
            </div>

            <div class="header-right">
                <!-- Desktop Quick Action: + Vocha Mpya -->
                <a href="{{ route('admin.vouchers.index') }}" class="btn-quick-voucher desktop-only-header" title="Tengeneza Vocha">
                    <i class="fas fa-plus"></i>
                    <span>Vocha Mpya</span>
                </a>

                <!-- Mobile Search Toggle Button (Shows on mobile only) -->
                <button type="button" class="mobile-search-toggle-btn d-md-none" id="mobileSearchToggleBtn" title="Tafuta">
                    <i class="fas fa-search"></i>
                </button>

                <!-- Global Customer Search -->
                <div class="search-container" id="globalSearchWrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="globalSearch" class="search-input" placeholder="Tafuta mteja, namba ya simu..." autocomplete="off">
                    <span class="search-kbd-badge desktop-only-header">Ctrl K</span>
                    <div id="globalSearchResults" style="position: absolute; top: calc(100% + 8px); left: 0; right: 0; background: #FFFFFF; border-radius: var(--radius-md); box-shadow: var(--shadow-dropdown); border: 1px solid var(--border-color); display: none; z-index: 1000; overflow: hidden; max-height: 380px; overflow-y: auto;"></div>
                </div>

                <!-- Notification Bell -->
                <div class="bell-icon" id="notificationBell" title="Taarifa">
                    <i class="fas fa-bell"></i>
                    <div class="bell-badge" id="notificationBadge">0</div>

                    <!-- Dropdown -->
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span>Taarifa za Mfumo</span>
                            <button id="clearNotifsBtn" style="background: none; border: none; color: var(--blue-primary); cursor: pointer; font-size: 0.78rem; font-weight: 700;">Futa Zote</button>
                        </div>
                        <div class="notif-body" id="notifList">
                            <div id="emptyNotifState" style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                                <i class="fas fa-bell-slash mb-2" style="font-size: 1.75rem; opacity: 0.35;"></i><br>
                                Hakuna taarifa mpya
                            </div>
                        </div>
                        <div class="notif-footer" style="padding: 0.75rem 1rem; text-align: center; background: var(--surface-soft); border-top: 1px solid var(--border-light);">
                            <a href="{{ route('admin.notifications.index', ['mark_read' => 1]) }}" style="font-size: 0.82rem; font-weight: 700; color: #FF3300; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <span>Fungua Kituo cha Taarifa</span>
                                <i class="fas fa-arrow-right" style="font-size: 0.72rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Pill -->
                <a href="{{ route('admin.profile') }}" class="user-profile-pill" title="Profaili Yangu">
                    <div class="user-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                    <div class="user-meta desktop-only-header">
                        <span class="user-name">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <span class="user-role">Msimamizi</span>
                    </div>
                </a>
            </div>
        </header>

        <!-- Main Page Content -->
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Bar (Dock) -->
    <nav class="mobile-bottom-nav">
        <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>Dash</span>
        </a>
        <a href="{{ route('admin.customers.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Wateja</span>
        </a>
        @php
            $isDrawerActive = request()->routeIs('admin.routers.*') ||
                              request()->routeIs('admin.packages.*') ||
                              request()->routeIs('admin.transactions*') ||
                              request()->routeIs('admin.expenses.*') ||
                              request()->routeIs('admin.reports.*') ||
                              request()->routeIs('admin.settings*') ||
                              request()->routeIs('admin.profile*') ||
                              request()->routeIs('admin.users*') ||
                              request()->routeIs('admin.sites.*') ||
                              request()->routeIs('admin.notifications.*') ||
                              request()->routeIs('admin.subscriptions.*');
        @endphp
        <button type="button" class="bottom-nav-item center-launcher {{ $isDrawerActive ? 'active' : '' }}" id="openMobileDrawerBtn" aria-label="Menu Kuu na Huduma Zote">
            <div class="center-fab {{ $isDrawerActive ? 'active-fab' : '' }}">
                <i class="fas fa-th-large"></i>
            </div>
            <span>Menu</span>
        </button>
        <a href="{{ route('admin.live_sessions') }}" class="bottom-nav-item {{ request()->routeIs('admin.live_sessions*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrap">
                <i class="fas fa-satellite-dish"></i>
                <span class="bottom-live-dot"></span>
            </div>
            <span>Live</span>
        </a>
        <a href="{{ route('admin.vouchers.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
            <i class="fas fa-ticket-alt"></i>
            <span>Vocha</span>
        </a>
    </nav>

    <!-- Mobile Slide-Up Drawer ("Menu Kuu") -->
    <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="drawer-handle-bar"><div class="drawer-handle"></div></div>
        
        <!-- Drawer Header with Tenant Info & Close Button -->
        <div class="drawer-header-bar">
            <div class="drawer-header-brand">
                <div class="drawer-brand-icon" style="padding: 4px; background: #FFFFFF; border: 1px solid rgba(255,255,255,0.2); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.25);">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="MedBando Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div>
                    <div class="drawer-brand-title">{{ $tenantName }}</div>
                    <div class="drawer-brand-sub"><span class="live-pulse-dot"></span> MedBando Wi-Fi</div>
                </div>
            </div>
            <button id="closeDrawerBtn" class="drawer-close-btn" aria-label="Funga Menu">&times;</button>
        </div>

        <!-- PWA Quick Install Banner inside Drawer -->
        <div id="pwaDrawerCard" style="margin: 0.65rem 0.65rem 0.35rem 0.65rem; background: linear-gradient(135deg, rgba(255, 51, 0, 0.12), rgba(37, 99, 235, 0.12)); border: 1px solid rgba(255, 51, 0, 0.25); border-radius: 14px; padding: 0.75rem 0.85rem; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="background: #FFFFFF; padding: 3px; border-radius: 8px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="MedBando" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div>
                    <div style="font-size: 0.82rem; font-weight: 800; color: #FFFFFF;">MedBando App</div>
                    <div style="font-size: 0.68rem; color: #94A3B8;">Weka app kwenye simu yako</div>
                </div>
            </div>
            <button id="pwaInstallBtn" type="button" style="background: linear-gradient(135deg, #FF3300, #EA580C); color: white; border: none; padding: 0.45rem 0.85rem; border-radius: 50px; font-size: 0.74rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 5px; box-shadow: 0 3px 10px rgba(255, 51, 0, 0.35); white-space: nowrap;">
                <i class="fas fa-download"></i> Sakinisha
            </button>
        </div>


        <!-- Drawer App Grid -->
        <div class="drawer-grid">
            <div class="drawer-category-title">MTANDAO &amp; VIFAA</div>
            <a href="{{ route('admin.routers.index') }}" class="drawer-btn {{ request()->routeIs('admin.routers.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(99, 102, 241, 0.12); color: #6366F1;">
                    <i class="fas fa-network-wired"></i>
                </div>
                <span>Routers</span>
            </a>
            <a href="{{ route('admin.live_sessions') }}" class="drawer-btn {{ request()->routeIs('admin.live_sessions*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #10B981; position: relative;">
                    <i class="fas fa-satellite-dish"></i>
                    <span class="drawer-mini-pulse"></span>
                </div>
                <span>Live Sessions</span>
            </a>
            <a href="{{ route('admin.sites.index') }}" class="drawer-btn {{ request()->routeIs('admin.sites.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(236, 72, 153, 0.12); color: #EC4899;">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <span>Sites</span>
            </a>

            <div class="drawer-category-title">MAUZO &amp; FEDHA</div>
            <a href="{{ route('admin.packages.index') }}" class="drawer-btn {{ request()->routeIs('admin.packages.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(245, 158, 11, 0.12); color: #F59E0B;">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <span>Vifurushi</span>
            </a>
            <a href="{{ route('admin.vouchers.index') }}" class="drawer-btn {{ request()->routeIs('admin.vouchers.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(255, 51, 0, 0.12); color: #FF3300;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <span>Vocha</span>
            </a>
            <a href="{{ route('admin.transactions') }}" class="drawer-btn {{ request()->routeIs('admin.transactions*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(6, 182, 212, 0.12); color: #06B6D4;">
                    <i class="fas fa-receipt"></i>
                </div>
                <span>Miamala</span>
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="drawer-btn {{ request()->routeIs('admin.expenses.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(244, 63, 94, 0.12); color: #F43F5E;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <span>Matumizi</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="drawer-btn {{ request()->routeIs('admin.reports.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span>Ripoti</span>
            </a>
            <a href="{{ route('admin.customers.index') }}" class="drawer-btn {{ request()->routeIs('admin.customers.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #3B82F6;">
                    <i class="fas fa-users"></i>
                </div>
                <span>Wateja</span>
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" class="drawer-btn {{ request()->routeIs('admin.subscriptions.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(99, 102, 241, 0.12); color: #6366F1;">
                    <i class="fas fa-id-card"></i>
                </div>
                <span>Vifurushi Wateja</span>
            </a>

            <div class="drawer-category-title">UTAWALA &amp; MIPANGILIO</div>
            @if(auth()->user() && auth()->user()->is_super_admin)
            <a href="{{ route('superadmin.dashboard') }}" class="drawer-btn" style="border: 1.5px solid rgba(124, 58, 237, 0.35); background: rgba(124, 58, 237, 0.08);">
                <div class="drawer-icon-box" style="background: rgba(124, 58, 237, 0.2); color: #7C3AED;">
                    <i class="fas fa-crown"></i>
                </div>
                <span style="color: #7C3AED; font-weight: 800;">Admin Mkuu</span>
            </a>
            @endif
            @php
                $currentTenant = app(\App\Services\TenantContext::class)->getTenant();
                $hasAutoPayment = $currentTenant && $currentTenant->currentSubscription && $currentTenant->currentSubscription->plan && $currentTenant->currentSubscription->plan->has_auto_payment;
            @endphp
            @if($hasAutoPayment)
            <a href="{{ route('admin.settings.payment') }}" class="drawer-btn {{ request()->routeIs('admin.settings.payment*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">
                    <i class="fas fa-wallet"></i>
                </div>
                <span>Njia za Malipo</span>
            </a>
            @endif
            <a href="{{ route('admin.notifications.index') }}" class="drawer-btn {{ request()->routeIs('admin.notifications.*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(239, 68, 68, 0.12); color: #EF4444; position: relative;">
                    <i class="fas fa-bell"></i>
                    @if(isset($globalUnreadNotifs) && $globalUnreadNotifs > 0)
                        <span class="drawer-mini-pulse" style="background: #EF4444;"></span>
                    @endif
                </div>
                <span>Taarifa</span>
            </a>
            <a href="{{ route('admin.users') }}" class="drawer-btn {{ request()->routeIs('admin.users*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(100, 116, 139, 0.12); color: #475569;">
                    <i class="fas fa-user-lock"></i>
                </div>
                <span>Miunganisho</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="drawer-btn {{ request()->routeIs('admin.settings*') && !request()->routeIs('admin.settings.payment*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(100, 116, 139, 0.12); color: #475569;">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <span>Mipangilio</span>
            </a>
            <a href="{{ route('admin.profile') }}" class="drawer-btn {{ request()->routeIs('admin.profile*') ? 'active-drawer-btn' : '' }}">
                <div class="drawer-icon-box" style="background: rgba(255, 51, 0, 0.12); color: #FF3300;">
                    <i class="fas fa-user-circle"></i>
                </div>
                <span>Profaili</span>
            </a>

            <!-- Drawer Logout Action -->
            <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0; grid-column: span 3;">
                @csrf
                <button type="submit" class="drawer-logout-action">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Toka Kwenye Mfumo</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Search Toggle
            const mobileSearchBtn = document.getElementById('mobileSearchToggleBtn');
            const searchWrap = document.getElementById('globalSearchWrap');
            if (mobileSearchBtn && searchWrap) {
                mobileSearchBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    searchWrap.classList.toggle('mobile-active');
                    if (searchWrap.classList.contains('mobile-active')) {
                        const input = document.getElementById('globalSearch');
                        if (input) input.focus();
                    }
                });
                document.addEventListener('click', function(e) {
                    if (searchWrap && !searchWrap.contains(e.target) && !mobileSearchBtn.contains(e.target)) {
                        searchWrap.classList.remove('mobile-active');
                    }
                });

                // Keyboard Shortcut Ctrl+K / Cmd+K to focus search
                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                        e.preventDefault();
                        const searchInput = document.getElementById('globalSearch');
                        if (searchInput) {
                            searchInput.focus();
                            searchInput.select();
                        }
                    }
                });
            }

            // Desktop Sidebar Collapse Toggle
            const desktopToggle = document.getElementById('desktopSidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            if (desktopToggle && sidebar && mainContent) {
                // Read saved state
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }

                desktopToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }

            // Mobile Drawer
            const openDrawerBtn = document.getElementById('openMobileDrawerBtn');
            const closeDrawerBtn = document.getElementById('closeDrawerBtn');
            const drawer = document.getElementById('mobileDrawer');
            const overlay = document.getElementById('mobileDrawerOverlay');

            function toggleDrawer(open) {
                if (open) {
                    overlay.classList.add('active');
                    drawer.classList.add('open');
                } else {
                    overlay.classList.remove('active');
                    drawer.classList.remove('open');
                }
            }

            if (openDrawerBtn) openDrawerBtn.addEventListener('click', () => toggleDrawer(true));
            if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', () => toggleDrawer(false));
            if (overlay) overlay.addEventListener('click', () => toggleDrawer(false));

            // ==========================================
            // Real-time Notifications System
            // ==========================================
            function playNotificationSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1760, ctx.currentTime + 0.1);
                    gain.gain.setValueAtTime(0, ctx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.4, ctx.currentTime + 0.05);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.4);
                } catch (e) {
                    console.log('AudioContext not available');
                }
            }

            function showToast(message, type, link, id) {
                const container = document.getElementById('toast-container');
                if (!container) return;

                // On mobile show max 1 active toast, on desktop max 2 so screen is never covered
                const isMobile = window.innerWidth <= 768;
                const maxAllowed = isMobile ? 1 : 2;
                while (container.children.length >= maxAllowed) {
                    container.removeChild(container.firstChild);
                }

                const toast = document.createElement('div');
                toast.className = 'toast-msg';

                let icon = 'fa-check';
                let iconType = type || 'success';
                if (type === 'alert') { icon = 'fa-exclamation'; iconType = 'alert'; }
                else if (type === 'payment') { icon = 'fa-mobile-alt'; iconType = 'payment'; }
                else if (type === 'voucher') { icon = 'fa-ticket-alt'; iconType = 'voucher'; }
                else if (type === 'router') { icon = 'fa-network-wired'; iconType = 'router'; }

                toast.innerHTML = `
                    <div class="toast-icon-wrap ${iconType}">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="toast-body" title="${message}">${message}</div>
                    <button type="button" class="toast-close-btn" aria-label="Funga">&times;</button>
                `;

                // Handle click: close button dismisses, body marks as read and navigates to item
                toast.addEventListener('click', function(e) {
                    const isClose = e.target.closest('.toast-close-btn');
                    if (isClose) {
                        e.stopPropagation();
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        setTimeout(() => { if (toast.parentNode) toast.remove(); }, 250);
                        return;
                    }

                    if (id && typeof window.markNotificationRead === 'function') {
                        window.markNotificationRead(id);
                    }

                    if (link) {
                        window.location.href = link;
                    } else {
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        setTimeout(() => { if (toast.parentNode) toast.remove(); }, 250);
                    }
                });

                container.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.classList.add('show');
                });

                // Auto dismiss after 4s
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        setTimeout(() => { if (toast.parentNode) toast.remove(); }, 250);
                    }
                }, 4000);
            }

            function fetchLatestNotifications() {
                fetch('/admin/notifications/latest-json')
                    .then(res => res.json())
                    .then(data => {
                        const badge = document.getElementById('notificationBadge');
                        if (badge) {
                            if (data.unread_count > 0) {
                                badge.innerText = data.unread_count > 99 ? '99+' : data.unread_count;
                                badge.style.display = 'flex';
                            } else {
                                badge.style.display = 'none';
                            }
                        }

                        const list = document.getElementById('notifList');
                        if (!list) return;

                        if (!data.notifications || data.notifications.length === 0) {
                            list.innerHTML = `
                                <div id="emptyNotifState" style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                                    <i class="fas fa-bell-slash mb-2" style="font-size: 1.75rem; opacity: 0.35;"></i><br>
                                    Hakuna taarifa mpya
                                </div>
                            `;
                            return;
                        }

                        list.innerHTML = data.notifications.map(n => {
                            let iconClass = n.icon || 'fas fa-info-circle';
                            let iconColor = n.color || '#3B82F6';
                            let bgLight = 'rgba(59, 130, 246, 0.12)';
                            if (n.type === 'payment') bgLight = 'rgba(16, 185, 129, 0.12)';
                            else if (n.type === 'voucher') bgLight = 'rgba(217, 119, 6, 0.12)';
                            else if (n.type === 'alert') bgLight = 'rgba(239, 68, 68, 0.12)';
                            else if (n.type === 'router') bgLight = 'rgba(99, 102, 241, 0.12)';

                            const targetUrl = n.link || '{{ route('admin.notifications.index') }}';
                            return `
                                <a href="${targetUrl}" onclick="markNotificationRead(${n.id})" class="notif-item" style="text-decoration: none; color: inherit; display: flex; gap: 8px; align-items: flex-start; padding: 0.5rem 0.75rem; border-bottom: 1px solid var(--border-light); background: ${!n.is_read ? 'rgba(255, 51, 0, 0.02)' : 'transparent'};">
                                    <div class="notif-icon" style="background: ${bgLight}; color: ${iconColor}; width: 26px; height: 26px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0;">
                                        <i class="${iconClass}"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.76rem; font-weight: 700; color: var(--text-main); margin-bottom: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${n.title || n.message}</div>
                                        <div class="notif-text" style="font-size: 0.72rem; color: var(--text-muted); line-height: 1.25; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${n.message}</div>
                                        <div class="notif-time" style="font-size: 0.65rem; color: var(--text-muted); margin-top: 2px;">${n.time_human || ''}</div>
                                    </div>
                                    ${!n.is_read ? '<span style="width: 6px; height: 6px; border-radius: 50%; background: #EF4444; flex-shrink: 0; margin-top: 5px;"></span>' : ''}
                                </a>
                            `;
                        }).join('');
                    })
                    .catch(err => console.error('Latest notifications fetch error:', err));
            }

            window.markNotificationRead = function(id) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch(`/admin/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    keepalive: true
                }).catch(() => {});
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    let count = parseInt(badge.innerText || '0', 10);
                    if (count > 0) {
                        count--;
                        badge.innerText = count > 99 ? '99+' : (count === 0 ? '' : count);
                        if (count === 0) badge.style.display = 'none';
                    }
                }
            };

            fetchLatestNotifications();

            function checkForNotifications() {
                fetch('/admin/notifications/check')
                    .then(res => res.json())
                    .then(data => {
                        if (data.notifications && data.notifications.length > 0) {
                            playNotificationSound();
                            const bell = document.getElementById('notificationBell');
                            if (bell) {
                                bell.classList.add('ringing');
                                setTimeout(() => bell.classList.remove('ringing'), 2200);
                            }

                            const isMobileScreen = window.innerWidth <= 768;
                            const notifsToShow = isMobileScreen ? data.notifications.slice(-1) : data.notifications.slice(-2);
                            notifsToShow.forEach(notif => {
                                showToast(notif.message, notif.type, notif.link, notif.id);
                            });

                            fetchLatestNotifications();
                        }
                    })
                    .catch(err => console.error('Notification check:', err));
            }

            checkForNotifications();
            setInterval(checkForNotifications, 10000);

            // Bell Dropdown Toggle
            const bellIcon = document.getElementById('notificationBell');
            const notifDropdown = document.getElementById('notifDropdown');
            const clearBtn = document.getElementById('clearNotifsBtn');

            if (bellIcon) {
                bellIcon.addEventListener('click', function(e) {
                    if (e.target === clearBtn || (clearBtn && clearBtn.contains(e.target))) return;
                    notifDropdown.classList.toggle('show');
                    if (notifDropdown.classList.contains('show')) {
                        fetchLatestNotifications();
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!bellIcon.contains(e.target)) {
                        notifDropdown.classList.remove('show');
                    }
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch('{{ route('admin.notifications.clear_all') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        fetchLatestNotifications();
                        notifDropdown.classList.remove('show');
                        showToast('Taarifa zote zimefutwa.', 'success');
                    }).catch(err => console.error('Clear notifs error:', err));
                });
            }

            // ── Global Customer Search ──────────────────────────────────
            const searchInput = document.getElementById('globalSearch');
            const searchResults = document.getElementById('globalSearchResults');
            if (searchInput && searchResults) {
                let searchTimer;
                function renderSearchResults(data) {
                    if (!data || data.length === 0) {
                        searchResults.innerHTML = '<div style="padding: 1.25rem; text-align: center; color: var(--text-muted); font-size: 0.84rem;"><i class="fas fa-search" style="opacity: 0.3; margin-bottom: 0.4rem; display: block;"></i>Hakuna mteja aliyepatikana</div>';
                        searchResults.style.display = 'block';
                        return;
                    }
                    searchResults.innerHTML = data.map(c => `
                        <a href="${c.url}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; text-decoration: none; color: inherit; border-bottom: 1px solid var(--border-light); transition: background 0.15s;"
                           onmouseover="this.style.background='var(--surface-soft)'" onmouseout="this.style.background='transparent'">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--blue-light); color: var(--blue-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.82rem; flex-shrink: 0;">
                                ${c.name ? c.name[0].toUpperCase() : 'C'}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 700; font-size: 0.86rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${c.name || '—'}</div>
                                <div style="font-size: 0.76rem; color: var(--text-muted);">${c.phone_number} ${c.package ? '· ' + c.package : ''}</div>
                            </div>
                            <span class="badge ${c.status === 'active' ? 'success' : 'neutral'}">${c.status}</span>
                        </a>
                    `).join('');
                    searchResults.style.display = 'block';
                }

                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimer);
                    const q = this.value.trim();
                    if (q.length < 2) { searchResults.style.display = 'none'; return; }
                    searchTimer = setTimeout(() => {
                        fetch(`/admin/customers-search?q=${encodeURIComponent(q)}`)
                            .then(r => r.json())
                            .then(renderSearchResults)
                            .catch(() => { searchResults.style.display = 'none'; });
                    }, 300);
                });

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.style.display = 'none';
                    }
                });
            }

            // Virtual Keyboard Handling (Keeps Bottom Nav Down / Never Climbs Above Keyboard)
            if ('virtualKeyboard' in navigator) {
                navigator.virtualKeyboard.overlaysContent = true;
            }

            (function() {
                function isKeyboardElement(el) {
                    if (!el) return false;
                    const tag = (el.tagName || '').toLowerCase();
                    if (tag === 'textarea') return true;
                    if (tag === 'input') {
                        const type = (el.type || 'text').toLowerCase();
                        return ['text', 'tel', 'number', 'password', 'email', 'search', 'url', 'date', 'datetime-local'].includes(type);
                    }
                    return el.isContentEditable === true;
                }

                let blurTimer = null;

                function activateKeyboardState() {
                    clearTimeout(blurTimer);
                    document.body.classList.add('keyboard-open');
                }

                function deactivateKeyboardState() {
                    clearTimeout(blurTimer);
                    blurTimer = setTimeout(function() {
                        if (!isKeyboardElement(document.activeElement)) {
                            document.body.classList.remove('keyboard-open');
                        }
                    }, 120);
                }

                document.addEventListener('focusin', function(e) {
                    if (isKeyboardElement(e.target)) {
                        activateKeyboardState();
                    }
                }, { passive: true });

                document.addEventListener('focusout', function(e) {
                    if (isKeyboardElement(e.target)) {
                        deactivateKeyboardState();
                    }
                }, { passive: true });

                if (window.visualViewport) {
                    const initialHeight = window.innerHeight;
                    window.visualViewport.addEventListener('resize', function() {
                        const currentHeight = window.visualViewport.height;
                        if ((window.innerHeight - currentHeight) > 140 || (initialHeight - currentHeight) > 140) {
                            activateKeyboardState();
                        } else if (!isKeyboardElement(document.activeElement)) {
                            clearTimeout(blurTimer);
                            document.body.classList.remove('keyboard-open');
                        }
                    });
                }
            })();
        });
    </script>
    <!-- PWA Floating Install Banner for Mobile Web Browsers -->
    <div id="pwaFloatingBanner" style="position: fixed; bottom: 74px; left: 14px; right: 14px; z-index: 99998; background: rgba(15, 23, 42, 0.96); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 51, 0, 0.35); border-radius: 18px; padding: 10px 14px; display: none; align-items: center; justify-content: space-between; gap: 12px; box-shadow: 0 16px 36px rgba(0, 0, 0, 0.6);">
        <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
            <img src="{{ asset('img/icon-192.png') }}" alt="MedBando" style="width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
            <div style="min-width: 0;">
                <div style="font-size: 0.84rem; font-weight: 800; color: #FFFFFF; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Sakinisha App ya MedBando</div>
                <div style="font-size: 0.7rem; color: #94A3B8; line-height: 1.2;">Fungua kwa haraka bila browser!</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
            <button id="pwaBannerInstallBtn" type="button" style="background: linear-gradient(135deg, #FF3300, #EA580C); color: white; border: none; padding: 7px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 5px; box-shadow: 0 4px 12px rgba(255, 51, 0, 0.4);">
                <i class="fas fa-download"></i> Sakinisha
            </button>
            <button id="pwaBannerDismissBtn" type="button" style="background: transparent; border: none; color: #64748B; font-size: 1.2rem; line-height: 1; padding: 4px; cursor: pointer;">&times;</button>
        </div>
    </div>

    <!-- iOS Install Helper Modal -->
    <div id="iosInstallModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.78); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 999999; display: none; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #0F172A; border: 1px solid rgba(255,255,255,0.15); border-radius: 24px; padding: 24px; max-width: 360px; width: 100%; text-align: center; color: white; box-shadow: 0 25px 50px rgba(0,0,0,0.6);">
            <img src="{{ asset('img/icon-192.png') }}" alt="MedBando" style="width: 60px; height: 60px; border-radius: 16px; margin: 0 auto 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.4);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 6px;">Sakinisha MedBando kwenye iPhone</h3>
            <p style="font-size: 0.8rem; color: #94A3B8; margin-bottom: 16px; line-height: 1.4;">Weka icon ya MedBando kwenye skrini ya simu yako kwa hatua hizi rahisi:</p>
            <div style="text-align: left; background: rgba(255,255,255,0.05); padding: 12px 14px; border-radius: 14px; font-size: 0.78rem; line-height: 1.6; margin-bottom: 18px; color: #E2E8F0;">
                <div>1. Bonyeza kitufe cha <strong>Shiriki (Share <i class="fas fa-arrow-up-from-bracket"></i>)</strong> chini ya Safari.</div>
                <div>2. Tembeza chini kisha chagua <strong>'Weka kwenye Skrini ya Mwanzo' (Add to Home Screen)</strong>.</div>
                <div>3. Bonyeza <strong>'Ongeza' (Add)</strong> juu kulia.</div>
            </div>
            <button onclick="document.getElementById('iosInstallModal').style.display='none'" style="width: 100%; background: #FF3300; color: white; border: none; padding: 11px; border-radius: 50px; font-weight: 800; font-size: 0.9rem; cursor: pointer;">Nimeelewa</button>
        </div>
    </div>

    <!-- PWA Service Worker Registration & Installation Script -->
    <script>
        (function() {
            // Register Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('{{ asset('sw.js') }}')
                        .then(function(reg) {
                            console.log('[MedBando] PWA Service Worker active:', reg.scope);
                        })
                        .catch(function(err) {
                            console.warn('[MedBando] Service Worker failed:', err);
                        });
                });
            }

            // PWA Install prompt tracking
            let deferredPrompt = null;
            const isIos = /iphone|ipad|ipod/.test(window.navigator.userAgent.toLowerCase());
            const isInStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

            window.addEventListener('beforeinstallprompt', function(e) {
                e.preventDefault();
                deferredPrompt = e;
                if (!isInStandalone && !sessionStorage.getItem('medbando_pwa_dismissed')) {
                    const banner = document.getElementById('pwaFloatingBanner');
                    if (banner) banner.style.display = 'flex';
                }
            });

            window.addEventListener('appinstalled', function() {
                console.log('[MedBando] App installed successfully');
                const banner = document.getElementById('pwaFloatingBanner');
                if (banner) banner.style.display = 'none';
                const drawerCard = document.getElementById('pwaDrawerCard');
                if (drawerCard) drawerCard.style.display = 'none';
                deferredPrompt = null;
            });

            function handlePwaInstall() {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function(result) {
                        if (result.outcome === 'accepted') {
                            const banner = document.getElementById('pwaFloatingBanner');
                            if (banner) banner.style.display = 'none';
                        }
                        deferredPrompt = null;
                    });
                } else if (isIos) {
                    const modal = document.getElementById('iosInstallModal');
                    if (modal) modal.style.display = 'flex';
                } else {
                    alert('Ili kusakinisha MedBando kama app, bonyeza alama ya nukta tatu (menu) juu kulia mwa browser yako kisha chagua "Sakinisha App" au "Weka kwenye Skrini ya Mwanzo" (Install App / Add to Home screen).');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const drawerInstallBtn = document.getElementById('pwaInstallBtn');
                if (drawerInstallBtn) {
                    drawerInstallBtn.addEventListener('click', handlePwaInstall);
                }

                const bannerInstallBtn = document.getElementById('pwaBannerInstallBtn');
                if (bannerInstallBtn) {
                    bannerInstallBtn.addEventListener('click', handlePwaInstall);
                }

                const bannerDismissBtn = document.getElementById('pwaBannerDismissBtn');
                if (bannerDismissBtn) {
                    bannerDismissBtn.addEventListener('click', function() {
                        const banner = document.getElementById('pwaFloatingBanner');
                        if (banner) banner.style.display = 'none';
                        sessionStorage.setItem('medbando_pwa_dismissed', '1');
                    });
                }

                if (isInStandalone) {
                    const banner = document.getElementById('pwaFloatingBanner');
                    if (banner) banner.style.display = 'none';
                    const drawerCard = document.getElementById('pwaDrawerCard');
                    if (drawerCard) drawerCard.style.display = 'none';
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
