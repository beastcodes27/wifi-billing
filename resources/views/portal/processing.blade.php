<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    @php
        $_t = app(\App\Services\TenantContext::class)->getTenant();
        $_b = $_t ? $_t->branding() : ['name' => 'MedBando', 'primary_color' => '#FF3300'];
    @endphp
    <meta name="theme-color" content="{{ $_b['primary_color'] }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ strtoupper($_b['name']) }}">
    <meta name="application-name" content="{{ strtoupper($_b['name']) }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ !empty($_b['logo']) ? asset($_b['logo']) : asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <title>Kuthibitisha Malipo - {{ strtoupper($_b['name']) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: {{ $_b['primary_color'] }};
            --primary-glow: {{ $_b['primary_color'] }}59;
            --success: #10B981;
            --success-glow: rgba(16, 185, 129, 0.4);
            --bg: #0c0c0e;
            --card-bg: rgba(24, 24, 28, 0.85);
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

        /* Ambient background glow */
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 51, 0, 0.18) 0%, rgba(12, 12, 14, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        .processing-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 2.5rem 1.75rem 2rem 1.75rem;
            text-align: center;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(20px);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Hero Radar Animation */
        .radar-box {
            position: relative;
            width: 110px;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.75rem;
        }

        .radar-wave {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid var(--primary);
            opacity: 0;
            animation: radarPulse 2.4s cubic-bezier(0.2, 0.8, 0.2, 1) infinite;
        }

        .radar-wave.wave-2 {
            animation-delay: 1.2s;
        }

        .radar-core {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF451A, #E02800);
            box-shadow: 0 0 35px var(--primary-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 2rem;
            transition: all 0.4s ease;
        }

        .radar-core.success {
            background: linear-gradient(135deg, #10B981, #059669);
            box-shadow: 0 0 40px var(--success-glow);
            transform: scale(1.05);
        }

        @keyframes radarPulse {
            0% {
                transform: scale(0.7);
                opacity: 0.8;
            }
            100% {
                transform: scale(1.75);
                opacity: 0;
            }
        }

        /* Package & Price Tag */
        .pkg-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.35rem 0.9rem;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #f1f5f9;
            margin-bottom: 1.2rem;
        }

        .pkg-tag .highlight {
            color: var(--primary);
        }

        /* Typography */
        h1.title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.4rem;
            line-height: 1.2;
            letter-spacing: -0.3px;
        }

        /* Phone Display Chip */
        .phone-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--text-main);
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: 0.6px;
            margin-bottom: 0.6rem;
            background: rgba(255, 51, 0, 0.08);
            border: 1px solid rgba(255, 51, 0, 0.2);
            padding: 0.35rem 0.95rem;
            border-radius: 12px;
        }

        .phone-chip i {
            color: var(--primary);
            font-size: 0.95rem;
        }

        p.prompt {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.45;
            margin-bottom: 1.5rem;
            max-width: 320px;
        }

        /* Live Pulsing Status Pill */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.5rem 1.1rem;
            border-radius: 30px;
            font-size: 0.86rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 1.75rem;
            transition: all 0.3s;
        }

        .status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #f59e0b;
            box-shadow: 0 0 10px #f59e0b;
            animation: dotBlink 1.4s infinite ease-in-out;
        }

        .status-pill.success .status-dot {
            background: var(--success);
            box-shadow: 0 0 12px var(--success);
        }

        .status-pill.success {
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        @keyframes dotBlink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(0.85); }
        }

        /* Micro Hint */
        .hint-bar {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .hint-bar i {
            color: var(--primary);
            font-size: 0.85rem;
        }

        /* Cancel Button */
        .btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #94a3b8;
            padding: 0.55rem 1.25rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-cancel:hover {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>

@php
    $rawPhoneVal = $phone ?? $phoneNumber ?? '';
    $cleanPhone = preg_replace('/^255/', '0', $rawPhoneVal);
    if (strlen($cleanPhone) === 10) {
        $displayPhone = substr($cleanPhone, 0, 4) . ' ' . substr($cleanPhone, 4, 3) . ' ' . substr($cleanPhone, 7);
    } else {
        $displayPhone = $cleanPhone ?: 'Simu ya Mteja';
    }
@endphp

<div class="processing-card">
    <!-- Visual Radar Hero -->
    <div class="radar-box">
        <div class="radar-wave wave-1" id="radarWave1"></div>
        <div class="radar-wave wave-2" id="radarWave2"></div>
        <div class="radar-core" id="radarCore">
            <i class="fas fa-lock" id="radarIcon"></i>
        </div>
    </div>

    <!-- Package Tag -->
    @if(isset($package) && $package)
        <div class="pkg-tag">
            <span>{{ $package->name }}</span>
            <span class="highlight">•</span>
            <span class="highlight">TZS {{ number_format($amount ?? $package->price, 0) }}</span>
        </div>
    @elseif(isset($amount) && $amount)
        <div class="pkg-tag">
            <span>Vocha WiFi</span>
            <span class="highlight">•</span>
            <span class="highlight">TZS {{ number_format($amount, 0) }}</span>
        </div>
    @endif

    <!-- Main Title -->
    <h1 class="title" id="mainTitle">Weka PIN ya Simu</h1>

    <!-- Phone Badge -->
    <div class="phone-chip">
        <i class="fas fa-mobile-alt"></i>
        <span>{{ $displayPhone }}</span>
    </div>

    <!-- Clear, Minimalist Instruction -->
    <p class="prompt" id="promptText">
        Tafadhali weka namba yako ya siri kukamilisha malipo.
    </p>

    <!-- Live Status Indicator with 1-Minute Countdown -->
    <div class="status-pill" id="statusPill">
        <span class="status-dot" id="statusDot"></span>
        <span id="statusLabel">Inasubiri PIN...</span>
        <span id="countdownTimer" style="background: rgba(255,255,255,0.1); padding: 0.15rem 0.55rem; border-radius: 10px; font-size: 0.78rem; font-weight: 700; margin-left: 0.35rem; color: #f59e0b;">60s</span>
    </div>

    <!-- Slim Progress Bar for 3 Minute Window -->
    <div class="timer-bar-wrap" style="width: 100%; max-width: 240px; height: 3.5px; background: rgba(255,255,255,0.08); border-radius: 4px; overflow: hidden; margin-top: -0.9rem; margin-bottom: 1.5rem;">
        <div id="timerProgress" style="width: 100%; height: 100%; background: linear-gradient(90deg, var(--primary), #f59e0b); border-radius: 4px; transition: width 1s linear;"></div>
    </div>

    <!-- Subdued Hint -->
    <!-- Subdued Hint -->
    <div class="hint-bar" id="hintBar">
        <i class="fas fa-bolt"></i>
        <span>Utaunganishwa moja kwa moja ukishalipa</span>
    </div>

    <!-- Direct Connecting State -->
    <div id="connectingBox" style="display: none; width: 100%; margin: 0.75rem 0 1.25rem 0;">
        <div style="background: rgba(16, 185, 129, 0.12); border: 1.5px solid rgba(16, 185, 129, 0.35); border-radius: 20px; padding: 1.5rem 1.25rem; text-align: center; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.15);">
            <div style="width: 58px; height: 58px; border-radius: 50%; background: rgba(16, 185, 129, 0.2); border: 2px solid #10B981; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.9rem auto;">
                <i class="fas fa-wifi" style="font-size: 1.6rem; color: #10B981;"></i>
            </div>
            <div style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin-bottom: 0.35rem;" id="successHeading">
                Malipo Yamekamilika!
            </div>

            <!-- Prominent Voucher Display Box -->
            <div id="voucherCard" style="background: rgba(0,0,0,0.4); border: 1.5px dashed rgba(16, 185, 129, 0.5); border-radius: 16px; padding: 0.85rem 1rem; margin: 0.85rem 0; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                <div style="text-align: left;">
                    <span style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; display: block;">Vocha Yako ya WiFi:</span>
                    <strong id="displayVoucher" style="font-size: 1.5rem; letter-spacing: 2px; color: #10B981; font-weight: 900;">------</strong>
                </div>
                <button type="button" onclick="copyVoucher()" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #6ee7b7; border-radius: 10px; padding: 0.5rem 0.85rem; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-copy"></i> <span id="copyVoucherText">Nakili</span>
                </button>
            </div>

            <p id="connectingText" style="font-size: 0.88rem; color: #a7f3d0; margin-bottom: 1.1rem; line-height: 1.45;">
                <i class="fas fa-spinner fa-spin" style="margin-right: 5px;"></i> Tunaandaa intaneti yako kwenye router, tafadhali subiri kidogo...
            </p>
            <button type="button" id="connectNowBtn" onclick="submitMikrotikLogin()" style="display: none; align-items: center; justify-content: center; gap: 8px; width: 100%; background: linear-gradient(135deg, #10B981, #059669); color: #ffffff; border: none; cursor: pointer; padding: 0.85rem 1.25rem; border-radius: 14px; font-weight: 800; font-size: 0.95rem; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: transform 0.2s;">
                <i class="fas fa-wifi"></i> <span>Unganisha Intaneti Sasa</span>
            </button>
        </div>
    </div>

    <!-- Hidden POST Form to authenticate on MikroTik Hotspot (link-login-only / login) -->
    <form id="mikrotikLoginForm" action="{{ $loginUrl }}" method="POST" style="display: none;">
        <input type="hidden" name="username" id="mtUsername" value="">
        <input type="hidden" name="password" id="mtPassword" value="">
        <input type="hidden" name="dst" value="{{ $linkOrig ?? 'http://www.google.com' }}">
        <input type="hidden" name="popup" value="true">
    </form>

    <!-- Cancel Action -->
    <a href="{!! route('portal.index', ['mac' => $mac ?? '', 'ip' => $ip ?? '', 'link-login' => $loginUrl ?? '', 'link-orig' => $linkOrig ?? '']) !!}" class="btn-cancel" id="btnCancel">
        <i class="fas fa-arrow-left"></i> Rudi Nyuma
    </a>
</div>

<script>
    let currentVoucherCode = '';

    function copyVoucher() {
        if (!currentVoucherCode) return;
        navigator.clipboard.writeText(currentVoucherCode).then(() => {
            const txt = document.getElementById('copyVoucherText');
            if (txt) {
                txt.innerText = 'Imenakiliwa!';
                setTimeout(() => { txt.innerText = 'Nakili'; }, 2000);
            }
        }).catch(() => {});
    }

    function submitMikrotikLogin() {
        const form = document.getElementById('mikrotikLoginForm');
        const connectingText = document.getElementById('connectingText');
        if (connectingText) {
            connectingText.innerHTML = '<i class="fas fa-check-circle" style="color: #10B981;"></i> <span>Inafungua intaneti...</span>';
        }

        // 1. Submit POST form directly to MikroTik Hotspot
        let postSuccess = false;
        try {
            if (form) {
                form.submit();
                postSuccess = true;
            }
        } catch(e) {
            console.error('Form submit error:', e);
        }

        // 2. Direct GET URL fallback if browser blocks cross-origin POST or captive portal webview fails
        if (currentVoucherCode) {
            setTimeout(function() {
                const baseLogin = "{{ $loginUrl }}";
                const sep = baseLogin.indexOf('?') !== -1 ? '&' : '?';
                const directUrl = baseLogin + sep + 'username=' + encodeURIComponent(currentVoucherCode) +
                    '&password=' + encodeURIComponent(currentVoucherCode) +
                    '&dst=' + encodeURIComponent('{{ $linkOrig ?? "http://www.google.com" }}') +
                    '&popup=true';
                window.location.href = directUrl;
            }, 1200);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        let isRedirecting = false;
        let paymentVerified = false;
        let provisionWaitCount = 0;
        let autoLoginAttempted = false;
        let timeLeft = 60; // 1-minute countdown for payment entry

        const countdownEl = document.getElementById('countdownTimer');
        const timerProgress = document.getElementById('timerProgress');

        // Clean local storage
        try {
            localStorage.removeItem('last_voucher');
            localStorage.removeItem('last_voucher_time');
        } catch(e) {}

        const triggerTimeoutCheck = () => {
            clearInterval(timerInterval);
            // If payment was already verified, NEVER timeout back to the package list!
            if (paymentVerified) {
                return;
            }

            const mainTitle = document.getElementById('mainTitle');
            const promptText = document.getElementById('promptText');
            const label = document.getElementById('statusLabel');

            if (mainTitle) mainTitle.innerText = 'Inakagua Malipo...';
            if (promptText) promptText.innerText = 'Tunakagua kama muamala wako umekamilika, tafadhali subiri...';
            if (label) label.innerText = 'Inakagua';

            // Check server one last time before declaring timeout
            fetch("{{ url('portal/check-status/' . $transactionRef) }}")
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success' && data.voucher) {
                        handlePaymentStatus(data);
                    } else {
                        isRedirecting = true;
                        if (mainTitle) mainTitle.innerText = 'Muda Umekwisha';
                        if (promptText) promptText.innerText = 'Muda wa malipo umekwisha. Tunakurudisha nyuma...';
                        setTimeout(() => {
                            window.location.href = "{!! route('portal.index', ['error' => 'timeout', 'mac' => $mac ?? '', 'ip' => $ip ?? '', 'link-login' => $loginUrl ?? '', 'link-orig' => $linkOrig ?? '']) !!}";
                        }, 600);
                    }
                })
                .catch(() => {
                    isRedirecting = true;
                    window.location.href = "{!! route('portal.index', ['error' => 'timeout', 'mac' => $mac ?? '', 'ip' => $ip ?? '', 'link-login' => $loginUrl ?? '', 'link-orig' => $linkOrig ?? '']) !!}";
                });
        };

        // 1-minute countdown timer for PIN entry
        const timerInterval = setInterval(() => {
            if (isRedirecting || paymentVerified) {
                clearInterval(timerInterval);
                return;
            }

            timeLeft--;
            if (countdownEl) {
                countdownEl.innerText = timeLeft + 's';
            }
            if (timerProgress) {
                timerProgress.style.width = Math.max(0, (timeLeft / 60) * 100) + '%';
            }

            if (timeLeft <= 0) {
                triggerTimeoutCheck();
            }
        }, 1000);

        function handlePaymentStatus(data) {
            paymentVerified = true;
            clearInterval(timerInterval);

            // Populate current voucher code
            currentVoucherCode = data.voucher;
            const displayVoucher = document.getElementById('displayVoucher');
            if (displayVoucher && data.voucher) {
                displayVoucher.innerText = data.voucher;
            }

            // Populate hidden form credentials with voucher code
            const mtUser = document.getElementById('mtUsername');
            const mtPass = document.getElementById('mtPassword');
            if (mtUser) mtUser.value = data.voucher;
            if (mtPass) mtPass.value = data.voucher;

            // Persist voucher & phone to device localStorage for seamless auto-reconnect
            try {
                if (data.voucher) {
                    localStorage.setItem('medbando_active_voucher', data.voucher);
                    localStorage.setItem('medbando_active_phone', '{{ $phone ?? $phoneNumber ?? "" }}');
                }
            } catch(e) {}

            // UI Elements
            const core = document.getElementById('radarCore');
            const icon = document.getElementById('radarIcon');
            const wave1 = document.getElementById('radarWave1');
            const wave2 = document.getElementById('radarWave2');
            const mainTitle = document.getElementById('mainTitle');
            const promptText = document.getElementById('promptText');
            const pill = document.getElementById('statusPill');
            const label = document.getElementById('statusLabel');
            const hintBar = document.getElementById('hintBar');
            const btnCancel = document.getElementById('btnCancel');
            const phoneChip = document.querySelector('.phone-chip');
            const timerBarWrap = document.querySelector('.timer-bar-wrap');
            const connectingBox = document.getElementById('connectingBox');
            const connectingText = document.getElementById('connectingText');
            const connectNowBtn = document.getElementById('connectNowBtn');

            // Success visuals
            if (core) { core.classList.add('success'); }
            if (icon) { icon.className = 'fas fa-wifi'; }
            if (wave1) { wave1.style.borderColor = 'var(--success)'; }
            if (wave2) { wave2.style.borderColor = 'var(--success)'; }
            if (countdownEl) { countdownEl.style.display = 'none'; }
            if (timerBarWrap) { timerBarWrap.style.display = 'none'; }
            if (hintBar) { hintBar.style.display = 'none'; }
            if (btnCancel) { btnCancel.style.display = 'none'; }
            if (phoneChip) { phoneChip.style.display = 'none'; }
            if (promptText) { promptText.style.display = 'none'; }
            if (connectingBox) { connectingBox.style.display = 'block'; }

            // PHASE 5: Check if MikroTik Router has confirmed provisioning
            if (data.is_provisioned) {
                // ROUTER HAS CONFIRMED PROVISIONING -> PERFORM HOTSPOT LOGIN!
                if (pill) { pill.classList.add('success'); }
                if (label) { label.innerText = 'Uko Tayari!'; }
                if (mainTitle) { mainTitle.innerText = 'Uko Tayari!'; }
                if (connectingText) {
                    connectingText.innerHTML = '<i class="fas fa-check-circle" style="color: #10B981;"></i> <span>Umesajiliwa kikamilifu! Inakuunganisha kwenye intaneti...</span>';
                }
                if (connectNowBtn) { connectNowBtn.style.display = 'flex'; }

                if (!autoLoginAttempted) {
                    autoLoginAttempted = true;
                    setTimeout(submitMikrotikLogin, 500);
                }
            } else {
                // PAYMENT IS COMPLETE, WAITING FOR MIKROTIK SYNC SCHEDULER
                provisionWaitCount++;
                if (label) { label.innerText = 'Inaandaa Router...'; }
                if (mainTitle) { mainTitle.innerText = 'Malipo Yamethibitishwa!'; }
                if (connectingText) {
                    connectingText.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 5px;"></i> Tunaandaa intaneti yako kwenye router, tafadhali subiri kidogo...';
                }

                // If router sync takes longer than 20 seconds, expose connect button as fallback
                if (provisionWaitCount >= 10 && connectNowBtn) {
                    connectNowBtn.style.display = 'flex';
                    if (connectingText) {
                        connectingText.innerHTML = 'Vocha yako iko tayari. Bonyeza kitufe hapa chini kujiunga moja kwa moja:';
                    }
                }
            }
        }

        const checkStatus = () => {
            if (isRedirecting) return;

            fetch("{{ url('portal/check-status/' . $transactionRef) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.voucher) {
                        handlePaymentStatus(data);
                    } else if (!paymentVerified && (data.status === 'failed' || data.status === 'cancelled')) {
                        isRedirecting = true;
                        clearInterval(timerInterval);
                        window.location.href = "{!! route('portal.index', ['mac' => $mac ?? '', 'ip' => $ip ?? '', 'link-login' => $loginUrl ?? '', 'link-orig' => $linkOrig ?? '']) !!}";
                    }
                })
                .catch(error => console.error('Status check error:', error));
        };

        // Poll status every 1.5 seconds for instant detection
        setInterval(checkStatus, 1500);
    });
</script>
</body>
</html>
