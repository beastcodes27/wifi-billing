<!DOCTYPE html>
<html lang="{{ $lang ?? 'sw' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    @php
        $_t = app(\App\Services\TenantContext::class)->getTenant();
        $_b = $_t ? $_t->branding() : ['name' => 'MedBando', 'primary_color' => '#FF3300'];
    @endphp
    <meta name="theme-color" content="{{ $_b['primary_color'] }}">
    <title>Inakuunganisha Moja kwa Moja - {{ strtoupper($_b['name']) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: {{ $_b['primary_color'] }};
            --primary-glow: {{ $_b['primary_color'] }}59;
            --success: #10B981;
            --success-glow: rgba(16, 185, 129, 0.4);
            --bg: #0c0c0e;
            --card-bg: rgba(24, 24, 28, 0.9);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #FFFFFF;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 51, 0, 0.16) 0%, rgba(12, 12, 14, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        .reconnect-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 2.25rem 1.75rem 2rem 1.75rem;
            text-align: center;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(20px);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .radar-box {
            position: relative;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .radar-wave {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid var(--success);
            opacity: 0;
            animation: radarPulse 2.2s cubic-bezier(0.2, 0.8, 0.2, 1) infinite;
        }

        .radar-wave.wave-2 {
            animation-delay: 1.1s;
        }

        .radar-core {
            position: relative;
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10B981, #059669);
            box-shadow: 0 0 35px var(--success-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.9rem;
        }

        @keyframes radarPulse {
            0% { transform: scale(0.7); opacity: 0.85; }
            100% { transform: scale(1.75); opacity: 0; }
        }

        .badge-active {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            margin-bottom: 1rem;
        }

        .badge-active i {
            font-size: 0.65rem;
            color: #10B981;
        }

        h1.title {
            font-size: 1.55rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.4rem;
            line-height: 1.25;
        }

        p.subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            line-height: 1.45;
        }

        .package-info-box {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            margin-bottom: 0.45rem;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .info-val {
            color: #ffffff;
            font-weight: 700;
        }

        .btn-connect {
            width: 100%;
            background: linear-gradient(135deg, #10B981, #059669);
            color: #ffffff;
            border: none;
            cursor: pointer;
            padding: 0.85rem 1.25rem;
            border-radius: 14px;
            font-weight: 800;
            font-size: 0.95rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.2s;
            margin-bottom: 1rem;
        }

        .btn-connect:active {
            transform: scale(0.98);
        }

        .btn-change-plan {
            color: var(--text-muted);
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-change-plan:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="reconnect-card">
    <div class="radar-box">
        <div class="radar-wave"></div>
        <div class="radar-wave wave-2"></div>
        <div class="radar-core">
            <i class="fas fa-wifi"></i>
        </div>
    </div>

    <div class="badge-active">
        <i class="fas fa-circle"></i> {{ $lang == 'en' ? 'PACKAGE ACTIVE' : 'KIFURUSHI KIKO ACTIVE' }}
    </div>

    <h1 class="title">{{ $lang == 'en' ? 'Welcome Back!' : 'Karibu Tena!' }}</h1>
    <p class="subtitle" id="statusMessage">
        <i class="fas fa-spinner fa-spin" style="margin-right: 4px;"></i>
        {{ $lang == 'en' ? 'Reconnecting your internet automatically...' : 'Inakuunganisha kwenye intaneti moja kwa moja...' }}
    </p>

    @php
        $now = now();
        $expiresAt = $activeWifiUser->expires_at;
        $remainingHours = $expiresAt ? max(0, $now->diffInHours($expiresAt, false)) : 0;
        $remainingMins = $expiresAt ? max(0, $now->diffInMinutes($expiresAt, false) % 60) : 0;
        $timeString = $remainingHours > 0 ? "{$remainingHours}h {$remainingMins}m" : "{$remainingMins}m";
    @endphp

    <div class="package-info-box">
        <div class="info-row">
            <span class="info-label"><i class="fas fa-box" style="margin-right: 5px;"></i> {{ $lang == 'en' ? 'Package' : 'Kifurushi' }}</span>
            <span class="info-val">{{ $activeWifiUser->package->name ?? 'WiFi Unlimited' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="fas fa-ticket-alt" style="margin-right: 5px;"></i> {{ $lang == 'en' ? 'Voucher Code' : 'Namba ya Vocha' }}</span>
            <span class="info-val" style="color: #6ee7b7; font-family: monospace; font-size: 0.95rem; letter-spacing: 1px;">{{ $activeWifiUser->mikrotik_username }}</span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="fas fa-clock" style="margin-right: 5px;"></i> {{ $lang == 'en' ? 'Time Remaining' : 'Muda Uliobaki' }}</span>
            <span class="info-val" style="color: #6ee7b7;">{{ $timeString }}</span>
        </div>
    </div>

    <!-- Hidden POST Form to authenticate on MikroTik Hotspot -->
    <form id="reconnectForm" action="{{ $loginUrl }}" method="POST" style="width: 100%;">
        <input type="hidden" name="username" value="{{ $activeWifiUser->mikrotik_username }}">
        <input type="hidden" name="password" value="{{ $activeWifiUser->mikrotik_password ?? $activeWifiUser->mikrotik_username }}">
        <input type="hidden" name="dst" value="{{ $linkOrig ?? 'http://www.google.com' }}">
        <input type="hidden" name="popup" value="true">

        <button type="submit" class="btn-connect" id="btnConnectManual" onclick="handleManualConnect(event)">
            <i class="fas fa-bolt"></i> <span>{{ $lang == 'en' ? 'Connect Now' : 'Unganisha Intaneti Sasa' }}</span>
        </button>
    </form>

    <a href="{!! route('portal.index', array_merge(request()->query(), ['force_portal' => '1'])) !!}" class="btn-change-plan">
        <i class="fas fa-shopping-cart"></i> {{ $lang == 'en' ? 'Buy new package / Change account' : 'Nunua kifurushi kingine / Badilisha' }}
    </a>
</div>

<script>
    const mtUsername = "{{ $activeWifiUser->mikrotik_username }}";
    const mtPassword = "{{ $activeWifiUser->mikrotik_password ?? $activeWifiUser->mikrotik_username }}";
    const loginUrl = "{{ $loginUrl }}";
    const linkOrig = "{{ $linkOrig ?? 'http://www.google.com' }}";

    function triggerDirectLogin() {
        const sep = loginUrl.indexOf('?') !== -1 ? '&' : '?';
        const directUrl = loginUrl + sep + 'username=' + encodeURIComponent(mtUsername) +
            '&password=' + encodeURIComponent(mtPassword) +
            '&dst=' + encodeURIComponent(linkOrig) +
            '&popup=true';
        window.location.href = directUrl;
    }

    function handleManualConnect(event) {
        const form = document.getElementById('reconnectForm');
        const msg = document.getElementById('statusMessage');
        if (msg) {
            msg.innerHTML = '<i class="fas fa-check-circle" style="color: #10B981;"></i> {{ $lang == "en" ? "Restoring access..." : "Inafungua intaneti..." }}';
        }
        try {
            if (form) form.submit();
        } catch(e) {}
        setTimeout(triggerDirectLogin, 1200);
    }

    // Automatic submission in 300ms without user needing to click anything
    document.addEventListener('DOMContentLoaded', function() {
        try {
            if (mtUsername) {
                localStorage.setItem('medbando_active_voucher', mtUsername);
            }
        } catch(e) {}

        setTimeout(function() {
            const form = document.getElementById('reconnectForm');
            const msg = document.getElementById('statusMessage');
            if (msg) {
                msg.innerHTML = '<i class="fas fa-check-circle" style="color: #10B981;"></i> {{ $lang == "en" ? "Restoring access..." : "Inafungua intaneti..." }}';
            }
            try {
                if (form) form.submit();
            } catch(e) {}
            setTimeout(triggerDirectLogin, 1500);
        }, 300);
    });
</script>

</body>
</html>
