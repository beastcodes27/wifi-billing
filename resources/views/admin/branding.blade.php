@extends('layouts.app')

@section('title', 'Muonekano wa Portal')

@section('content')
<style>
    .br-field label { display:block; font-size:0.78rem; font-weight:700; color:var(--text-secondary); margin-bottom:0.3rem; }
    .br-field input[type=text], .br-field input[type=email], .br-field input[type=tel] {
        width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:10px;
        font-size:0.88rem; color:var(--text-main); background:#fff; outline:none;
    }
    .br-field input:focus { border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
    .br-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:0.9rem; }
    @media (max-width:640px){ .br-grid2{grid-template-columns:1fr;} }
    .color-swatch { width:46px; height:44px; border:1px solid var(--border-color); border-radius:10px; padding:2px; background:#fff; cursor:pointer; }
    .preset { width:26px; height:26px; border-radius:50%; border:2px solid #fff; box-shadow:0 0 0 1px var(--border-color); cursor:pointer; }
    .br-preview { border-radius:16px; overflow:hidden; box-shadow:0 12px 30px rgba(0,0,0,0.18); }
    .br-preview .pv-header { padding:1rem; color:#fff; border-bottom-left-radius:18px; border-bottom-right-radius:18px; }
    .br-preview .pv-logo { width:38px; height:38px; background:#fff; border-radius:10px; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .br-preview .pv-logo img { width:100%; height:100%; object-fit:contain; }
</style>

<div class="page-header-compact" style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
    <div>
        <h2><i class="fas fa-palette" style="color:var(--primary-blue);"></i> Muonekano wa Portal ya Wageni</h2>
        <p>Badilisha jina, taarifa za mawasiliano, rangi na nembo ya ukurasa wageni wanaouona</p>
    </div>
</div>

@if(session('success'))
<div class="banking-card mb-3" style="background:#ECFDF5; border-color:#A7F3D0; color:#065F46; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.6rem; border-radius:10px;">
    <i class="fas fa-check-circle" style="color:#059669;"></i>
    <span style="font-weight:600; font-size:0.85rem;">{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div class="banking-card mb-3" style="background:#FEF2F2; border-color:#FECACA; color:#991B1B; padding:0.8rem 1rem; border-radius:10px; font-size:0.84rem;">
    <ul style="margin:0 0 0 1rem; padding:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div style="display:grid; grid-template-columns: 1.4fr 1fr; gap:1.25rem; align-items:start;" class="branding-layout">
    {{-- Form --}}
    <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="banking-card p-3 p-md-4">
            <h3 style="font-size:1rem; font-weight:800; color:var(--text-main); margin:0 0 1rem;"><i class="fas fa-store"></i> Jina &amp; Mawasiliano</h3>

            <div class="br-field" style="margin-bottom:0.9rem;">
                <label for="display_name">Jina la Biashara (linaloonekana)</label>
                <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $branding['name']) }}" placeholder="Mfano: Mama Asha WiFi" maxlength="120">
            </div>

            <div class="br-field" style="margin-bottom:0.9rem;">
                <label for="tagline">Mstari wa Chini (Tagline)</label>
                <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $branding['tagline']) }}" placeholder="FAST & UNLIMITED WI-FI" maxlength="120">
            </div>

            <div class="br-grid2" style="margin-bottom:0.9rem;">
                <div class="br-field">
                    <label for="contact_phone">Namba ya Simu (kuwasaidia)</label>
                    <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $branding['phone']) }}" placeholder="07XXXXXXXX">
                </div>
                <div class="br-field">
                    <label for="whatsapp">WhatsApp (hiari)</label>
                    <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $branding['whatsapp']) }}" placeholder="07XXXXXXXX">
                </div>
            </div>

            <div class="br-field" style="margin-bottom:1.25rem;">
                <label for="email">Barua Pepe (hiari)</label>
                <input type="email" name="email" id="email" value="{{ old('email', $branding['email']) }}" placeholder="biashara@email.com">
            </div>

            <h3 style="font-size:1rem; font-weight:800; color:var(--text-main); margin:0 0 1rem;"><i class="fas fa-droplet"></i> Rangi</h3>

            <div class="br-grid2" style="margin-bottom:0.9rem;">
                <div class="br-field">
                    <label for="primary_color">Rangi Kuu (Primary)</label>
                    <div style="display:flex; align-items:center; gap:0.6rem;">
                        <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $branding['primary_color']) }}" class="color-swatch">
                        <input type="text" id="primary_hex" value="{{ old('primary_color', $branding['primary_color']) }}" readonly style="flex:1; padding:0.55rem 0.75rem; border:1px solid var(--border-color); border-radius:10px; font-family:monospace; font-size:0.85rem;">
                    </div>
                </div>
                <div class="br-field">
                    <label for="accent_color">Rangi ya Pili (Accent)</label>
                    <div style="display:flex; align-items:center; gap:0.6rem;">
                        <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', $branding['accent_color']) }}" class="color-swatch">
                        <input type="text" id="accent_hex" value="{{ old('accent_color', $branding['accent_color']) }}" readonly style="flex:1; padding:0.55rem 0.75rem; border:1px solid var(--border-color); border-radius:10px; font-family:monospace; font-size:0.85rem;">
                    </div>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-bottom:1.25rem;">
                <span style="font-size:0.75rem; color:var(--text-muted); font-weight:700;">Mapendekezo:</span>
                <span class="preset" data-p="#FF3300" data-a="#cc2900" style="background:#FF3300;"></span>
                <span class="preset" data-p="#2563EB" data-a="#1D4ED8" style="background:#2563EB;"></span>
                <span class="preset" data-p="#16A34A" data-a="#15803D" style="background:#16A34A;"></span>
                <span class="preset" data-p="#7C3AED" data-a="#6D28D9" style="background:#7C3AED;"></span>
                <span class="preset" data-p="#DB2777" data-a="#BE185D" style="background:#DB2777;"></span>
                <span class="preset" data-p="#0F172A" data-a="#020617" style="background:#0F172A;"></span>
            </div>

            <h3 style="font-size:1rem; font-weight:800; color:var(--text-main); margin:0 0 1rem;"><i class="fas fa-image"></i> Nembo (Logo)</h3>

            <div class="br-field" style="margin-bottom:0.75rem;">
                <input type="file" name="logo" id="logo" accept="image/*">
                <small style="display:block; color:var(--text-muted); font-size:0.74rem; margin-top:0.3rem;">PNG/JPG/SVG, kiwango cha juu 2MB. Inaonekana kwenye kichwa cha portal.</small>
            </div>
            @if(!empty($branding['logo']))
            <label style="display:inline-flex; align-items:center; gap:0.5rem; font-size:0.8rem; color:var(--text-secondary); font-weight:600;">
                <input type="checkbox" name="remove_logo" value="1"> Ondoa nembo ya sasa
            </label>
            @endif

            <button type="submit" class="btn-banking btn-primary" style="justify-content:center; width:100%; margin-top:1.25rem;">
                <i class="fas fa-save"></i> Hifadhi Muonekano
            </button>
        </div>
    </form>

    {{-- Live preview --}}
    <div>
        <div class="banking-card p-3">
            <div style="font-size:0.78rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:0.75rem;">Onyesho la Moja kwa Moja</div>
            <div class="br-preview">
                <div class="pv-header" id="pvHeader">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div style="line-height:1.1;">
                            <div style="font-size:0.6rem; opacity:0.85; text-transform:uppercase; letter-spacing:0.5px;">Tupigie</div>
                            <div style="font-weight:800; font-size:0.95rem;" id="pvPhone">0762457308</div>
                        </div>
                        <div style="background:rgba(255,255,255,0.18); border-radius:20px; padding:2px 8px; font-size:0.62rem; font-weight:700;">ENG · SWA</div>
                    </div>
                    <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-top:0.6rem;">
                        <div class="pv-logo">
                            <img id="pvLogo" src="{{ !empty($branding['logo']) ? asset($branding['logo']) : asset('images/logo-icon.png') }}" alt="logo">
                        </div>
                        <div id="pvName" style="font-size:1.15rem; font-weight:900;">{{ strtoupper($branding['name']) }}</div>
                    </div>
                    <div id="pvTagline" style="text-align:center; font-size:0.62rem; font-weight:700; letter-spacing:0.8px; opacity:0.9; margin-top:0.2rem; text-transform:uppercase;">{{ $branding['tagline'] }}</div>
                </div>
            </div>
            <p style="font-size:0.74rem; color:var(--text-muted); margin-top:0.75rem;">Hivi ndivyo wageni wataona kichwa cha ukurasa wakati wa kuunganisha.</p>
        </div>
    </div>
