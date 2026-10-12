<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#070B14">
    <meta name="description" content="MedBando — mfumo wa kisasa wa kuendesha biashara ya WiFi. Vocha, malipo ya mobile money, na ripoti zote mahali pamoja.">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <title>MedBando — Endesha Biashara Yako ya WiFi kwa Urahisi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --brand: #FF3300;
            --brand-2: #EA580C;
            --bg: #070B14;
            --bg-2: #0B1120;
            --card: rgba(15, 23, 42, 0.7);
            --border: rgba(255, 255, 255, 0.09);
            --text: #F8FAFC;
            --muted: #94A3B8;
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
        .orb { position: fixed; border-radius: 50%; filter: blur(120px); z-index: 0; pointer-events: none; opacity: .5; }
        .orb-1 { width: 460px; height: 460px; background: radial-gradient(circle, rgba(255,51,0,.32), transparent 70%); top: -140px; left: -120px; }
        .orb-2 { width: 520px; height: 520px; background: radial-gradient(circle, rgba(37,99,235,.28), transparent 70%); top: 320px; right: -180px; }
        .grid-bg { position: fixed; inset: 0; background-image: radial-gradient(rgba(255,255,255,.06) 1px, transparent 1px); background-size: 30px 30px; z-index: 0; pointer-events: none; opacity: .5; }

        /* Nav */
        nav { position: sticky; top: 0; z-index: 50; backdrop-filter: blur(14px); background: rgba(7,11,20,.72); border-bottom: 1px solid var(--border); }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; height: 66px; }
        .logo { display: flex; align-items: center; gap: .6rem; font-family: 'Outfit'; font-weight: 800; font-size: 1.2rem; }
        .logo-mark { width: 38px; height: 38px; border-radius: 11px; background: linear-gradient(135deg, var(--brand), var(--brand-2)); display: grid; place-items: center; box-shadow: 0 8px 20px rgba(255,51,0,.4); }
        .logo-mark img { height: 22px; }
        .nav-links { display: flex; align-items: center; gap: 1.6rem; }
        .nav-links a { color: var(--muted); font-size: .9rem; font-weight: 600; transition: color .2s; }
        .nav-links a:hover { color: #fff; }
        .nav-cta { display: flex; align-items: center; gap: .75rem; }
        .btn { display: inline-flex; align-items: center; gap: .5rem; font-family: 'Outfit'; font-weight: 700; border-radius: 50px; padding: .7rem 1.4rem; font-size: .9rem; transition: all .2s; cursor: pointer; border: none; }
        .btn-primary { background: linear-gradient(135deg, var(--brand), var(--brand-2)); color: #fff; box-shadow: 0 8px 22px rgba(255,51,0,.38); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(255,51,0,.5); }
        .btn-ghost { background: rgba(255,255,255,.06); color: #fff; border: 1px solid var(--border); }
        .btn-ghost:hover { background: rgba(255,255,255,.12); }
        .menu-toggle { display: none; background: none; border: none; color: #fff; font-size: 1.4rem; }

        section { position: relative; z-index: 2; }
        .hero { padding: 5rem 0 4rem; text-align: center; }
        .pill { display: inline-flex; align-items: center; gap: .5rem; background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3); color: #34D399; padding: .35rem 1rem; border-radius: 50px; font-size: .78rem; font-weight: 700; letter-spacing: .3px; text-transform: uppercase; margin-bottom: 1.5rem; }
        .dot { width: 7px; height: 7px; border-radius: 50%; background: #10B981; box-shadow: 0 0 10px #10B981; animation: pulse 1.8s infinite; }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .35; transform: scale(1.3); } }
        .hero h1 { font-size: clamp(2.2rem, 6vw, 3.9rem); font-weight: 900; margin-bottom: 1.25rem; }
        .hero h1 span { background: linear-gradient(135deg, var(--brand), #F97316); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--muted); font-size: clamp(1rem, 2.2vw, 1.2rem); max-width: 680px; margin: 0 auto 2rem; }
        .hero-cta { display: flex; gap: .85rem; justify-content: center; flex-wrap: wrap; }
        .hero-stats { display: flex; justify-content: center; gap: 2.5rem; flex-wrap: wrap; margin-top: 3rem; }
        .hero-stats .stat b { display: block; font-family: 'Outfit'; font-size: 1.6rem; font-weight: 800; color: #fff; }
        .hero-stats .stat span { color: var(--muted); font-size: .82rem; }

        .section-head { text-align: center; max-width: 640px; margin: 0 auto 3rem; }
        .section-head h2 { font-size: clamp(1.7rem, 4vw, 2.5rem); font-weight: 800; margin-bottom: .75rem; }
        .section-head p { color: var(--muted); }

        .features { padding: 4rem 0; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; }
        .feature { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 1.6rem; transition: transform .25s, border-color .25s; }
        .feature:hover { transform: translateY(-4px); border-color: rgba(255,51,0,.4); }
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
        .price-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; display: flex; flex-direction: column; }
        .price-card.featured { border-color: var(--brand); box-shadow: 0 0 0 1px var(--brand), 0 20px 50px rgba(255,51,0,.18); position: relative; }
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
        .trial-note strong { color: #34D399; }

        .cta-band { margin: 4rem 0; background: linear-gradient(135deg, rgba(255,51,0,.14), rgba(37,99,235,.14)); border: 1px solid var(--border); border-radius: 24px; padding: 3rem 2rem; text-align: center; }
        .cta-band h2 { font-size: clamp(1.6rem, 4vw, 2.3rem); margin-bottom: 1rem; }
        .cta-band p { color: var(--muted); margin-bottom: 1.75rem; max-width: 560px; margin-left: auto; margin-right: auto; }

        footer { border-top: 1px solid var(--border); padding: 2.5rem 0; text-align: center; color: var(--muted); font-size: .85rem; position: relative; z-index: 2; }
        footer a { color: #CBD5E1; font-weight: 600; }
        footer a:hover { color: var(--brand); }

        @media (max-width: 820px) {
            .nav-links { display: none; }
            .menu-toggle { display: block; }
            .nav-links.open { display: flex; position: absolute; top: 66px; left: 0; right: 0; flex-direction: column; background: rgba(7,11,20,.98); padding: 1.25rem; gap: 1rem; border-bottom: 1px solid var(--border); }
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
                <a href="#features">Vipengele</a>
                <a href="#how">Jinsi Inavyofanya Kazi</a>
                <a href="#pricing">Bei</a>
                <a href="{{ route('owner.login') }}">Ingia</a>
            </div>
            <div class="nav-cta">
                <a href="{{ route('owner.register') }}" class="btn btn-primary" style="display:none;" id="navDesktopCta">Anza Sasa</a>
                <a href="{{ route('owner.register') }}" class="btn btn-primary d-none d-md-inline-flex" id="navCta">Anza Sasa</a>
                <button class="menu-toggle" id="menuToggle" aria-label="Menyu"><i class="fas fa-bars"></i></button>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="wrap">
            <span class="pill"><span class="dot"></span> Imethibitishwa na wamiliki wa WiFi Tanzania</span>
            <h1>Endesha biashara yako ya <span>WiFi</span> kwa urahisi</h1>
            <p>Vocha, malipo ya mobile money, na ripoti za mapato — vyote mahali pamoja. Fuatilia biashara yako mahali popote ulipo.</p>
            <div class="hero-cta">
                <a href="{{ route('owner.register') }}" class="btn btn-primary"><i class="fas fa-rocket"></i> Fungua Akaunti Bure</a>
                <a href="#how" class="btn btn-ghost"><i class="fas fa-circle-play"></i> Jifunze Zaidi</a>
            </div>
            <div class="hero-stats">
                <div class="stat"><b>MikroTik</b><span>Msaada Kamili</span></div>
                <div class="stat"><b>5+</b><span>Njia za Malipo</span></div>
                <div class="stat"><b>Popote</b><span>Fuatilia Uko Wote</span></div>
                <div class="stat"><b>Mwezi 1</b><span>Jaribio la Bure</span></div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="wrap">
            <div class="section-head">
                <h2>Kila Kitu cha Kufaidika na WiFi</h2>
                <p>Malipo ya kuaminika, ripoti za wazi, na udhibiti kamili kutoka jukwaa moja.</p>
            </div>
            <div class="feature-grid">
                <div class="feature"><div class="ico"><i class="fas fa-tags"></i></div><h3>Portal Yenye Chapa Yako</h3><p>Nembo, rangi na vifurushi vyako vinaonekana kwenye ukurasa wa mgeni.</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-mobile-screen-button"></i></div><h3>Mobile Money</h3><p>M-Pesa, Tigo Pesa, Airtel Money na Halopesa — malipo ya papo hapo.</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-ticket"></i></div><h3>Vocha</h3><p>Tengeneza vocha kwa wingi kwa muda na bei unayotaka.</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-network-wired"></i></div><h3>MikroTik HotSpot</h3><p>Msaada kamili wa RouterOS. Bandika script moja na uanze kutengeneza mapato.</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-location-dot"></i></div><h3>Maeneo Mengi</h3><p>Hotspot nyingi chini ya akaunti moja ya mmiliki.</p></div>
                <div class="feature"><div class="ico"><i class="fas fa-chart-line"></i></div><h3>Ripoti</h3><p>Mapato ya vocha na malipo ya simu kwa TZS.</p></div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="steps" id="how">
        <div class="wrap">
            <div class="section-head">
                <h2>Hatua Tatu Kuanza Kupata Mapato</h2>
                <p>Kutoka usajili hadi mapato ya kwanza ndani ya dakika chache.</p>
            </div>
            <div class="step-grid">
                <div class="step"><div class="num">1</div><h3>Fungua Akaunti</h3><p>Jisajili kwa dakika moja. Unapata mwezi mmoja wa bure kujaribu kila kitu.</p></div>
                <div class="step"><div class="num">2</div><h3>Weka Bei Zako</h3><p>Bainisha vifurushi. Wezesha vocha, malipo ya simu, au vyote viwili.</p></div>
                <div class="step"><div class="num">3</div><h3>Pata Mapato</h3><p>Wageni wanalipa na kuingia mtandaoni papo hapo. Fuatilia kila kitu.</p></div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section class="pricing" id="pricing">
        <div class="wrap">
            <div class="section-head">
                <h2>Bei Rahisi</h2>
                <p>Anza na mwezi mmoja wa bure. Kisha chagua kifurushi kinachokufaa.</p>
            </div>
            <div class="price-grid">
                @forelse($plans as $i => $plan)
                <div class="price-card {{ $i === 1 ? 'featured' : '' }}">
                    <h3>{{ $plan->name }}</h3>
                    <div class="desc">{{ $plan->description ?: 'Kifurushi cha biashara ya WiFi' }}</div>
                    <div class="amount">TSh {{ number_format($plan->price) }} <small>/ {{ $plan->billing_cycle_months > 1 ? $plan->billing_cycle_months.' miezi' : 'mwezi' }}</small></div>
                    <ul>
                        <li><i class="fas fa-check-circle"></i><span>Routers hadi <strong>{{ number_format($plan->max_routers) }}</strong></span></li>
                        <li><i class="fas fa-check-circle"></i><span>Wateja hadi <strong>{{ number_format($plan->max_customers) }}</strong></span></li>
                        <li><i class="fas fa-check-circle"></i><span>Vocha, portal ya chapa &amp; ripoti</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Malipo ya mobile money</span></li>
                    </ul>
                    <a href="{{ route('owner.register', ['plan' => $plan->id]) }}" class="btn {{ $i === 1 ? 'btn-primary' : 'btn-ghost' }}">Chagua Kifurushi</a>
                </div>
                @empty
                <div class="price-card featured" style="grid-column: 1 / -1;">
                    <h3>Kifurushi cha Kuanzia</h3>
                    <div class="desc">Fungua akaunti yako leo na upate mwezi wa kwanza bure.</div>
                    <div class="amount">Bure <small>/ mwezi wa 1</small></div>
                    <ul>
                        <li><i class="fas fa-check-circle"></i><span>Routers zote na wateja</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Vocha, portal ya chapa &amp; ripoti</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Malipo ya mobile money</span></li>
                    </ul>
                    <a href="{{ route('owner.register') }}" class="btn btn-primary">Anza Sasa</a>
                </div>
                @endforelse
            </div>
            <p class="trial-note"><i class="fas fa-gift"></i> Kila akaunti mpya inapata <strong>kipindi cha majaribio cha bure</strong> — hakuna kadi ya benki inayohitajika.</p>
        </div>
    </section>

    <!-- CTA -->
    <section class="wrap">
        <div class="cta-band">
            <h2>Uko tayari kuanza kupata mapato?</h2>
            <p>Fungua akaunti yako leo na uanze kuendesha biashara yako ya WiFi kwa urahisi.</p>
            <a href="{{ route('owner.register') }}" class="btn btn-primary"><i class="fas fa-arrow-right"></i> Fungua Akaunti Bure</a>
        </div>
    </section>

    <footer>
        <div class="wrap">
            <div>&copy; {{ date('Y') }} MedBando. Haki zote zimehifadhiwa.</div>
            <div style="margin-top:.6rem;">
                <a href="{{ route('owner.login') }}">Ingia</a> &nbsp;·&nbsp;
                <a href="{{ route('owner.register') }}">Jisajili</a>
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
