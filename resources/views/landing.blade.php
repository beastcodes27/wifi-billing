<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#F8FAFC">
    <meta name="description" content="{{ __('landing.meta_desc') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <title>{{ __('landing.meta_title') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --brand: #FF3300;
            --brand-2: #EA580C;
            --bg: #F8FAFC;
            --bg-2: #FFFFFF;
            --card: #FFFFFF;
            --border: rgba(2, 6, 23, 0.08);
            --text: #0F172A;
            --muted: #64748B;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }
        h1, h2, h3, h4 { font-family: 'Outfit', sans-serif; line-height: 1.15; letter-spacing: -0.02em; }
        a { color: inherit; text-decoration: none; }
        .wrap { width: 100%; max-width: 1140px; margin: 0 auto; padding: 0 1.25rem; }
        .orb { position: fixed; border-radius: 50%; filter: blur(120px); z-index: 0; pointer-events: none; opacity: .35; }
        .orb-1 { width: 460px; height: 460px; background: radial-gradient(circle, rgba(255,51,0,.18), transparent 70%); top: -140px; left: -120px; }
        .orb-2 { width: 520px; height: 520px; background: radial-gradient(circle, rgba(37,99,235,.16), transparent 70%); top: 320px; right: -180px; }
        .grid-bg { position: fixed; inset: 0; background-image: radial-gradient(rgba(2,6,23,.05) 1px, transparent 1px); background-size: 30px 30px; z-index: 0; pointer-events: none; opacity: .6; }

        /* Nav */
        nav { position: sticky; top: 0; z-index: 50; backdrop-filter: blur(14px); background: rgba(255,255,255,.82); border-bottom: 1px solid var(--border); }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; height: 66px; }
        .logo { display: flex; align-items: center; gap: .6rem; font-family: 'Outfit'; font-weight: 800; font-size: 1.2rem; color: var(--text); }
        .logo-mark { width: 38px; height: 38px; border-radius: 11px; background: linear-gradient(135deg, var(--brand), var(--brand-2)); display: grid; place-items: center; box-shadow: 0 8px 20px rgba(255,51,0,.25); }
        .logo-mark img { height: 22px; }
        .nav-links { display: flex; align-items: center; gap: 1.6rem; }
        .nav-links a { color: var(--muted); font-size: .9rem; font-weight: 600; transition: color .2s; }
        .nav-links a:hover { color: var(--text); }
        .nav-cta { display: flex; align-items: center; gap: .75rem; }
        .btn { display: inline-flex; align-items: center; gap: .5rem; font-family: 'Outfit'; font-weight: 700; border-radius: 50px; padding: .7rem 1.4rem; font-size: .9rem; transition: all .2s; cursor: pointer; border: none; }
        .btn-primary { background: linear-gradient(135deg, var(--brand), var(--brand-2)); color: #fff; box-shadow: 0 8px 22px rgba(255,51,0,.28); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(255,51,0,.4); }
        .btn-ghost { background: #FFFFFF; color: var(--text); border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(2,6,23,.04); }
        .btn-ghost:hover { background: #F1F5F9; }
        .menu-toggle { display: none; background: none; border: none; color: var(--text); font-size: 1.4rem; }

        section { position: relative; z-index: 2; }
        .hero { padding: 5rem 0 4rem; text-align: center; }
        .pill { display: inline-flex; align-items: center; gap: .5rem; background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25); color: #059669; padding: .35rem 1rem; border-radius: 50px; font-size: .78rem; font-weight: 700; letter-spacing: .3px; text-transform: uppercase; margin-bottom: 1.5rem; }
        .dot { width: 7px; height: 7px; border-radius: 50%; background: #10B981; box-shadow: 0 0 10px #10B981; animation: pulse 1.8s infinite; }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .35; transform: scale(1.3); } }
        .hero h1 { font-size: clamp(2.2rem, 6vw, 3.9rem); font-weight: 900; margin-bottom: 1.25rem; }
        .hero h1 span { background: linear-gradient(135deg, var(--brand), #F97316); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--muted); font-size: clamp(1rem, 2.2vw, 1.2rem); max-width: 680px; margin: 0 auto 2rem; }
        .hero-cta { display: flex; gap: .85rem; justify-content: center; flex-wrap: wrap; }
        .hero-stats { display: flex; justify-content: center; gap: 2.5rem; flex-wrap: wrap; margin-top: 3rem; }
        .hero-stats .stat b { display: block; font-family: 'Outfit'; font-size: 1.6rem; font-weight: 800; color: var(--text); }
        .hero-stats .stat span { color: var(--muted); font-size: .82rem; }

        .section-head { text-align: center; max-width: 640px; margin: 0 auto 3rem; }
        .section-head h2 { font-size: clamp(1.7rem, 4vw, 2.5rem); font-weight: 800; margin-bottom: .75rem; }
        .section-head p { color: var(--muted); }

        .features { padding: 4rem 0; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; }
        .feature { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 1.6rem; transition: transform .25s, border-color .25s, box-shadow .25s; box-shadow: 0 1px 3px rgba(2,6,23,.04); }
        .feature:hover { transform: translateY(-4px); border-color: rgba(255,51,0,.4); box-shadow: 0 14px 30px rgba(2,6,23,.08); }
        .feature .ico { width: 48px; height: 48px; border-radius: 13px; background: rgba(255,51,0,.13); color: var(--brand); display: grid; place-items: center; font-size: 1.25rem; margin-bottom: 1rem; }
        .feature h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: .5rem; }
        .feature p { color: var(--muted); font-size: .9rem; }

        .steps { padding: 4rem 0; }
        .step-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; }
        .step { text-align: center; padding: 1.5rem; }
        .step .num { width: 54px; height: 54px; margin: 0 auto 1rem; border-radius: 50%; background: linear-gradient(135deg, var(--brand), var(--brand-2)); display: grid; place-items: center; font-family: 'Outfit'; font-weight: 800; font-size: 1.3rem; box-shadow: 0 8px 20px rgba(255,51,0,.35); }
        .step h3 { font-size: 1.05rem; margin-bottom: .5rem; }
        .step p { color: var(--muted); font-size: .9rem; }

        .pricing { padding: 4rem 0; }
        .price-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; align-items: start; }
        .price-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; display: flex; flex-direction: column; box-shadow: 0 1px 3px rgba(2,6,23,.05); }
        .price-card.featured { border-color: var(--brand); box-shadow: 0 0 0 1px var(--brand), 0 20px 50px rgba(255,51,0,.12); position: relative; }
        .price-card.featured::before { content: 'Inapendekezwa'; position: absolute; top: -12px; right: 20px; background: linear-gradient(135deg, var(--brand), var(--brand-2)); color: #fff; font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; padding: .25rem .85rem; border-radius: 50px; }
        .price-card h3 { font-size: 1.2rem; margin-bottom: .35rem; }
        .price-card .desc { color: var(--muted); font-size: .84rem; margin-bottom: 1rem; min-height: 2.5rem; }
        .price-card .amount { font-family: 'Outfit'; font-size: 2.1rem; font-weight: 800; }
        .price-card .amount small { font-size: .85rem; color: var(--muted); font-weight: 600; }
        .price-card ul { list-style: none; margin: 1.25rem 0; display: grid; gap: .7rem; }
        .price-card li { display: flex; gap: .6rem; font-size: .88rem; align-items: flex-start; }
        .price-card li i { color: #10B981; margin-top: .2rem; }
        .price-card .btn { width: 100%; justify-content: center; margin-top: auto; }
        .trial-note { text-align: center; color: var(--muted); font-size: .85rem; margin-top: 1.5rem; }
        .trial-note strong { color: #059669; }

        .cta-band { margin: 4rem 0; background: linear-gradient(135deg, rgba(255,51,0,.08), rgba(37,99,235,.08)); border: 1px solid var(--border); border-radius: 24px; padding: 3rem 2rem; text-align: center; }
        .cta-band h2 { font-size: clamp(1.6rem, 4vw, 2.3rem); margin-bottom: 1rem; }
        .cta-band p { color: var(--muted); margin-bottom: 1.75rem; max-width: 560px; margin-left: auto; margin-right: auto; }

        footer { border-top: 1px solid var(--border); padding: 2.5rem 0; text-align: center; color: var(--muted); font-size: .85rem; position: relative; z-index: 2; }
        footer a { color: #334155; font-weight: 600; }
        footer a:hover { color: var(--brand); }

        @media (max-width: 820px) {
            .nav-links { display: none; }
            .menu-toggle { display: block; }
            .nav-links.open { display: flex; position: absolute; top: 66px; left: 0; right: 0; flex-direction: column; background: rgba(255,255,255,.99); padding: 1.25rem; gap: 1rem; border-bottom: 1px solid var(--border); box-shadow: 0 12px 24px rgba(2,6,23,.08); }
            .hero { padding: 3.25rem 0 2.5rem; }
            .hero-stats { gap: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="grid-bg"></div>

    <nav>
        <div class="wrap nav-inner">
            <a href="{{ route('landing') }}" class="logo">
                <span class="logo-mark"><img src="{{ asset('images/logo.png') }}" alt="MedBando"></span>
                MedBando
            </a>
            <div class="nav-links" id="navLinks">
                <a href="#features">{{ __('landing.nav_features') }}</a>
                <a href="#how">{{ __('landing.nav_how') }}</a>
                <a href="#pricing">{{ __('landing.nav_pricing') }}</a>
                <a href="{{ route('owner.login') }}">{{ __('landing.nav_login') }}</a>
                @include('partials.lang-switch')
            </div>
            <div class="nav-cta">
                <a href="{{ route('owner.register') }}" class="btn btn-primary" id="navCta">{{ __('landing.nav_start') }}</a>
                <button class="menu-toggle" id="menuToggle" aria-label="Menu"><i class="fas fa-bars"></i></button>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="wrap">
            <span class="pill"><span class="dot"></span> {{ __('landing.hero_pill') }}</span>
            <h1>{{ __('landing.hero_title_a') }} <span>{{ __('landing.hero_title_b') }}</span> {{ __('landing.hero_title_c') }}</h1>
            <p>{{ __('landing.hero_sub') }}</p>
            <div class="hero-cta">
                <a href="{{ route('owner.register') }}" class="btn btn-primary"><i class="fas fa-rocket"></i> {{ __('landing.hero_cta_primary') }}</a>
                <a href="#how" class="btn btn-ghost"><i class="fas fa-circle-play"></i> {{ __('landing.hero_cta_secondary') }}</a>
            </div>
            <div class="hero-stats">
                <div class="stat"><b>{{ __('landing.stat_mikrotik') }}</b><span>{{ __('landing.stat_mikrotik_sub') }}</span></div>
                <div class="stat"><b>{{ __('landing.stat_providers') }}</b><span>{{ __('landing.stat_providers_sub') }}</span></div>
                <div class="stat"><b>{{ __('landing.stat_anywhere') }}</b><span>{{ __('landing.stat_anywhere_sub') }}</span></div>
                <div class="stat"><b>{{ __('landing.stat_trial') }}</b><span>{{ __('landing.stat_trial_sub') }}</span></div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="wrap">
            <div class="section-head">
                <h2>{{ __('landing.features_title') }}</h2>
                <p>{{ __('landing.features_sub') }}</p>
            </div>
            <div class="feature-grid">
                <div class="feature"><div class="ico"><i class="fas fa-tags"></i></div><h3>{{ __('landing.f1_t') }}</h3><p>{{ __('landing.f1_d') }}</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-mobile-screen-button"></i></div><h3>{{ __('landing.f2_t') }}</h3><p>{{ __('landing.f2_d') }}</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-ticket"></i></div><h3>{{ __('landing.f3_t') }}</h3><p>{{ __('landing.f3_d') }}</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-network-wired"></i></div><h3>{{ __('landing.f4_t') }}</h3><p>{{ __('landing.f4_d') }}</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-location-dot"></i></div><h3>{{ __('landing.f5_t') }}</h3><p>{{ __('landing.f5_d') }}</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-chart-line"></i></div><h3>{{ __('landing.f6_t') }}</h3><p>{{ __('landing.f6_d') }}</p></div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="steps" id="how">
        <div class="wrap">
            <div class="section-head">
                <h2>{{ __('landing.how_title') }}</h2>
                <p>{{ __('landing.how_sub') }}</p>
            </div>
            <div class="step-grid">
                <div class="step"><div class="num">1</div><h3>{{ __('landing.s1_t') }}</h3><p>{{ __('landing.s1_d') }}</p></div>
                <div class="step"><div class="num">2</div><h3>{{ __('landing.s2_t') }}</h3><p>{{ __('landing.s2_d') }}</p></div>
                <div class="step"><div class="num">3</div><h3>{{ __('landing.s3_t') }}</h3><p>{{ __('landing.s3_d') }}</p></div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section class="pricing" id="pricing">
        <div class="wrap">
            <div class="section-head">
                <h2>{{ __('landing.pricing_title') }}</h2>
                <p>{{ __('landing.pricing_sub') }}</p>
            </div>
            <div class="price-grid">
                @forelse($plans as $i => $plan)
                <div class="price-card {{ $i === 1 ? 'featured' : '' }}">
                    <h3>{{ $plan->name }}</h3>
                    <div class="desc">{{ $plan->description ?: __('landing.plan_business_desc') }}</div>
                    <div class="amount">TSh {{ number_format($plan->price) }} <small>/ {{ $plan->billing_cycle_months > 1 ? __('landing.plan_months', ['n' => $plan->billing_cycle_months]) : __('landing.plan_per_month') }}</small></div>
                    <ul>
                        <li><i class="fas fa-check-circle"></i><span>{!! __('landing.plan_routers', ['n' => '<strong>'.number_format($plan->max_routers).'</strong>']) !!}</span></li>
                        <li><i class="fas fa-check-circle"></i><span>{!! __('landing.plan_customers', ['n' => '<strong>'.number_format($plan->max_customers).'</strong>']) !!}</span></li>
                        <li><i class="fas fa-check-circle"></i><span>{{ __('landing.plan_features') }}</span></li>
                        <li><i class="fas fa-check-circle"></i><span>{{ __('landing.plan_payments') }}</span></li>
                    </ul>
                    <a href="{{ route('owner.register', ['plan' => $plan->id]) }}" class="btn {{ $i === 1 ? 'btn-primary' : 'btn-ghost' }}">{{ __('landing.plan_choose') }}</a>
                </div>
                @empty
                <div class="price-card featured" style="grid-column: 1 / -1;">
                    <h3>{{ __('landing.s1_t') }}</h3>
                    <div class="desc">{{ __('landing.cta_sub') }}</div>
                    <div class="amount">{{ __('landing.plan_free') }} <small>/ {{ __('landing.plan_first_month') }}</small></div>
                    <ul>
                        <li><i class="fas fa-check-circle"></i><span>{{ __('landing.plan_features') }}</span></li>
                        <li><i class="fas fa-check-circle"></i><span>{{ __('landing.plan_payments') }}</span></li>
                    </ul>
                    <a href="{{ route('owner.register') }}" class="btn btn-primary">{{ __('landing.nav_start') }}</a>
                </div>
                @endforelse
            </div>
            <p class="trial-note"><i class="fas fa-gift"></i> {{ __('landing.trial_note') }}</p>
        </div>
    </section>

    <!-- CTA -->
    <section class="wrap">
        <div class="cta-band">
            <h2>{{ __('landing.cta_title') }}</h2>
            <p>{{ __('landing.cta_sub') }}</p>
            <a href="{{ route('owner.register') }}" class="btn btn-primary"><i class="fas fa-arrow-right"></i> {{ __('landing.cta_btn') }}</a>
        </div>
    </section>

    <footer>
        <div class="wrap">
            <div>&copy; {{ date('Y') }} MedBando. {{ __('landing.footer_rights') }}</div>
            <div style="margin-top:.6rem;">
                <a href="{{ route('owner.login') }}">{{ __('landing.footer_login') }}</a> &nbsp;·&nbsp;
                <a href="{{ route('owner.register') }}">{{ __('landing.footer_register') }}</a>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('menuToggle')?.addEventListener('click', function () {
            document.getElementById('navLinks').classList.toggle('open');
        });
    </script>
</body>
</html>