</div>

<script>
(function () {
    const pc = document.getElementById('primary_color');
    const ac = document.getElementById('accent_color');
    const ph = document.getElementById('primary_hex');
    const ah = document.getElementById('accent_hex');
    const pv = document.getElementById('pvHeader');
    const name = document.getElementById('display_name');
    const tag = document.getElementById('tagline');
    const phone = document.getElementById('contact_phone');
    const pvName = document.getElementById('pvName');
    const pvTag = document.getElementById('pvTagline');
    const pvPhone = document.getElementById('pvPhone');
    const logoInput = document.getElementById('logo');
    const pvLogo = document.getElementById('pvLogo');

    function paint() {
        pv.style.background = 'linear-gradient(135deg, ' + pc.value + ', ' + ac.value + ')';
        ph.value = pc.value;
        ah.value = ac.value;
    }
    function texts() {
        pvName.textContent = (name.value || 'MedBando').toUpperCase();
        pvTag.textContent = tag.value || 'FAST & UNLIMITED WI-FI';
        pvPhone.textContent = phone.value || '0762457308';
    }
    pc.addEventListener('input', paint);
    ac.addEventListener('input', paint);
    [name, tag, phone].forEach(el => el.addEventListener('input', texts));
    document.querySelectorAll('.preset').forEach(function (el) {
        el.addEventListener('click', function () {
            pc.value = el.dataset.p; ac.value = el.dataset.a; paint();
        });
    });
    logoInput.addEventListener('change', function () {
        const f = logoInput.files[0];
        if (f) pvLogo.src = URL.createObjectURL(f);
    });
    paint(); texts();
})();
</script>
<style>
@media (max-width: 900px){ .branding-layout{ grid-template-columns: 1fr !important; } }
</style>
@endsection
