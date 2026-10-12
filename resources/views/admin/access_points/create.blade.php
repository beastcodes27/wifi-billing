@extends('layouts.app')

@section('title', 'Ongeza Access Point')

@section('content')
<style>
    .ap-tabs { display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1.25rem; }
    .ap-tab { border:2px solid var(--border-color); border-radius:14px; padding:1rem; cursor:pointer; background:#fff; transition:all .2s; display:flex; align-items:center; gap:0.75rem; }
    .ap-tab:hover { border-color:rgba(37,99,235,0.4); }
    .ap-tab.active { border-color:#2563EB; background:rgba(37,99,235,0.05); box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
    .ap-tab .ico { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex-shrink:0; background:rgba(37,99,235,0.1); color:#2563EB; }
    .ap-tab .t { font-weight:800; color:var(--text-main); font-size:0.9rem; }
    .ap-tab .d { font-size:0.74rem; color:var(--text-muted); }
    .photo-drop { border:2px dashed var(--border-color); border-radius:16px; padding:1.75rem 1rem; text-align:center; cursor:pointer; background:#FAFAFC; transition:all .2s; }
    .photo-drop:hover { border-color:#2563EB; background:rgba(37,99,235,0.04); }
    .photo-drop .cam { width:56px; height:56px; border-radius:16px; background:rgba(37,99,235,0.1); color:#2563EB; display:inline-flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:0.6rem; }
    .photo-preview { display:none; max-width:100%; max-height:280px; border-radius:12px; border:1px solid var(--border-color); margin:0 auto 0.75rem; }
    .ap-field label { display:block; font-size:0.78rem; font-weight:700; color:var(--text-secondary); margin-bottom:0.3rem; }
    .ap-field input, .ap-field select, .ap-field textarea { width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:10px; font-size:0.88rem; color:var(--text-main); background:#fff; outline:none; }
    .ap-field input:focus, .ap-field select:focus, .ap-field textarea:focus { border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
    .ap-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:0.9rem; }
    @media (max-width:640px){ .ap-tabs{grid-template-columns:1fr;} .ap-grid2{grid-template-columns:1fr;} }
    .scan-status { display:none; margin-top:0.75rem; padding:0.7rem 0.9rem; border-radius:10px; font-size:0.83rem; font-weight:600; align-items:center; gap:0.55rem; }
</style>

<div class="page-header-compact" style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
    <div>
        <h2><i class="fas fa-tower-broadcast" style="color:var(--primary-blue);"></i> Ongeza Access Point</h2>
        <p>Chagua kuweka kwa mkono au kwa kupiga picha ya sticker (AI)</p>
    </div>
    <a href="{{ route('admin.access_points.index') }}" class="btn-banking" style="border:1px solid var(--border-color); background:#fff; color:var(--text-secondary); white-space:nowrap;">
        <i class="fas fa-arrow-left"></i> Rudi
    </a>
</div>

@if($errors->any())
<div class="banking-card mb-3" style="background:#FEF2F2; border-color:#FECACA; color:#991B1B; padding:0.8rem 1rem; border-radius:10px; font-size:0.84rem;">
    <strong>Kuna makosa:</strong>
    <ul style="margin:0.35rem 0 0 1rem; padding:0;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.access_points.store') }}" method="POST" id="ap-form">
    @csrf
    <input type="hidden" name="adoption_method" id="adoption_method" value="manual">
    <input type="hidden" name="scan_confidence" id="scan_confidence" value="">
    <input type="hidden" name="scan_raw" id="scan_raw" value="">

    <div class="banking-card p-3 p-md-4 mb-3">
        <div class="ap-tabs">
            <div class="ap-tab active" data-mode="ai_scan">
                <div class="ico"><i class="fas fa-camera"></i></div>
                <div>
                    <div class="t">Piga Picha ya Sticker (AI)</div>
                    <div class="d">AI/OCR isome Serial & Device Key kiotomatiki</div>
                </div>
            </div>
            <div class="ap-tab" data-mode="manual">
                <div class="ico" style="background:rgba(16,185,129,0.1); color:#10B981;"><i class="fas fa-keyboard"></i></div>
                <div>
                    <div class="t">Weka kwa Mkono</div>
                    <div class="d">Jaza taarifa za kifaa mwenyewe</div>
                </div>
            </div>
        </div>

        {{-- AI scan panel --}}
        <div id="ai-panel">
            <div class="photo-drop" id="photoDrop">
                <div class="cam"><i class="fas fa-camera-retro"></i></div>
                <div style="font-weight:800; color:var(--text-main); font-size:0.92rem;">Piga au chagua picha ya sticker</div>
                <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.25rem;">JPG / PNG / WebP — kiwango cha juu 8MB</div>
                <input type="file" id="photo" accept="image/*" capture="environment" style="display:none;">
            </div>
            <div style="text-align:center;">
                <img id="photoPreview" class="photo-preview" alt="Sticker preview">
            </div>
            <div style="text-align:center; margin-top:0.5rem;">
                <button type="button" id="scanBtn" class="btn-banking btn-primary" style="display:none; margin:0 auto;">
                    <i class="fas fa-wand-magic-sparkles"></i> Soma kwa AI
                </button>
            </div>
            <div class="scan-status" id="scanStatus"></div>
        </div>

        <div class="scan-status" id="manualHint" style="display:flex; background:rgba(37,99,235,0.06); color:#1E40AF; border:1px solid rgba(37,99,235,0.18);">
            <i class="fas fa-keyboard"></i> Weka taarifa za kifaa kwenye fomu hapa chini.
        </div>
    </div>

    {{-- Detail fields --}}
    <div class="banking-card p-3 p-md-4">
        <h3 style="font-size:1rem; font-weight:800; color:var(--text-main); margin:0 0 1rem;">Taarifa za Kifaa</h3>

        <div class="ap-grid2" style="margin-bottom:0.9rem;">
            <div class="ap-field">
                <label for="brand">Aina ya Kifaa (Brand)</label>
                <select name="brand" id="brand" required>
                    @foreach($brands as $b)
                        <option value="{{ $b }}" {{ old('brand') === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ap-field">
                <label for="site_id">Site (Eneo)</label>
                <select name="site_id" id="site_id">
                    <option value="">— Hakuna / Chagua —</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ (string)old('site_id') === (string)$site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="ap-grid2" style="margin-bottom:0.9rem;">
            <div class="ap-field">
                <label for="name">Jina la Kifaa</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Mfano: EAP225 Ukumbi">
            </div>
            <div class="ap-field">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="Mfano: EAP225 / hAP ac2">
            </div>
        </div>

        <div class="ap-grid2" style="margin-bottom:0.9rem;">
            <div class="ap-field">
                <label for="serial_number">Serial Number (S/N)</label>
                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" placeholder="Namba ya serial">
            </div>
            <div class="ap-field">
                <label for="device_key">Device Key</label>
                <input type="text" name="device_key" id="device_key" value="{{ old('device_key') }}" placeholder="Device key / password">
            </div>
        </div>

        <div class="ap-grid2" style="margin-bottom:0.9rem;">
            <div class="ap-field">
                <label for="mac_address">MAC Address</label>
                <input type="text" name="mac_address" id="mac_address" value="{{ old('mac_address') }}" placeholder="AA:BB:CC:DD:EE:FF">
            </div>
            <div class="ap-field">
                <label for="management_ip">Management IP</label>
                <input type="text" name="management_ip" id="management_ip" value="{{ old('management_ip') }}" placeholder="192.168.1.10">
            </div>
        </div>

        <div class="ap-field" style="margin-bottom:1.1rem;">
            <label for="notes">Maelezo (hiari)</label>
            <textarea name="notes" id="notes" rows="2" placeholder="Maelezo ya ziada...">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn-banking btn-primary" style="justify-content:center; width:100%;">
            <i class="fas fa-save"></i> Hifadhi Access Point
        </button>
    </div>
</form>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const tabs = document.querySelectorAll('.ap-tab');
    const aiPanel = document.getElementById('ai-panel');
    const manualHint = document.getElementById('manualHint');
    const methodInput = document.getElementById('adoption_method');
    const fileInput = document.getElementById('photo');
    const drop = document.getElementById('photoDrop');
    const preview = document.getElementById('photoPreview');
    const scanBtn = document.getElementById('scanBtn');
    const statusBox = document.getElementById('scanStatus');

    function setMode(mode) {
        tabs.forEach(t => t.classList.toggle('active', t.dataset.mode === mode));
        const ai = mode === 'ai_scan';
        aiPanel.style.display = ai ? 'block' : 'none';
        manualHint.style.display = ai ? 'none' : 'flex';
        methodInput.value = mode;
    }
    tabs.forEach(t => t.addEventListener('click', () => setMode(t.dataset.mode)));
    setMode('ai_scan');

    drop.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        const f = fileInput.files[0];
        if (!f) return;
        preview.src = URL.createObjectURL(f);
        preview.style.display = 'block';
        scanBtn.style.display = 'inline-flex';
        hideStatus();
    });

    function showStatus(type, msg) {
        statusBox.style.display = 'flex';
        if (type === 'error') {
            statusBox.style.background = '#FEF2F2';
            statusBox.style.color = '#991B1B';
            statusBox.style.border = '1px solid #FECACA';
            statusBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
        } else if (type === 'ok') {
            statusBox.style.background = '#ECFDF5';
            statusBox.style.color = '#065F46';
            statusBox.style.border = '1px solid #A7F3D0';
            statusBox.innerHTML = '<i class="fas fa-check-circle"></i> ' + msg;
        } else {
            statusBox.style.background = 'rgba(37,99,235,0.06)';
            statusBox.style.color = '#1E40AF';
            statusBox.style.border = '1px solid rgba(37,99,235,0.18)';
            statusBox.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + msg;
        }
    }
    function hideStatus() { statusBox.style.display = 'none'; }

    scanBtn.addEventListener('click', function () {
        const f = fileInput.files[0];
        if (!f) return;

        const fd = new FormData();
        fd.append('photo', f);
        fd.append('_token', csrf);

        scanBtn.disabled = true;
        showStatus('wait', 'Inasoma sticker... tafadhali subiri.');

        fetch("{{ route('admin.access_points.scan') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: fd,
        })
        .then(r => r.json().then(d => ({ ok: r.ok, d })))
        .then(({ ok, d }) => {
            scanBtn.disabled = false;
            if (!ok || !d.ok) {
                showStatus('error', d.message || 'Imeshindikana kusoma sticker. Jaribu picha iliyo wazi zaidi.');
                return;
            }
            let filled = 0;
            [['brand', 'brand'], ['model', 'model'], ['serial_number', 'serial_number'],
             ['device_key', 'device_key'], ['mac_address', 'mac_address']].forEach(function (p) {
                const el = document.getElementById(p[0]);
                if (d[p[1]]) { el.value = d[p[1]]; filled++; }
            });
            document.getElementById('scan_confidence').value = d.confidence || '';
            document.getElementById('scan_raw').value = d.raw ? JSON.stringify(d.raw) : '';
            methodInput.value = 'ai_scan';
            showStatus('ok', 'Imesomwa kwa mafanikio (' + (d.driver || 'AI') + '). Thibitisha taarifa kisha hifadhi. Sehemu ' + filled + ' zimejazwa.');
        })
        .catch(() => {
            scanBtn.disabled = false;
            showStatus('error', 'Hitilafu ya mtandao. Jaribu tena.');
        });
    });
})();
</script>
@endsection
