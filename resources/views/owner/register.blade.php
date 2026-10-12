<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#070B14">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Jisajili — MedBando</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --brand:#FF3300; --brand-2:#EA580C; --bg:#070B14; --card:rgba(15,23,42,.78); --border:rgba(255,255,255,.1); --muted:#94A3B8; --input:rgba(255,255,255,.05); }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color:transparent; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg); color:#fff; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1.5rem 1rem; position:relative; overflow-x:hidden; }
        .orb { position:fixed; border-radius:50%; filter:blur(110px); z-index:0; pointer-events:none; opacity:.5; }
        .orb-1 { width:360px; height:360px; background:radial-gradient(circle, rgba(255,51,0,.3), transparent 70%); top:-90px; left:-90px; }
        .orb-2 { width:400px; height:400px; background:radial-gradient(circle, rgba(37,99,235,.25), transparent 70%); bottom:-110px; right:-110px; }
        .grid-bg { position:fixed; inset:0; background-image:radial-gradient(rgba(255,255,255,.06) 1px, transparent 1px); background-size:28px 28px; z-index:0; opacity:.55; }
        .container { width:100%; max-width:560px; position:relative; z-index:2; }
        .card { background:var(--card); backdrop-filter:blur(24px); border:1px solid var(--border); border-radius:24px; padding:2.25rem 2rem; box-shadow:0 25px 50px -12px rgba(0,0,0,.65); position:relative; overflow:hidden; }
        .card::before { content:''; position:absolute; top:0; left:15%; right:15%; height:2px; background:linear-gradient(90deg, transparent, var(--brand), #3B82F6, transparent); opacity:.85; }
        .back { display:inline-flex; align-items:center; gap:.45rem; color:var(--muted); font-size:.82rem; font-weight:600; margin-bottom:1.25rem; }
        .back:hover { color:#fff; }
        .brand-header { text-align:center; margin-bottom:1.5rem; }
        .logo-wrap { display:inline-flex; background:#fff; padding:10px 22px; border-radius:18px; margin-bottom:1rem; border:2px solid rgba(255,255,255,.25); }
        .logo-wrap img { height:42px; display:block; }
        h1 { font-family:'Outfit'; font-size:1.5rem; font-weight:800; margin-bottom:.3rem; }
        .sub { color:var(--muted); font-size:.86rem; }
        .trial-pill { display:inline-flex; align-items:center; gap:.5rem; background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.3); color:#34D399; padding:.3rem .9rem; border-radius:50px; font-size:.74rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; margin-top:.75rem; }
        .alert { background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.35); color:#FCA5A5; padding:.8rem 1rem; border-radius:12px; font-size:.83rem; font-weight:600; margin-bottom:1.25rem; }
        .alert ul { margin:0; padding-left:1.1rem; }
        .grid2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .form-group { margin-bottom:1.1rem; }
        label { display:block; font-size:.8rem; font-weight:700; color:#CBD5E1; margin-bottom:.45rem; letter-spacing:.2px; }
        .input-wrapper { position:relative; display:flex; align-items:center; }
        .input-wrapper i { position:absolute; left:1rem; color:#64748B; font-size:.9rem; pointer-events:none; }
        input[type=text], input[type=email], input[type=tel], input[type=password] { width:100%; height:48px; padding:0 1rem 0 2.7rem; background:var(--input); border:1.5px solid var(--border); border-radius:13px; color:#fff; font-size:16px; font-family:inherit; outline:none; transition:.25s; }
        input::placeholder { color:#475569; font-size:.86rem; }
        input:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(255,51,0,.2); }
        .plans { display:grid; gap:.7rem; margin-bottom:1.25rem; }
        .plan { display:flex; align-items:center; gap:.85rem; border:1.5px solid var(--border); border-radius:14px; padding:.9rem 1rem; cursor:pointer; transition:.2s; background:rgba(255,255,255,.02); }
        .plan:hover { border-color:rgba(255,51,0,.4); }
        .plan input { display:none; }
        .plan .radio { width:20px; height:20px; border-radius:50%; border:2px solid rgba(255,255,255,.25); flex-shrink:0; position:relative; transition:.2s; }
        .plan.selected { border-color:var(--brand); background:rgba(255,51,0,.07); }
        .plan.selected .radio { border-color:var(--brand); }
        .plan.selected .radio::after { content:''; position:absolute; inset:3px; border-radius:50%; background:var(--brand); }
        .plan .info { flex:1; }
        .plan .name { font-weight:700; font-size:.92rem; }
        .plan .meta { color:var(--muted); font-size:.76rem; }
        .plan .price { font-family:'Outfit'; font-weight:800; color:#fff; }
        .btn-submit { width:100%; height:50px; background:linear-gradient(135deg,var(--brand),var(--brand-2)); color:#fff; border:none; border-radius:50px; font-family:'Outfit'; font-size:.95rem; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:.55rem; box-shadow:0 8px 20px rgba(255,51,0,.4); transition:.2s; margin-top:.5rem; }
        .btn-submit:hover { transform:translateY(-1.5px); box-shadow:0 12px 28px rgba(255,51,0,.55); }
        .btn-submit:disabled { opacity:.7; cursor:not-allowed; transform:none; }
        .footer { text-align:center; margin-top:1.35rem; font-size:.83rem; color:var(--muted); }
        .footer a { color:#CBD5E1; font-weight:700; }
        .footer a:hover { color:var(--brand); }
        @media (max-width:520px) { .card { padding:1.75rem 1.35rem; border-radius:20px; } .grid2 { grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="grid-bg"></div>

    <div class="container">
        <div class="card">
            <a href="{{ route('landing') }}" class="back"><i class="fas fa-arrow-left"></i> Nyumbani</a>

            <div class="brand-header">
                <div class="logo-wrap"><img src="{{ asset('images/logo.png') }}" alt="MedBando"></div>
                <h1>Fungua Akaunti ya Biashara</h1>
                <p class="sub">Anza kuendesha biashara yako ya WiFi leo</p>
                <div><span class="trial-pill"><i class="fas fa-gift"></i> Mwezi 1 wa Bure</span></div>
            </div>

            @if($errors->any())
                <div class="alert">
                    <ul>
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('owner.register.submit') }}" method="POST" id="registerForm">
                @csrf

                <div class="form-group">
                    <label for="business_name">Jina la Biashara</label>
                    <div class="input-wrapper">
                        <i class="fas fa-store"></i>
                        <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}" placeholder="Mfano: Mama Asha WiFi" required>
                    </div>
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="owner_name">Jina Lako</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" placeholder="Jina kamili" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Namba ya Simu</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="07XXXXXXXX" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Barua Pepe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="wewe@biashara.com" required>
                    </div>
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="password">Nenosiri</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Angalau herufi 8" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Rudia Nenosiri</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Rudia nenosiri" required>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:.6rem;">
                    <label>Chagua Kifurushi</label>
                </div>
                <div class="plans" id="plans">
                    @php $selectedPlan = old('saas_plan_id', request('plan')); @endphp
                    @forelse($plans as $plan)
                    <label class="plan {{ (string)$selectedPlan === (string)$plan->id || ( !$selectedPlan && $loop->first ) ? 'selected' : '' }}" data-plan>
                        <input type="radio" name="saas_plan_id" value="{{ $plan->id }}" {{ (string)$selectedPlan === (string)$plan->id || ( !$selectedPlan && $loop->first ) ? 'checked' : '' }}>
                        <span class="radio"></span>
                        <span class="info">
                            <span class="name">{{ $plan->name }}</span>
                            <span class="meta">{{ number_format($plan->max_routers) }} routers · {{ number_format($plan->max_customers) }} wateja</span>
                        </span>
                        <span class="price">TSh {{ number_format($plan->price) }}</span>
                    </label>
                    @empty
                    <div class="alert" style="margin:0;">Hakuna vifurushi vinavyopatikana kwa sasa. Tafadhali jaribu tena baadaye.</div>
                    @endforelse
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-user-plus" id="btnIcon"></i>
                    <span id="btnText">Fungua Akaunti Yangu</span>
                </button>
            </form>

            <div class="footer">
                Tayari una akaunti? <a href="{{ route('owner.login') }}">Ingia hapa</a>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-plan]').forEach(function (el) {
            el.addEventListener('click', function () {
                document.querySelectorAll('[data-plan]').forEach(function (p) { p.classList.remove('selected'); });
                el.classList.add('selected');
                el.querySelector('input').checked = true;
            });
        });
        document.getElementById('registerForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('btnIcon').className = 'fas fa-spinner fa-spin';
            document.getElementById('btnText').innerText = 'Inasajili...';
        });
    </script>
</body>
</html>
