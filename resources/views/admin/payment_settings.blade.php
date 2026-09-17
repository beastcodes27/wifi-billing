@extends('layouts.app')

@section('title', 'Njia za Malipo')

@section('content')
<div style="max-width: 840px; margin: 0 auto; padding-bottom: 5rem;">

    {{-- Page Header --}}
    <div class="page-header-compact" style="margin-bottom: 1.25rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">Njia za Malipo</h2>
                    <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Akaunti ya kupokelea fedha za wateja wanaonunua vifurushi</p>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.settings') }}" class="btn-banking btn-secondary mobile-btn-compact" style="text-decoration: none; font-size: 0.82rem; padding: 0.55rem 0.95rem; border-radius: 10px;">
            <i class="fas fa-arrow-left"></i> Rudi Mipangilio
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.65rem; font-weight: 600; font-size: 0.85rem;">
        <i class="fas fa-check-circle" style="color: #059669; font-size: 1rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Gateway Selectors --}}
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
        <button type="button" class="btn-banking btn-primary" style="font-size: 0.82rem; padding: 0.5rem 1rem; border-radius: 10px; background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%); color: #fff; border: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fas fa-check-circle"></i> FimiPay (Inatumika Sasa)
        </button>
        <button type="button" class="btn-banking btn-secondary" style="font-size: 0.82rem; padding: 0.5rem 1rem; border-radius: 10px; opacity: 0.6; cursor: not-allowed;" disabled>
            PalmPesa (Standby)
        </button>
        <button type="button" class="btn-banking btn-secondary" style="font-size: 0.82rem; padding: 0.5rem 1rem; border-radius: 10px; opacity: 0.6; cursor: not-allowed;" disabled>
            AzamPay (Coming Soon)
        </button>
    </div>

    {{-- Main Form Card --}}
    <div class="banking-card p-0" style="overflow: hidden; border-radius: 16px; border: 1px solid var(--border-color); background: #FFFFFF; box-shadow: var(--shadow-xs);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #F8FAFC; display: flex; align-items: center; gap: 0.85rem;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: var(--text-main);">FimiPay Integration</h3>
                <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Miamala ya M-Pesa, TigoPesa, Airtel Money na HaloPesa itaingia moja kwa moja kupitia FimiPay</p>
            </div>
        </div>

        <form action="{{ route('admin.settings.payment.update') }}" method="POST" style="padding: 1.5rem;">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label" style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem; display: block;">
                        FimiPay Public ID <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" id="fimipay_public_id" name="fimipay_public_id" value="{{ old('fimipay_public_id', $settings['fimipay']['public_id'] ?? env('FIMIPAY_PUBLIC_ID')) }}" placeholder="Mfano: pk_live_..." required class="form-control" style="border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.9rem; border: 1px solid var(--border-color); font-family: monospace; width: 100%;">
                    <small style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.35rem;">Public Key iliyotolewa na FimiPay kwenye akaunti yako ya biashara</small>
                </div>

                <div>
                    <label class="form-label" style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem; display: block;">
                        FimiPay Secret Key <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="password" id="fimipay_secret_key" name="fimipay_secret_key" value="{{ old('fimipay_secret_key', $settings['fimipay']['secret_key'] ?? env('FIMIPAY_SECRET_KEY')) }}" placeholder="Weka Secret Key ya FimiPay (sk_live_...)" required class="form-control" style="border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.9rem; border: 1px solid var(--border-color); font-family: monospace; width: 100%;">
                    <small style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.35rem;">Funguo hii ya siri inatumika kuanzisha maombi ya malipo na kukagua status</small>
                </div>

                <div>
                    <label class="form-label" style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem; display: block;">
                        Mazingira ya Mfumo (Environment)
                    </label>
                    @php
                        $currEnv = old('fimipay_environment', $settings['fimipay']['environment'] ?? env('FIMIPAY_ENVIRONMENT', 'live'));
                    @endphp
                    <select name="fimipay_environment" class="form-control" style="border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.9rem; border: 1px solid var(--border-color); background: #FFFFFF; width: 100%;">
                        <option value="live" {{ $currEnv === 'live' ? 'selected' : '' }}>Live (Miamala Halisi ya Pesa)</option>
                        <option value="test" {{ $currEnv === 'test' ? 'selected' : '' }}>Test (Mazingira ya Majaribio)</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size: 0.82rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem; display: block;">
                        Webhook URL (Callback Endpoint)
                    </label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="text" readonly id="webhookUrlInput" value="{{ route('portal.fimipay.webhook') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 0.7rem 0.95rem; border-radius: 10px; font-family: monospace; font-size: 0.82rem; color: #0F172A; width: 100%;" />
                        <button type="button" onclick="copyWebhookUrl()" id="copyWebhookBtn" class="btn-banking btn-secondary" style="padding: 0.7rem 1rem; border-radius: 10px; font-size: 0.82rem; white-space: nowrap; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i class="fas fa-copy"></i> Nakili
                        </button>
                    </div>
                    <small style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.35rem;">Weka link hii kwenye dashboard ya FimiPay ili kupokea taarifa ya papo hapo (Instant Notification)</small>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); flex-wrap: wrap;">
                <a href="{{ route('admin.dashboard') }}" class="btn-banking btn-secondary" style="text-decoration: none; padding: 0.65rem 1.25rem; font-size: 0.85rem; border-radius: 10px; font-weight: 700;">
                    Ghairi
                </a>
                <button type="submit" class="btn-banking btn-primary" style="padding: 0.65rem 1.5rem; font-size: 0.85rem; font-weight: 700; border-radius: 10px; background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%); color: #fff; border: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="fas fa-save"></i> Hifadhi Mipangilio ya FimiPay
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function copyWebhookUrl() {
    const input = document.getElementById('webhookUrlInput');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = document.getElementById('copyWebhookBtn');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check" style="color: #10B981;"></i> Imenakiliwa!';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2000);
    });
}
</script>
@endsection
