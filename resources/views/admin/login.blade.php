<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-title" content="MedBando">
    <meta name="application-name" content="MedBando">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @php
        $tenant = app(\App\Services\TenantContext::class)->getTenant();
        $businessName = ($tenant && !empty($tenant->business_name)) ? strtoupper($tenant->business_name) : 'MEDBANDO';
        $brandInitials = substr($businessName, 0, 2);
    @endphp

    <title>Ingia — {{ $businessName }} Management System</title>

    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-orange: #FF3300;
            --primary-orange-glow: rgba(255, 51, 0, 0.4);
            --bg-dark: #070B14;
            --card-bg: rgba(15, 23, 42, 0.78);
            --card-border: rgba(255, 255, 255, 0.1);
            --text-main: #FFFFFF;
            --text-muted: #94A3B8;
            --input-bg: rgba(255, 255, 255, 0.05);
            --input-border: rgba(255, 255, 255, 0.12);
            --success: #10B981;
            --danger: #EF4444;
            --radius-pill: 50px;
            --radius-card: 24px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glowing Background Orbs */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.6;
        }
        .ambient-orb-1 {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(255, 51, 0, 0.28) 0%, rgba(255, 51, 0, 0) 70%);
            top: -60px;
            left: -60px;
            animation: floatOrb 12s ease-in-out infinite alternate;
        }
        .ambient-orb-2 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, rgba(37, 99, 235, 0) 70%);
            bottom: -80px;
            right: -80px;
            animation: floatOrb 16s ease-in-out infinite alternate-reverse;
        }
        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 30px) scale(1.1); }
        }

        /* Subtle Grid Pattern Overlay */
        .bg-grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 1;
            opacity: 0.65;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 2;
        }

        /* Elevating Glassmorphism Card */
        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-card);
            padding: 2.25rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        /* Subtle Top Border Glow */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #FF3300, #3B82F6, transparent);
            opacity: 0.8;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .brand-logo-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .brand-logo {
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #FF3300 0%, #FF5722 100%);
            border-radius: 18px;
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 10px 24px var(--primary-orange-glow);
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
        }
        .brand-logo-ring {
            position: absolute;
            inset: -4px;
            border-radius: 22px;
            border: 1px dashed rgba(255, 51, 0, 0.4);
            animation: rotateRing 20s linear infinite;
        }
        @keyframes rotateRing {
            to { transform: rotate(360deg); }
        }

        .system-live-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34D399;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-bottom: 0.65rem;
            text-transform: uppercase;
        }
        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 8px #10B981;
            animation: pulseDot 1.8s infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.3); }
        }

        .brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.84rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* Error Notification Banner */
        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #FCA5A5;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-size: 0.84rem;
            font-weight: 600;
            margin-bottom: 1.35rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            animation: shakeAlert 0.4s ease;
        }
        @keyframes shakeAlert {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            font-weight: 700;
            color: #CBD5E1;
            margin-bottom: 0.5rem;
            letter-spacing: 0.2px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 1rem;
            color: #64748B;
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-input {
            width: 100%;
            height: 50px;
            padding: 0 2.75rem 0 2.85rem;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 14px;
            font-size: 16px; /* Prevents auto-zoom on iOS mobile */
            font-family: inherit;
            color: #FFFFFF;
            transition: all 0.25s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: #475569;
            font-size: 0.88rem;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 51, 0, 0.22);
        }

        .form-input:focus + .input-icon-left,
        .input-wrapper:focus-within .input-icon-left {
            color: var(--primary-orange);
        }

        /* Password Eye Toggle Button */
        .password-toggle-btn {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748B;
            font-size: 0.95rem;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .password-toggle-btn:hover {
            color: #CBD5E1;
            background: rgba(255, 255, 255, 0.06);
        }

        /* Remember Me Row */
        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .custom-checkbox-label {
            font-size: 0.82rem;
            color: #94A3B8;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            user-select: none;
        }
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }
        .custom-checkbox:checked {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
        }
        .custom-checkbox:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #FFFFFF;
            font-size: 10px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* High-Impact Submit Button */
        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #FF3300 0%, #EA580C 100%);
            color: #FFFFFF;
            border: none;
            border-radius: var(--radius-pill);
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 20px var(--primary-orange-glow);
            margin-top: 1.35rem;
        }

        .btn-submit:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 12px 28px rgba(255, 51, 0, 0.55);
            background: linear-gradient(135deg, #ff471a 0%, #f97316 100%);
        }

        .btn-submit:active {
            transform: translateY(1px);
            box-shadow: 0 4px 12px rgba(255, 51, 0, 0.4);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Security Badges Footer */
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: rgba(255, 255, 255, 0.04);
            padding: 0.4rem 0.85rem;
            border-radius: 50px;
            font-size: 0.72rem;
            color: #94A3B8;
            font-weight: 600;
            margin-top: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .auth-footer {
            margin-top: 1.65rem;
            text-align: center;
            font-size: 0.76rem;
            color: #64748B;
            line-height: 1.5;
        }
        .support-link {
            color: #CBD5E1;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.15s;
        }
        .support-link:hover {
            color: var(--primary-orange);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem 0.85rem;
            }
            .auth-card {
                padding: 1.85rem 1.35rem;
                border-radius: 20px;
            }
            .brand-title {
                font-size: 1.35rem;
            }
            .brand-logo {
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient Glowing Orbs -->
    <div class="ambient-orb ambient-orb-1"></div>
    <div class="ambient-orb ambient-orb-2"></div>
    <div class="bg-grid-overlay"></div>

    <div class="auth-container">
        <div class="auth-card">
            
            {{-- Brand & Header --}}
            <div class="brand-header">
                <div class="brand-logo-wrap" style="background: #FFFFFF; padding: 12px 24px; border-radius: 20px; box-shadow: 0 12px 28px rgba(0,0,0,0.4); border: 2px solid rgba(255,255,255,0.25); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.15rem;">
                    <img src="{{ asset('images/logo.png') }}" alt="MedBando Logo" style="height: 48px; width: auto; object-fit: contain; display: block;">
                </div>

                <div>
                    <span class="system-live-pill">
                        <span class="pulse-dot"></span> System Online
                    </span>
                </div>

                <h1 class="brand-title">{{ $businessName }}</h1>
                <p class="brand-subtitle">Ingia kusimamia mtandao, vocha na miamala</p>
            </div>

            {{-- Error Message Display --}}
            @if($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle" style="font-size: 1rem; flex-shrink: 0; color: #EF4444;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Login Form --}}
            <form action="{{ route('admin.login.submit') }}" method="POST" id="loginForm">
                @csrf

                {{-- Email Field --}}
                <div class="form-group">
                    <label class="form-label" for="email">
                        <span>Barua Pepe (Email)</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon-left"></i>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="admin@domain.com" required autocomplete="email" autofocus>
                    </div>
                </div>

                {{-- Password Field with Eye Toggle --}}
                <div class="form-group">
                    <label class="form-label" for="password">
                        <span>Nenosiri (Password)</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon-left"></i>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Weka nenosiri lako" required autocomplete="current-password">
                        <button type="button" class="password-toggle-btn" id="passwordToggleBtn" title="Onyesha / Ficha nenosiri" aria-label="Badili kuona nenosiri">
                            <i class="fas fa-eye" id="passwordEyeIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember Me Option --}}
                <div class="remember-row">
                    <label class="custom-checkbox-label" for="rememberMeCheckbox">
                        <input type="checkbox" id="rememberMeCheckbox" name="remember" class="custom-checkbox">
                        <span>Nikumbuke kwenye kifaa hiki</span>
                    </label>
                </div>

                {{-- High-Impact Submit Button --}}
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-arrow-right-to-bracket" id="btnIcon"></i>
                    <span id="btnText">Ingia Kwenye Dashibodi</span>
                </button>

                {{-- Security Badge --}}
                <div style="text-align: center;">
                    <div class="security-badge">
                        <i class="fas fa-shield-halved" style="color: #10B981;"></i>
                        <span>256-Bit SSL • Salama Kabisa</span>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer Support Meta --}}
        <div class="auth-footer">
            <div>&copy; {{ date('Y') }} {{ $businessName }}. Haki zote zimehifadhiwa.</div>
            <div style="margin-top: 0.35rem;">
                Unahitaji msaada? <a href="tel:0762457308" class="support-link"><i class="fas fa-phone-alt" style="font-size: 0.7rem; margin-right: 2px;"></i> 0762457308</a>
            </div>
        </div>
    </div>

    {{-- Interactive Form Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Show/Hide Toggle
            const toggleBtn = document.getElementById('passwordToggleBtn');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('passwordEyeIcon');

            if (toggleBtn && passwordInput && eyeIcon) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                        eyeIcon.style.color = '#FF3300';
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                        eyeIcon.style.color = '#64748B';
                    }
                    passwordInput.focus();
                });
            }

            // Submit Loading State
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnIcon = document.getElementById('btnIcon');
            const btnText = document.getElementById('btnText');

            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    if (btnIcon) {
                        btnIcon.className = 'fas fa-spinner fa-spin';
                    }
                    if (btnText) {
                        btnText.innerText = 'Inaingia...';
                    }
                });
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
