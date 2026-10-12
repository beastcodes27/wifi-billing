<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    @php
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        $branding = $tenant ? $tenant->branding() : [
            'name' => 'MedBando', 'tagline' => 'FAST & UNLIMITED WI-FI',
            'phone' => '0762457308', 'whatsapp' => null, 'email' => null,
            'primary_color' => '#FF3300', 'accent_color' => '#cc2900', 'logo' => null,
        ];
        $tenantName = strtoupper($branding['name']);
    @endphp
    <meta name="theme-color" content="{{ $branding['primary_color'] }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $tenantName }}">
    <meta name="application-name" content="{{ $tenantName }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ $branding['logo'] ? asset($branding['logo']) : asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <title>{{ $tenantName }} - WiFi Hotspot</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: {{ $branding['primary_color'] }}; /* Owner-configured brand colour */
            --primary-dark: {{ $branding['accent_color'] }};
            --bg-top: #121212; /* Very Dark Grey Background */
            --bg-bottom: #121212; /* Very Dark Grey Background */
            --text-dark: #ffffff; /* White text for dark mode */
            --text-muted: #a1a1aa;
            --card-border: #3f3f46;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body {
            background-color: #000000; 
            color: var(--text-dark);
            min-height: 100vh;
        }
        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            background: var(--bg-bottom);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
            overflow-x: hidden;
        }
        .header-bg {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark, #cc2900));
            padding: 0.95rem 1rem 1.95rem 1rem;
            color: white;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 14px rgba(255, 51, 0, 0.2);
        }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; }
        .contact-info { display: flex; flex-direction: column; }
        .contact-label { font-size: 0.65rem; font-weight: 600; opacity: 0.85; margin-bottom: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .contact-number { font-size: 1.05rem; font-weight: 800; letter-spacing: 0.5px; line-height: 1.1; }
        .lang-toggle { background: rgba(255,255,255,0.18); border-radius: 20px; padding: 0.15rem; display: flex; gap: 0.15rem; align-items: center; }
        .lang-btn { padding: 0.2rem 0.55rem; border-radius: 10px; font-size: 0.68rem; font-weight: 700; border: none; background: transparent; color: white; transition: all 0.2s; cursor: pointer; text-decoration: none;}
        .lang-btn.active { background: #121212; color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.25); }
        
        .welcome-title { font-size: 1.35rem; font-weight: 900; text-align: center; margin-bottom: 0.1rem; letter-spacing: 0.5px; line-height: 1.15; }
        .welcome-sub { text-align: center; color: rgba(255,255,255,0.85); font-size: 0.68rem; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;}
        
        .payment-methods {
            display: flex;
            justify-content: space-between;
            background: #18181b;
            padding: 0.45rem 0.85rem;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
            margin: -1.2rem 0.85rem 0.45rem 0.85rem;
            box-shadow: 0 6px 16px rgba(0,0,0,0.3);
            position: relative;
            z-index: 10;
        }
        .method-icon { display: flex; flex-direction: column; align-items: center; gap: 0.15rem; font-size: 0.58rem; font-weight: 700; color: var(--text-muted); }
        .method-icon svg { width: 14px; height: 14px; }
        .method-icon.mpesa svg { color: #ef4444; }
        .method-icon.airtel svg { color: #dc2626; }
        .method-icon.halo svg { color: #f97316; }
        .method-icon.tigo svg { color: #3b82f6; }
        
        .card-section {
            background: #18181b;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 0.65rem 0.85rem;
            margin: 0 0.85rem 0.45rem 0.85rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .section-title { display: flex; align-items: center; gap: 0.4rem; font-size: 0.84rem; font-weight: 800; margin-bottom: 0.45rem; color: var(--text-dark); }
        .section-icon { background: rgba(255, 51, 0, 0.12); color: var(--primary); width: 24px; height: 24px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.78rem; flex-shrink: 0; }
        .section-icon svg { width: 14px; height: 14px; }
        .section-icon.blue { background: rgba(255, 51, 0, 0.12); color: var(--primary); }
        
        .voucher-input-group { display: flex; gap: 0.45rem; }
        .voucher-input {
            flex: 1;
            min-width: 0;
            padding: 0.48rem 0.65rem;
            border: 1.5px solid var(--card-border);
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            text-align: center;
            outline: none;
            background: #121212;
            color: var(--text-dark);
            letter-spacing: 1.2px;
            transition: border-color 0.2s;
            height: 38px;
        }
        .voucher-input:focus { border-color: var(--primary); }
        .voucher-input::placeholder {
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0;
            color: #71717a;
        }
        @keyframes shakeAnim {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        .input-shake {
            animation: shakeAnim 0.32s ease-in-out;
        }
        .portal-alert {
            padding: 0.55rem 0.8rem;
            margin: 0 0.85rem 0.45rem 0.85rem;
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.45rem;
            text-align: left;
            line-height: 1.35;
        }
        .portal-alert.error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .portal-alert.success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        .btn-login {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0 0.95rem;
            font-weight: 700;
            font-size: 0.82rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            cursor: pointer;
            white-space: nowrap;
            transition: opacity 0.15s;
            height: 38px;
            box-shadow: 0 2px 6px rgba(255, 51, 0, 0.25);
        }
        .btn-login:active { opacity: 0.85; }

        .packages-wrap {
            padding: 0 0.85rem;
            margin-bottom: 0.45rem;
        }
        .packages-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; align-items: stretch; }
        .pkg-card {
            position: relative;
            overflow: hidden;
            background: #18181b;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 0.55rem 0.65rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .pkg-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, var(--primary), #ff6b3d);
        }
        .pkg-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(255, 51, 0, 0.22);
        }
        .pkg-header { margin-bottom: 0.25rem; padding-top: 0.2rem; }
        .pkg-name {
            font-weight: 800;
            font-size: 0.72rem;
            text-transform: uppercase;
            color: #a1a1aa;
            margin-bottom: 0.1rem;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pkg-price-box { display: flex; align-items: baseline; gap: 0.15rem; }
        .pkg-price { font-weight: 900; font-size: 1.18rem; color: #ffffff; line-height: 1.1; }
        .pkg-currency { font-size: 0.58rem; color: var(--primary); font-weight: 800; }
        
        .pkg-features { display: flex; flex-direction: column; gap: 0.2rem; margin: 0.35rem 0 0.45rem 0; }
        .feature-badge {
            background: #111113;
            padding: 0.16rem 0.35rem;
            border-radius: 5px;
            font-size: 0.62rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #a1a1aa;
            border: 1px solid rgba(255,255,255,0.03);
        }
        .feature-badge svg {
            width: 11px;
            height: 11px;
            flex-shrink: 0;
        }
        .feature-badge.green {
            background: rgba(255, 51, 0, 0.08);
            color: #ff6b3d;
            font-weight: 700;
            border-color: rgba(255, 51, 0, 0.15);
        }
        
        .btn-buy {
            width: 100%;
            padding: 0.4rem 0.45rem;
            background: linear-gradient(135deg, var(--primary), #e62e00);
            color: white;
            border: none;
            border-radius: 7px;
            font-weight: 800;
            font-size: 0.74rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            cursor: pointer;
            margin-top: auto;
            box-shadow: 0 2px 6px rgba(255, 51, 0, 0.2);
            transition: opacity 0.15s, transform 0.1s;
        }
        .btn-buy:active { opacity: 0.88; transform: scale(0.97); }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(6px);
            display: flex; justify-content: center; align-items: center; z-index: 99999;
            opacity: 0; pointer-events: none;
            visibility: hidden;
            transition: opacity 0.22s ease, visibility 0.22s ease;
            padding: 1rem;
        }
        .modal-content {
            background: #18181b; width: 100%; max-width: 400px;
            border-radius: 18px; border: 1px solid rgba(255,255,255,0.1);
            padding: 1.5rem 1.25rem;
            transform: scale(0.92);
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
            visibility: visible;
        }
        .modal-overlay.active .modal-content {
            transform: scale(1);
        }
        .close-modal {
            position: absolute; top: 1rem; right: 1.25rem; font-size: 1.6rem;
            color: #a1a1aa; background: none; border: none; cursor: pointer;
            width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; transition: background 0.2s, color 0.2s;
        }
        .close-modal:hover {
            color: #fff; background: rgba(255,255,255,0.1);
        }
        
        /* SVG Spinner */
        .spinner-icon {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="mobile-container">
    <div class="header-bg">
        <div class="top-bar">
            <div class="contact-info">
                <span class="contact-label">{{ $lang == 'en' ? 'Call us' : 'Tupigie' }}</span>
                @if(!empty($branding['whatsapp']))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $branding['whatsapp']) }}" class="contact-number" style="color:#fff; text-decoration:none;">{{ $branding['phone'] ?: $branding['whatsapp'] }}</a>
                @else
                    <a href="tel:{{ $branding['phone'] }}" class="contact-number" style="color:#fff; text-decoration:none;">{{ $branding['phone'] }}</a>
                @endif
            </div>
            <div class="lang-toggle">
                <a href="{{ route('portal.index', array_merge(request()->query(), ['lang' => 'en'])) }}" class="lang-btn {{ $lang == 'en' ? 'active' : '' }}">ENG</a>
                <a href="{{ route('portal.index', array_merge(request()->query(), ['lang' => 'sw'])) }}" class="lang-btn {{ $lang == 'sw' ? 'active' : '' }}">SWA</a>
            </div>
        </div>
        <!-- Welcome Section -->
        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 0.35rem; margin-bottom: 0.15rem;">
            <div style="background: #FFFFFF; padding: 4px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; flex-shrink: 0; overflow: hidden;">
                <img src="{{ !empty($branding['logo']) ? asset($branding['logo']) : asset('images/logo-icon.png') }}" alt="{{ $tenantName }} Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <h1 class="welcome-title" style="margin-bottom: 0;">{{ $tenantName }}</h1>
        </div>
        <p class="welcome-sub">{{ $branding['tagline'] }}</p>
    </div>

    <!-- Payment Methods Placeholder -->
    <div class="payment-methods">
        <div class="method-icon mpesa">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            M-PESA
        </div>
        <div class="method-icon airtel">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            AIRTEL
        </div>
        <div class="method-icon halo">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            HALOPESA
        </div>
        <div class="method-icon tigo">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            T-PESA
        </div>
    </div>


    @if(session('error') || (isset($errorMessage) && $errorMessage))
    <div class="portal-alert error" id="portalAlertBox">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> 
        <div>{{ session('error') ?: $errorMessage }}</div>
    </div>
    @endif

    @if(session('success'))
    <div class="portal-alert success">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    <!-- Voucher Login Section -->
    <div class="card-section">
        <div class="section-title">
            <div class="section-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22 10V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v4c1.1 0 2 .9 2 2s-.9 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2zm-6 7H8v-2h8v2zm0-4H8v-2h8v2zm0-4H8V7h8v2z"/></svg>
            </div>
            {{ $lang == 'en' ? 'Enter Voucher Number' : 'Weka Nambari ya Vocha' }}
        </div>

        <form onsubmit="handleVoucherLogin(event)" class="voucher-input-group" id="voucherForm">
            <input type="text" id="voucherCode" class="voucher-input" placeholder="Namba ya vocha..." required autocomplete="off" inputmode="numeric">
            <button type="submit" class="btn-login" id="btnVoucherLogin"> 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm-4.6-5.6c.4-.4.4-1 0-1.4-2.5-2.5-2.5-6.6 0-9.2.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0-3.3 3.3-3.3 8.7 0 12 .4.4 1 .4 1.4 0zm9.2 0c.4.4 1 .4 1.4 0 3.3-3.3 3.3-8.7 0-12-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4 2.5 2.5 2.5 6.6 0 9.2.4.4.4 1 0 1.4zm-6.8-2.8c.4-.4.4-1 0-1.4-1.7-1.7-1.7-4.5 0-6.2.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0-2.5 2.5-2.5 6.6 0 9 .4.4 1 .4 1.4 0zm4.4 0c.4.4 1 .4 1.4 0 2.5-2.5 2.5-6.6 0-9-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4 1.7 1.7 1.7 4.5 0 6.2.4.4.4 1 0 1.4z"/></svg>
                <span id="btnVoucherText">{{ $lang == 'en' ? 'Login' : 'Ingia' }}</span>
            </button>
        </form>
        <div id="voucherFeedback" style="display: none; margin-top: 0.5rem; padding: 0.5rem 0.75rem; border-radius: 8px; font-size: 0.78rem; font-weight: 600; text-align: left; align-items: center; gap: 0.4rem;">
            <i id="voucherFeedbackIcon" class="fas fa-exclamation-triangle"></i>
            <span id="voucherFeedbackText"></span>
        </div>
    </div>

    <!-- Packages Section -->
    <div class="packages-wrap">
        <div class="section-title">
            <div class="section-icon blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3v8h8V3H3zm6 6H5V5h4v4zm-6 4v8h8v-8H3zm6 6H5v-4h4v4zm4-16v8h8V3h-8zm6 6h-4V5h4v4zm-6 4v8h8v-8h-8zm6 6h-4v-4h4v4z"/></svg>
            </div>
            {{ $lang == 'en' ? 'Choose Package' : 'Chagua Kifurushi' }}
        </div>
        
        <div class="packages-grid">
            @forelse($packages as $pkg)
                <div class="pkg-card" onclick="openPaymentModal({{ $pkg->id }}, '{{ addslashes($pkg->name) }}', '{{ number_format($pkg->price, 0) }}')" style="cursor: pointer;">
                    <div class="pkg-header">
                        <div class="pkg-name">{{ $pkg->name }}</div>
                        <div class="pkg-price-box">
                            <div class="pkg-price">{{ number_format($pkg->price, 0) }}</div>
                            <div class="pkg-currency">TZS</div>
                        </div>
                    </div>
                    
                    <div class="pkg-features">
                        <div class="feature-badge" title="Muda wa Matumizi">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $pkg->human_duration }}
                        </div>
                        <div class="feature-badge green" title="Kiwango cha Data">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            @if($pkg->data_quota_mb)
                                {{ $pkg->data_quota_mb >= 1024 ? round($pkg->data_quota_mb / 1024, 1) . ' GB Data' : $pkg->data_quota_mb . ' MB Data' }}
                            @else
                                {{ $lang == 'en' ? 'Unlimited Data' : 'Data Bila Kikomo' }}
                            @endif
                        </div>
                    </div>
                    
                    <button type="button" class="btn-buy" onclick="event.stopPropagation(); openPaymentModal({{ $pkg->id }}, '{{ addslashes($pkg->name) }}', '{{ number_format($pkg->price, 0) }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                        {{ $lang == 'en' ? 'Buy Package' : 'Lipia kwa Simu' }}
                    </button>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 1.5rem; color: var(--text-muted); background: #18181b; border-radius: 12px;">
                    {{ $lang == 'en' ? 'No packages available.' : 'Hakuna vifurushi.' }}
                </div>
            @endforelse
        </div>
    </div>

    <!-- Portal Footer -->
    <div style="text-align: center; padding: 0.75rem 1rem 1.5rem 1rem; color: #71717a; font-size: 0.72rem; border-top: 1px solid rgba(255,255,255,0.06); margin-top: 0.6rem;">
        <div>&copy; {{ date('Y') }} {{ $tenantName }} &bull; FAST &amp; UNLIMITED WI-FI</div>
    </div>

    <!-- Payment Modal -->
    <div class="modal-overlay" id="paymentModal" onclick="if(event.target === this) closePaymentModal();">
        <div class="modal-content" style="position: relative;">
            <button class="close-modal" type="button" onclick="closePaymentModal()">&times;</button>
            <h2 style="margin-bottom: 0.25rem; font-size: 1.2rem; font-weight: 800; color: #fff;">{{ $lang == 'en' ? 'Pay for Package' : 'Lipia Kifurushi' }}</h2>
            <p style="color: var(--text-muted); margin-bottom: 0.95rem; font-size: 0.85rem;">
                {{ $lang == 'en' ? 'Package:' : 'Kifurushi:' }} <strong id="modalPkgName" style="color: #fff;"></strong> - <strong style="color: var(--primary);">TZS <span id="modalPkgPrice"></span></strong>
            </p>
            
            <form action="{{ route('portal.process') }}" method="POST" id="paymentForm" onsubmit="handlePaymentSubmit(event)">
                @csrf
                <input type="hidden" name="mac_address" value="{{ $mac }}">
                <input type="hidden" name="ip_address" value="{{ $ip }}">
                <input type="hidden" name="package_id" id="modalPkgId">
                <input type="hidden" name="login_url" value="{{ $loginUrl }}">
                <input type="hidden" name="link_orig" value="{{ $linkOrig ?? 'http://www.google.com' }}">
                
                <div style="margin-bottom: 0.95rem;">
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #d4d4d8; margin-bottom: 0.35rem; text-align: left;">
                        {{ $lang == 'en' ? 'Enter Mobile Money Phone Number' : 'Weka Namba ya Simu ya Malipo' }}
                    </label>
                    <input type="tel" name="phone_number" id="modalPhoneInput" required placeholder="07XXXXXXXX" 
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--card-border); border-radius: 10px; font-size: 1.05rem; font-weight: 700; outline: none; background: #121212; color: var(--text-dark); transition: border-color 0.3s;"
                           onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--card-border)'">
                    <small style="display: block; text-align: left; color: #a1a1aa; font-size: 0.72rem; margin-top: 0.35rem;">
                        <i class="fas fa-mobile-alt"></i> M-Pesa &bull; TigoPesa &bull; Airtel Money &bull; HaloPesa
                    </small>
                </div>
                
                <button type="submit" class="btn-buy" id="paySubmitBtn" style="font-size: 0.95rem; padding: 0.75rem; font-weight: 800; border-radius: 9px;">
                    <i class="fas fa-lock" style="margin-right: 6px;"></i> {{ $lang == 'en' ? 'Confirm Payment (PIN)' : 'Thibitisha Malipo (PIN)' }}
                </button>
            </form>

            <p style="margin-top: 0.9rem; text-align: center; font-size: 0.75rem; color: #a1a1aa; line-height: 1.4;">
                {{ $lang == 'en' ? 'Or get a voucher from the owner:' : 'Au pata vocha kwa mmiliki:' }}
                <a href="tel:{{ $branding['phone'] }}" style="color: var(--primary); font-weight: 700;">{{ $branding['phone'] }}</a>
            </p>
        </div>
    </div>
    
    <!-- Hidden POST Form for MikroTik Manual Voucher Login -->
    <form id="manualVoucherLoginForm" action="{{ $loginUrl }}" method="POST" style="display: none;">
        <input type="hidden" name="username" id="manualMtUser" value="">
        <input type="hidden" name="password" id="manualMtPass" value="">
        <input type="hidden" name="dst" value="{{ $linkOrig ?? 'http://www.google.com' }}">
        <input type="hidden" name="popup" value="true">
    </form>
</div>

    <script>
        function openPaymentModal(id, name, price) {
            document.getElementById('modalPkgId').value = id;
            document.getElementById('modalPkgName').innerText = name;
            document.getElementById('modalPkgPrice').innerText = price;
            document.getElementById('paymentModal').classList.add('active');
            
            const phoneInput = document.getElementById('modalPhoneInput');
            if (phoneInput) {
                setTimeout(() => phoneInput.focus(), 150);
            }

            const btn = document.getElementById('paySubmitBtn');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock" style="margin-right: 6px;"></i> {{ $lang == "en" ? "Confirm Payment (PIN)" : "Thibitisha Malipo (PIN)" }}';
                btn.style.opacity = '1';
            }
        }
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('active');
        }
        function handlePaymentSubmit(event) {
            const btn = document.getElementById('paySubmitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<svg class="spinner-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> {{ $lang == "en" ? "Sending Request..." : "Inatuma Ombi kwenye Simu..." }}';
                btn.style.opacity = '0.7';
            }
        }

        function handleVoucherLogin(event) {
            event.preventDefault();
            const input = document.getElementById('voucherCode');
            const code = input.value.trim();
            const btn = document.getElementById('btnVoucherLogin');
            const btnText = document.getElementById('btnVoucherText');
            const feedback = document.getElementById('voucherFeedback');
            const feedbackText = document.getElementById('voucherFeedbackText');
            const feedbackIcon = document.getElementById('voucherFeedbackIcon');

            if (!code) {
                input.focus();
                return;
            }

            // Hide old feedback
            feedback.style.display = 'none';
            input.classList.remove('input-shake');
            input.style.borderColor = 'var(--card-border)';
            btn.disabled = true;
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch('{{ url("portal/check-voucher") }}/' + encodeURIComponent(code))
                .then(res => res.json())
                .then(data => {
                    if (data.valid === false) {
                        btn.disabled = false;
                        btnText.innerText = '{{ $lang == "en" ? "Login" : "Ingia" }}';
                        input.style.borderColor = '#ef4444';
                        input.classList.add('input-shake');
                        input.focus();

                        feedback.style.display = 'flex';
                        feedback.style.background = 'rgba(239, 68, 68, 0.12)';
                        feedback.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                        feedback.style.color = '#fca5a5';
                        feedbackIcon.className = 'fas fa-circle-exclamation';
                        feedbackIcon.style.color = '#ef4444';
                        feedbackText.innerText = data.message;
                    } else {
                        // Valid voucher -> proceed directly to MikroTik via POST Form
                        document.getElementById('manualMtUser').value = code;
                        document.getElementById('manualMtPass').value = code;
                        document.getElementById('manualVoucherLoginForm').submit();
                    }
                })
                .catch(err => {
                    // Fallback directly to MikroTik via POST Form
                    document.getElementById('manualMtUser').value = code;
                    document.getElementById('manualMtPass').value = code;
                    document.getElementById('manualVoucherLoginForm').submit();
                });
        }

        // Auto-scroll to packages if coming back from timeout & Check saved session for randomized MAC
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const savedVoucher = localStorage.getItem('medbando_active_voucher');
                const urlParams = new URLSearchParams(window.location.search);
                const forcePortal = urlParams.get('force_portal');
                const hasError = urlParams.get('error');

                if (savedVoucher && !forcePortal && !hasError) {
                    fetch('{{ route("portal.check_saved_session") }}?voucher=' + encodeURIComponent(savedVoucher) + '&mac={{ $mac }}')
                        .then(r => r.json())
                        .then(res => {
                            if (res.valid && res.username) {
                                const btn = document.getElementById('btnVoucherLogin');
                                const feedback = document.getElementById('voucherFeedback');
                                const feedbackText = document.getElementById('voucherFeedbackText');
                                const feedbackIcon = document.getElementById('voucherFeedbackIcon');

                                if (feedback) {
                                    feedback.style.display = 'flex';
                                    feedback.style.background = 'rgba(16, 185, 129, 0.15)';
                                    feedback.style.border = '1px solid rgba(16, 185, 129, 0.35)';
                                    feedback.style.color = '#6ee7b7';
                                    feedbackIcon.className = 'fas fa-spinner fa-spin';
                                    feedbackIcon.style.color = '#10B981';
                                    feedbackText.innerText = 'Bando lako lipo active! Inakuunganisha kwenye intaneti...';
                                }
                                if (btn) btn.disabled = true;

                                document.getElementById('manualMtUser').value = res.username;
                                document.getElementById('manualMtPass').value = res.password || res.username;
                                setTimeout(() => {
                                    document.getElementById('manualVoucherLoginForm').submit();
                                }, 350);
                            } else {
                                localStorage.removeItem('medbando_active_voucher');
                            }
                        })
                        .catch(() => {});
                }
            } catch(e) {}

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('error') === 'timeout') {
                const pkgSection = document.querySelector('.packages-grid');
                if (pkgSection) {
                    setTimeout(() => {
                        pkgSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 350);
                }
            }
        });
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function(){});
            });
        }
    </script>
</body>
</html>
