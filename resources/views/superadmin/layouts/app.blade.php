<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Super Admin Control Center</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-navy: #0B1F3A;
            --deep-navy: #07162A;
            --primary-blue: #2563EB;
            --blue-accent: #3B82F6;
            --teal: #0F766E;
            --success: #16A34A;
            --warning: #D97706;
            --danger: #DC2626;
            --background: #F6F8FC;
            --surface: #FFFFFF;
            --card-bg: #FFFFFF;
            --soft-surface: #F8FAFC;
            --border-color: #E5E7EB;
            --glass-border: #E5E7EB;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--deep-navy);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 50;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.25s ease;
        }

        .brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.15rem;
            font-weight: 800;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-badge {
            background: rgba(37, 99, 235, 0.3);
            color: #60A5FA;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .nav-links {
            list-style: none;
            padding: 1.25rem 0.85rem;
            flex: 1;
            overflow-y: auto;
        }

        .nav-item {
            margin-bottom: 0.35rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem 1rem;
            color: #94A3B8;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.06);
        }

        .nav-link.active {
            color: #FFFFFF;
            background: var(--primary-blue);
        }

        .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .nav-header {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748B;
            padding: 0.8rem 1rem 0.4rem 1rem;
        }

        .user-profile {
            padding: 1.1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(0, 0, 0, 0.15);
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--primary-blue);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 2rem;
            min-height: 100vh;
            background-color: var(--background);
            max-width: calc(100vw - var(--sidebar-width));
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-title p {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin: 0.25rem 0 0 0;
        }

        /* Cards */
        .glass-card, .banking-card {
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        /* Buttons */
        .btn-banking, .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.65rem 1.25rem;
            border: 1px solid transparent;
            font-size: 0.875rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: #1D4ED8;
            color: #FFFFFF;
        }

        .btn-secondary {
            background: #FFFFFF;
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: var(--soft-surface);
        }

        .btn-danger {
            background: var(--danger);
            color: #FFFFFF;
        }

        /* Tables */
        .table-banking, table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-banking th, table th {
            padding: 0.85rem 1.2rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            background: #F8FAFC;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }

        .table-banking td, table td {
            padding: 0.9rem 1.2rem;
            font-size: 0.875rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .table-banking tbody tr:hover, table tbody tr:hover {
            background: #F8FAFC;
        }

        /* Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.7rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-success { background: #ECFDF5; color: #065F46; }
        .badge-danger { background: #FEF2F2; color: #991B1B; }
        .badge-warning { background: #FFFBEB; color: #92400E; }
        .badge-info { background: #EFF6FF; color: #1E40AF; }
        .badge-secondary { background: #F1F5F9; color: #475569; }

        /* Mobile Topbar */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--deep-navy);
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: 60;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .mobile-brand {
            font-size: 1.1rem;
            font-weight: 800;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mobile-toggle {
            background: none;
            border: none;
            color: #FFFFFF;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                max-width: 100vw;
                padding: 5rem 1rem 2rem 1rem;
            }
            .mobile-topbar {
                display: flex;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Mobile Topbar -->
    <div class="mobile-topbar">
        <div class="mobile-brand">
            <i class="fas fa-satellite-dish" style="color:var(--primary-blue);"></i> WiFi SaaS
            <span class="brand-badge">Super Admin</span>
        </div>
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 45;"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="superadmin-sidebar">
        <div class="brand">
            <i class="fas fa-satellite-dish" style="color:#60A5FA;"></i>
            <span>WiFi SaaS</span>
            <span class="brand-badge">Super</span>
        </div>
        
        <ul class="nav-links">
            <li class="nav-item" style="margin-bottom: 1rem;">
                <a href="{{ route('admin.dashboard') }}" class="nav-link" style="background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-arrow-left"></i>
                    <span>Manage WiFi Business</span>
                </a>
            </li>

            <div class="nav-header">PLATFORM CONTROL</div>

            <li class="nav-item">
                <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Control Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.tenants.index') }}" class="nav-link {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>Tenants / Biashara</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.plans.index') }}" class="nav-link {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                    <i class="fas fa-cube"></i>
                    <span>Vifurushi vya SaaS</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('superadmin.subscriptions.index') }}" class="nav-link {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    <span>Subscriptions / Malipo</span>
                </a>
            </li>
        </ul>

        <div class="user-profile">
            <div class="avatar">
                {{ substr(auth()->user()->name ?? 'SA', 0, 1) }}
            </div>
            <div style="overflow: hidden; flex: 1;">
                <div style="font-weight: 700; color: #FFFFFF; font-size: 0.85rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                <div style="font-size: 0.72rem; color: #94A3B8;">Root Administrator</div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #94A3B8; cursor: pointer; padding: 0.4rem; font-size: 0.9rem;" title="Ondoka">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('superadmin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                overlay.style.display = 'none';
            } else {
                sidebar.classList.add('open');
                overlay.style.display = 'block';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
