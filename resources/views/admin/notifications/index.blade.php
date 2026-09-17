@extends('layouts.app')

@section('title', 'Kituo cha Taarifa & SMS')

@section('content')
<div class="notifications-container" style="padding-bottom: 5rem;">

    <style>
        /* Modern Smart Notifications Styling */
        /* Modern Compact Notifications Styling */
        .notif-header-box {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 0.75rem 1.15rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .notif-title-group {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .notif-main-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(255, 51, 0, 0.12), rgba(255, 107, 61, 0.18));
            color: #FF3300;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(255, 51, 0, 0.12);
        }

        /* Compact KPI Cards Grid */
        .notif-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 0.45rem;
            margin-bottom: 0.75rem;
        }

        .notif-kpi-card {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0.5rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.15s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
        }

        .notif-kpi-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #CBD5E1;
        }

        .notif-kpi-card.active {
            border-color: #FF3300;
            background: #FFFDFD;
            box-shadow: 0 2px 8px rgba(255, 51, 0, 0.1);
        }

        .notif-kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notif-kpi-label {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: var(--text-muted);
        }

        .notif-kpi-icon {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }

        .notif-kpi-val {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
            font-family: 'Outfit', sans-serif;
        }

        /* Smart Toolbar & Bulk Actions */
        .smart-toolbar {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 0.55rem 0.85rem;
            margin-bottom: 0.65rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .toolbar-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .search-pill-box {
            position: relative;
            flex: 1;
            min-width: 200px;
            max-width: 420px;
        }

        .search-pill-input {
            width: 100%;
            height: 34px;
            padding: 0 2rem 0 2.1rem;
            border-radius: 10px;
            border: 1.5px solid var(--border-color);
            background: #F8FAFC;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s;
        }

        .search-pill-input:focus {
            background: #FFFFFF;
            border-color: #FF3300;
            box-shadow: 0 0 0 3px rgba(255, 51, 0, 0.1);
        }

        .search-pill-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.78rem;
            pointer-events: none;
        }

        .search-pill-clear {
            position: absolute;
            right: 0.65rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 0.85rem;
            cursor: pointer;
            padding: 4px;
        }

        /* Filter Segmented Pills */
        .filter-segmented-strip {
            display: flex;
            gap: 0.4rem;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .filter-seg-btn {
            padding: 0.4rem 0.85rem;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.18s;
            background: #F1F5F9;
            color: var(--text-secondary);
            border: 1px solid transparent;
        }

        .filter-seg-btn:hover {
            background: #E2E8F0;
            color: var(--text-main);
        }

        .filter-seg-btn.active {
            background: #0F172A;
            color: #FFFFFF;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.25);
        }

        .filter-seg-count {
            padding: 1px 6px;
            border-radius: 20px;
            font-size: 0.68rem;
            background: rgba(255, 255, 255, 0.2);
        }

        .filter-seg-btn:not(.active) .filter-seg-count {
            background: rgba(0, 0, 0, 0.08);
            color: var(--text-muted);
        }

        /* Floating Bulk Action Bar */
        .bulk-action-bar {
            display: none;
            align-items: center;
            justify-content: space-between;
            background: #0F172A;
            color: #FFFFFF;
            border-radius: 14px;
            padding: 0.65rem 1rem;
            gap: 0.75rem;
            animation: slideDown 0.25s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bulk-counter-chip {
            font-size: 0.8rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #38BDF8;
        }

        .bulk-btns-wrap {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-bulk-action {
            padding: 0.4rem 0.85rem;
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
        }

        .btn-bulk-action.read {
            background: rgba(16, 185, 129, 0.2);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }
        .btn-bulk-action.read:hover {
            background: #10B981;
            color: #FFFFFF;
        }

        .btn-bulk-action.delete {
            background: rgba(239, 68, 68, 0.2);
            color: #F87171;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }
        .btn-bulk-action.delete:hover {
            background: #EF4444;
            color: #FFFFFF;
        }

        .btn-bulk-action.cancel {
            background: rgba(255, 255, 255, 0.1);
            color: #CBD5E1;
        }
        .btn-bulk-action.cancel:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
        }

        /* Ultra-Compact Smart Notification Cards */
        .notif-smart-card {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0.5rem 0.85rem;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            transition: all 0.15s ease;
            position: relative;
            cursor: pointer;
        }

        .notif-smart-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            border-color: #CBD5E1;
        }

        .notif-smart-card.unread {
            background: #FFFFFF;
            border-left: 3.5px solid #FF3300;
            box-shadow: 0 2px 8px rgba(255, 51, 0, 0.03);
        }

        .notif-smart-card.unread.type-payment { border-left-color: #10B981; }
        .notif-smart-card.unread.type-voucher { border-left-color: #D97706; }
        .notif-smart-card.unread.type-alert { border-left-color: #EF4444; }
        .notif-smart-card.unread.type-router { border-left-color: #6366F1; }

        .notif-checkbox-wrap {
            display: flex;
            align-items: center;
        }

        .notif-checkbox {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            accent-color: #FF3300;
            cursor: pointer;
        }

        .notif-avatar-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            flex-shrink: 0;
            margin-top: 0;
        }

        .notif-body-content {
            flex: 1;
            min-width: 0;
        }

        .notif-top-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.1rem;
            gap: 0.4rem;
        }

        .notif-title-row {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
            flex: 1;
        }

        .notif-type-badge {
            font-size: 0.62rem;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 4px;
            letter-spacing: 0.2px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            flex-shrink: 0;
        }

        .notif-time-badge {
            font-size: 0.68rem;
            color: var(--text-muted);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .notif-unread-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #EF4444;
            flex-shrink: 0;
        }

        .notif-card-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .notif-desc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 1px;
        }

        .notif-card-msg {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin: 0;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
        }

        .notif-quick-del-btn {
            background: none;
            border: none;
            color: #94A3B8;
            padding: 4px 6px;
            border-radius: 6px;
            font-size: 0.78rem;
            cursor: pointer;
            opacity: 0.45;
            transition: all 0.15s;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notif-quick-del-btn:hover {
            opacity: 1;
            color: #EF4444;
            background: rgba(239, 68, 68, 0.08);
        }

        @media (max-width: 640px) {
            .notif-header-box {
                padding: 0.65rem 0.85rem;
            }
            .smart-toolbar {
                padding: 0.5rem;
            }
            .notif-smart-card {
                padding: 0.45rem 0.65rem;
                gap: 0.5rem;
            }
            .notif-avatar-icon {
                width: 26px;
                height: 26px;
                font-size: 0.72rem;
            }
            .notif-card-title {
                font-size: 0.78rem;
            }
            .notif-card-msg {
                font-size: 0.72rem;
            }
        }
    </style>

    {{-- 1. Page Header with Title and Quick Top Actions --}}
    <div class="notif-header-box">
        <div class="notif-title-group">
            <div class="notif-main-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">
                    Kituo cha Taarifa &amp; SMS
                </h2>
                <p style="margin: 0.1rem 0 0; font-size: 0.76rem; color: var(--text-muted);">
                    Taarifa zote za papo hapo: Malipo ya simu, vocha, routers na shughuli za mtandao
                </p>
            </div>
        </div>

        <div style="display: flex; gap: 0.45rem; align-items: center; flex-wrap: wrap;">
            {{-- Weka Zote Kuwa Zimesomwa --}}
            <button type="button" onclick="markAllNotificationsRead()" class="btn-banking btn-secondary" style="font-size: 0.78rem; padding: 0.35rem 0.8rem; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; font-weight: 700; height: 34px;">
                <i class="fas fa-check-double" style="color: #10B981;"></i>
                <span>Weka Zote Zimesomwa</span>
            </button>

            {{-- Futa Zote --}}
            <button type="button" onclick="clearAllNotifications()" class="btn-banking btn-secondary" style="font-size: 0.78rem; padding: 0.35rem 0.8rem; border-radius: 10px; cursor: pointer; color: #EF4444; border-color: rgba(239, 68, 68, 0.3); display: inline-flex; align-items: center; gap: 5px; font-weight: 700; height: 34px;">
                <i class="fas fa-trash-alt"></i>
                <span>Futa Zote</span>
            </button>
        </div>
    </div>

    {{-- 2. Interactive KPI Filter Cards --}}
    <div class="notif-kpi-grid">
        {{-- Zote --}}
        <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" class="notif-kpi-card {{ $filter === 'all' && empty($search) ? 'active' : '' }}">
            <div class="notif-kpi-header">
                <span class="notif-kpi-label">Zote</span>
                <div class="notif-kpi-icon" style="background: rgba(15, 23, 42, 0.08); color: #0F172A;">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <div class="notif-kpi-val" id="statTotalVal">{{ number_format($stats['total']) }}</div>
        </a>

        {{-- Hazijasomwa (Mpya) --}}
        <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" class="notif-kpi-card {{ $filter === 'unread' ? 'active' : '' }}" style="{{ $stats['unread'] > 0 ? 'border-color: rgba(239, 68, 68, 0.4);' : '' }}">
            <div class="notif-kpi-header">
                <span class="notif-kpi-label" style="{{ $stats['unread'] > 0 ? 'color: #DC2626;' : '' }}">Mpya</span>
                <div class="notif-kpi-icon" style="background: rgba(239, 68, 68, 0.12); color: #DC2626;">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
            <div class="notif-kpi-val" id="statUnreadVal" style="{{ $stats['unread'] > 0 ? 'color: #DC2626;' : '' }}">{{ number_format($stats['unread']) }}</div>
        </a>

        {{-- Malipo (Payments) --}}
        <a href="{{ route('admin.notifications.index', ['filter' => 'payment']) }}" class="notif-kpi-card {{ $filter === 'payment' ? 'active' : '' }}">
            <div class="notif-kpi-header">
                <span class="notif-kpi-label">Malipo</span>
                <div class="notif-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">
                    <i class="fas fa-mobile-alt"></i>
                </div>
            </div>
            <div class="notif-kpi-val">{{ number_format($stats['payment']) }}</div>
        </a>

        {{-- Vocha (Vouchers) --}}
        <a href="{{ route('admin.notifications.index', ['filter' => 'voucher']) }}" class="notif-kpi-card {{ $filter === 'voucher' ? 'active' : '' }}">
            <div class="notif-kpi-header">
                <span class="notif-kpi-label">Vocha</span>
                <div class="notif-kpi-icon" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
            <div class="notif-kpi-val">{{ number_format($stats['voucher']) }}</div>
        </a>

        {{-- Router & Tahadhari --}}
        <a href="{{ route('admin.notifications.index', ['filter' => 'router']) }}" class="notif-kpi-card {{ $filter === 'router' ? 'active' : '' }}">
            <div class="notif-kpi-header">
                <span class="notif-kpi-label">Router</span>
                <div class="notif-kpi-icon" style="background: rgba(99, 102, 241, 0.12); color: #6366F1;">
                    <i class="fas fa-network-wired"></i>
                </div>
            </div>
            <div class="notif-kpi-val">{{ number_format($stats['router']) }}</div>
        </a>
    </div>

    {{-- 3. Smart Search & Filter Toolbar --}}
    <div class="smart-toolbar">
        <div class="toolbar-top-row">
            {{-- Quick Live Search --}}
            <form method="GET" action="{{ route('admin.notifications.index') }}" style="display: flex; gap: 0.5rem; flex: 1; max-width: 480px; margin: 0;">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <div class="search-pill-box">
                    <i class="fas fa-search search-pill-icon"></i>
                    <input type="text" name="search" id="notifSearchInput" value="{{ $search ?? '' }}" placeholder="Tafuta namba ya simu, vocha au maelezo..." class="search-pill-input">
                    @if(!empty($search))
                        <a href="{{ route('admin.notifications.index', ['filter' => $filter]) }}" class="search-pill-clear" title="Ondoa Utafutaji">&times;</a>
                    @endif
                </div>
                <button type="submit" class="btn-banking btn-primary" style="height: 34px; padding: 0 0.85rem; border-radius: 10px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <span>Tafuta</span>
                </button>
            </form>

            {{-- Select All Master Checkbox --}}
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; font-weight: 700; color: var(--text-main); cursor: pointer; user-select: none;">
                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="width: 17px; height: 17px; accent-color: #FF3300; cursor: pointer;">
                    <span>Chagua Zote</span>
                </label>
            </div>
        </div>

        {{-- Filter Segmented Tabs --}}
        <div class="filter-segmented-strip">
            <a href="{{ route('admin.notifications.index', ['filter' => 'all', 'search' => $search]) }}" class="filter-seg-btn {{ $filter === 'all' ? 'active' : '' }}">
                <i class="fas fa-list-ul"></i>
                <span>Zote</span>
                <span class="filter-seg-count">{{ $stats['total'] }}</span>
            </a>

            <a href="{{ route('admin.notifications.index', ['filter' => 'unread', 'search' => $search]) }}" class="filter-seg-btn {{ $filter === 'unread' ? 'active' : '' }}">
                <i class="fas fa-circle" style="font-size: 0.5rem; color: #EF4444;"></i>
                <span>Mpya</span>
                <span class="filter-seg-count">{{ $stats['unread'] }}</span>
            </a>

            <a href="{{ route('admin.notifications.index', ['filter' => 'payment', 'search' => $search]) }}" class="filter-seg-btn {{ $filter === 'payment' ? 'active' : '' }}">
                <i class="fas fa-mobile-alt"></i>
                <span>Malipo</span>
                <span class="filter-seg-count">{{ $stats['payment'] }}</span>
            </a>

            <a href="{{ route('admin.notifications.index', ['filter' => 'voucher', 'search' => $search]) }}" class="filter-seg-btn {{ $filter === 'voucher' ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i>
                <span>Vocha</span>
                <span class="filter-seg-count">{{ $stats['voucher'] }}</span>
            </a>

            <a href="{{ route('admin.notifications.index', ['filter' => 'router', 'search' => $search]) }}" class="filter-seg-btn {{ $filter === 'router' ? 'active' : '' }}">
                <i class="fas fa-network-wired"></i>
                <span>Router</span>
                <span class="filter-seg-count">{{ $stats['router'] }}</span>
            </a>
        </div>

        {{-- Floating Bulk Actions Bar (Reveals when items are selected) --}}
        <div class="bulk-action-bar" id="bulkActionBar">
            <div class="bulk-counter-chip">
                <i class="fas fa-check-circle"></i>
                <span id="selectedCountText">Zilizoteuliwa: 0</span>
            </div>
            <div class="bulk-btns-wrap">
                <button type="button" onclick="executeBulkRead()" class="btn-bulk-action read">
                    <i class="fas fa-check-double"></i>
                    <span>Weka Zimesomwa</span>
                </button>
                <button type="button" onclick="executeBulkDelete()" class="btn-bulk-action delete">
                    <i class="fas fa-trash-alt"></i>
                    <span>Futa Zilizochaguliwa</span>
                </button>
                <button type="button" onclick="cancelSelection()" class="btn-bulk-action cancel">
                    <i class="fas fa-times"></i>
                    <span>Ghairi</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 4. Notification Cards List --}}
    <div id="notificationsListWrap">
        @forelse($notifications as $notif)
            @php
                $isUnread = !$notif->is_read;
                $iconClass = $notif->icon ?: 'fas fa-bell';
                $typeClass = 'type-' . $notif->type;
                $bgLight = 'rgba(59, 130, 246, 0.12)';
                $iconColor = '#3B82F6';
                $typeLabel = 'Taarifa';

                if ($notif->type === 'payment') {
                    $bgLight = 'rgba(16, 185, 129, 0.14)';
                    $iconColor = '#10B981';
                    $typeLabel = 'Malipo ya Simu';
                } elseif ($notif->type === 'voucher') {
                    $bgLight = 'rgba(217, 119, 6, 0.14)';
                    $iconColor = '#D97706';
                    $typeLabel = 'Vocha Imetumika';
                } elseif ($notif->type === 'alert') {
                    $bgLight = 'rgba(239, 68, 68, 0.14)';
                    $iconColor = '#EF4444';
                    $typeLabel = 'Tahadhari';
                } elseif ($notif->type === 'router') {
                    $bgLight = 'rgba(99, 102, 241, 0.14)';
                    $iconColor = '#6366F1';
                    $typeLabel = 'Router / Mtandao';
                }
            @endphp

            <div class="notif-smart-card {{ $isUnread ? 'unread ' . $typeClass : '' }}" id="notif-card-{{ $notif->id }}" onclick="handleCardClick(event, {{ $notif->id }}, '{{ route('admin.notifications.go', $notif->id) }}')">
                {{-- Checkbox --}}
                <div class="notif-checkbox-wrap" onclick="event.stopPropagation()">
                    <input type="checkbox" value="{{ $notif->id }}" class="notif-checkbox notif-item-checkbox" onchange="updateSelectedCount()">
                </div>

                {{-- Type Avatar Icon --}}
                <div class="notif-avatar-icon" style="background: {{ $bgLight }}; color: {{ $iconColor }};">
                    <i class="{{ $iconClass }}"></i>
                </div>

                {{-- Body Content --}}
                <div class="notif-body-content">
                    <div class="notif-top-meta">
                        <div class="notif-title-row">
                            <span class="notif-type-badge" style="background: {{ $bgLight }}; color: {{ $iconColor }};">
                                <i class="{{ $iconClass }}" style="font-size: 0.65rem;"></i>
                                <span>{{ $typeLabel }}</span>
                            </span>
                            <h4 class="notif-card-title">{{ $notif->title }}</h4>
                            @if($isUnread)
                                <span class="notif-unread-dot" title="Haijasomwa"></span>
                            @endif
                        </div>

                        <div class="notif-time-badge">
                            <i class="far fa-clock"></i>
                            <span>{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="notif-desc-row">
                        <p class="notif-card-msg">{{ $notif->message }}</p>
                    </div>
                </div>

                {{-- Subtle Quick Delete (Minimal Icon, does not trigger card navigation) --}}
                <button type="button" class="notif-quick-del-btn" onclick="deleteSingleNotif(event, {{ $notif->id }})" title="Futa Taarifa">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        @empty
            <div style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 20px; padding: 4rem 1.5rem; text-align: center; box-shadow: var(--shadow-xs);">
                <div style="width: 64px; height: 64px; border-radius: 18px; background: rgba(15, 23, 42, 0.04); color: var(--text-muted); display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin: 0 0 0.4rem 0;">
                    Hakuna Taarifa Zilizopatikana
                </h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0 0 1.25rem 0; max-width: 420px; margin-left: auto; margin-right: auto; line-height: 1.5;">
                    @if(!empty($search))
                        Hakuna taarifa inayolingana na utafutaji wako wa "<strong>{{ $search }}</strong>".
                    @elseif($filter === 'unread')
                        Umesoma taarifa zote! Taarifa mpya zikija zitaonekana hapa moja kwa moja.
                    @else
                        Mfumo haujarekodi taarifa mpya kwenye kundi hili kwa sasa.
                    @endif
                </p>
                <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" class="btn-banking btn-primary" style="font-size: 0.82rem; padding: 0.55rem 1.25rem; border-radius: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-sync-alt"></i> Onyesha Taarifa Zote
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div style="margin-top: 1.25rem; background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 14px; padding: 0.85rem 1.25rem; box-shadow: var(--shadow-xs);">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

{{-- Dynamic Smart AJAX JavaScript --}}
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function updateBadgeInDom(newCount) {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (newCount > 0) {
                badge.innerText = newCount > 99 ? '99+' : newCount;
                badge.style.display = 'flex';
            } else {
                badge.innerText = '0';
                badge.style.display = 'none';
            }
        }
        const statUnread = document.getElementById('statUnreadVal');
        if (statUnread) {
            statUnread.innerText = newCount;
        }
    }

    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.notif-item-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
        updateSelectedCount();
    }

    function cancelSelection() {
        const master = document.getElementById('selectAllCheckbox');
        if (master) master.checked = false;
        const checkboxes = document.querySelectorAll('.notif-item-checkbox');
        checkboxes.forEach(cb => { cb.checked = false; });
        updateSelectedCount();
    }

    function getSelectedIds() {
        const checked = document.querySelectorAll('.notif-item-checkbox:checked');
        return Array.from(checked).map(cb => cb.value);
    }

    function updateSelectedCount() {
        const ids = getSelectedIds();
        const bar = document.getElementById('bulkActionBar');
        const text = document.getElementById('selectedCountText');
        const master = document.getElementById('selectAllCheckbox');
        const total = document.querySelectorAll('.notif-item-checkbox').length;

        if (master) {
            master.checked = total > 0 && ids.length === total;
        }

        if (ids.length > 0) {
            bar.style.display = 'flex';
            text.innerText = `Zilizoteuliwa: ${ids.length}`;
        } else {
            bar.style.display = 'none';
        }
    }

    function handleCardClick(event, id, targetUrl) {
        if (event.target.closest('.notif-checkbox-wrap') || event.target.closest('.notif-quick-del-btn')) {
            return;
        }

        // Visually mark as read in DOM immediately
        const card = document.getElementById('notif-card-' + id);
        if (card) {
            card.classList.remove('unread', 'type-payment', 'type-voucher', 'type-alert', 'type-router');
            const dot = card.querySelector('.notif-unread-dot');
            if (dot) dot.remove();
        }

        // Decrement unread counter in DOM
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            let count = parseInt(badge.innerText || '0', 10);
            if (count > 0) {
                count--;
                badge.innerText = count > 99 ? '99+' : (count === 0 ? '' : count);
                if (count === 0) badge.style.display = 'none';
            }
        }
        const statUnread = document.getElementById('statUnreadVal');
        if (statUnread) {
            let sCount = parseInt(statUnread.innerText || '0', 10);
            if (sCount > 0) statUnread.innerText = (sCount - 1);
        }

        if (targetUrl) {
            window.location.href = targetUrl;
        } else {
            markSingleRead(id, false);
        }
    }

    function markSingleRead(id, shouldShowToast = true) {
        fetch(`/admin/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            keepalive: true
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById('notif-card-' + id);
                if (card) {
                    card.classList.remove('unread', 'type-payment', 'type-voucher', 'type-alert', 'type-router');
                    const dot = card.querySelector('.notif-unread-dot');
                    if (dot) dot.remove();
                }
                updateBadgeInDom(data.unread_count);
                if (shouldShowToast && typeof showToast === 'function') {
                    showToast('Imetiwa alama kuwa imesomwa.', 'success');
                }
            }
        })
        .catch(err => console.error('Mark read error:', err));
    }

    function deleteSingleNotif(event, id) {
        if (event) event.stopPropagation();
        if (!confirm('Je, una uhakika unataka kufuta taarifa hii?')) return;

        fetch(`/admin/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById('notif-card-' + id);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 250);
                }
                updateBadgeInDom(data.unread_count);
                if (typeof showToast === 'function') {
                    showToast('Taarifa imefutwa.', 'success');
                }
            }
        })
        .catch(err => console.error('Delete notif error:', err));
    }

    function executeBulkRead() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        fetch('{{ route('admin.notifications.bulk_read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                ids.forEach(id => {
                    const card = document.getElementById('notif-card-' + id);
                    if (card) {
                        card.classList.remove('unread', 'type-payment', 'type-voucher', 'type-alert', 'type-router');
                    }
                });
                cancelSelection();
                updateBadgeInDom(data.unread_count);
                if (typeof showToast === 'function') {
                    showToast(`${ids.length} taarifa zimetiwa alama kuwa zimesomwa.`, 'success');
                }
            }
        })
        .catch(err => console.error('Bulk read error:', err));
    }

    function executeBulkDelete() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;
        if (!confirm(`Je, una uhakika unataka kufuta taarifa ${ids.length} zilizoteuliwa?`)) return;

        fetch('{{ route('admin.notifications.bulk_delete') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                ids.forEach(id => {
                    const card = document.getElementById('notif-card-' + id);
                    if (card) {
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 250);
                    }
                });
                cancelSelection();
                updateBadgeInDom(data.unread_count);
                if (typeof showToast === 'function') {
                    showToast(`${ids.length} taarifa zimefutwa kikamilifu.`, 'success');
                }
            }
        })
        .catch(err => console.error('Bulk delete error:', err));
    }

    function markAllNotificationsRead() {
        fetch('{{ route('admin.notifications.mark_all_read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notif-smart-card.unread').forEach(card => {
                    card.classList.remove('unread', 'type-payment', 'type-voucher', 'type-alert', 'type-router');
                });
                updateBadgeInDom(0);
                if (typeof showToast === 'function') {
                    showToast('Taarifa zote zimetiwa alama kuwa zimesomwa.', 'success');
                }
            }
        })
        .catch(err => console.error('Mark all read error:', err));
    }

    function clearAllNotifications() {
        if (!confirm('Una uhakika unataka kufuta taarifa zote kabisa?')) return;

        fetch('{{ route('admin.notifications.clear_all') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(err => console.error('Clear all error:', err));
    }
</script>
@endsection
