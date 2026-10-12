@extends('layouts.app')

@section('title', 'Mipangilio')

@section('content')
<div style="padding-bottom: 5rem;">

    {{-- Page Header --}}
    <div class="page-header-compact" style="margin-bottom: 1.25rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                    <i class="fas fa-cog"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">Mipangilio</h2>
                    <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Usanidi wa mfumo na mtandao</p>
                </div>
            </div>
        </div>
    </div>

    <div class="settings-two-col-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
        {{-- MikroTik RouterOS Gateway --}}
        <div>
            <div class="banking-card p-3 p-md-4 h-100" style="border-radius: 16px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(15, 23, 42, 0.08); color: #0F172A; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div style="min-width: 0;">
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--text-main);">MikroTik RouterOS Gateway</h3>
                            <p style="margin: 0.15rem 0 0; font-size: 0.78rem; color: var(--text-muted);">Mawasiliano ya msingi ya mtandao na API</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.routers.index') }}" class="btn-banking btn-secondary" style="font-size: 0.75rem; padding: 0.4rem 0.75rem; border-radius: 8px; text-decoration: none; white-space: nowrap;">
                        Dhibiti Routers
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 0.35rem;">IP Address / Host</label>
                        <div style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 0.65rem 0.95rem; border-radius: 10px; font-family: monospace; font-weight: 700; color: #0F172A; word-break: break-all; font-size: 0.85rem;">
                            {{ env('MIKROTIK_HOST', 'Not Set') }}
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 0.35rem;">Username</label>
                            <div style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 0.65rem 0.95rem; border-radius: 10px; font-family: monospace; font-weight: 700; color: #0F172A; font-size: 0.85rem;">
                                {{ env('MIKROTIK_USER', 'Not Set') }}
                            </div>
                        </div>

                        <div>
                            <label style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 0.35rem;">API Port</label>
                            <div style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 0.65rem 0.95rem; border-radius: 10px; font-family: monospace; font-weight: 700; color: #0F172A; font-size: 0.85rem;">
                                {{ env('MIKROTIK_PORT', '8728') }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 0.35rem;">Hali ya Miunganisho</label>
                        <div style="display: flex; align-items: center; gap: 0.5rem; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 0.65rem 0.95rem; border-radius: 10px; color: #065F46; font-size: 0.82rem; font-weight: 700;">
                            <i class="fas fa-check-circle" style="color: #10B981;"></i>
                            <span>Multi-Router Tenant Architecture Imewezeshwa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SonicPesa Gateway (platform-managed) --}}
        <div>
            <div class="banking-card p-3 p-md-4 h-100" style="border-radius: 16px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div style="min-width: 0;">
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--text-main);">SonicPesa Gateway</h3>
                            <p style="margin: 0.15rem 0 0; font-size: 0.78rem; color: var(--text-muted);">Lango la malipo la mfumo (linawekwa na msimamizi mkuu)</p>
                        </div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 0.35rem;">Base URL</label>
                        <div style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 0.65rem 0.95rem; border-radius: 10px; font-family: monospace; font-size: 0.82rem; color: #0F172A; word-break: break-all;">
                            {{ env('SONIC_PESA_BASE_URL', 'https://api.sonicpesa.com/api/v1') }}
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 0.35rem;">Currency</label>
                            <div style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 0.65rem 0.95rem; border-radius: 10px; font-family: monospace; font-weight: 700; color: #0F172A; font-size: 0.85rem;">
                                {{ env('SONIC_PESA_CURRENCY', 'TZS') }}
                            </div>
                        </div>

                        <div>
                            <label style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 0.35rem;">API Key</label>
                            <div style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 0.65rem 0.95rem; border-radius: 10px; font-family: monospace; font-size: 0.82rem;">
                                @if(env('SONIC_PESA_API_KEY'))
                                    <span style="font-size: 0.7rem; color: #10B981; font-weight: 700;">Imehifadhiwa</span>
                                @else
                                    <span style="font-size: 0.7rem; color: #DC2626; font-weight: 700;">Haijawekwa</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    {{-- Eneo la Kuweka Mauzo ya Dashibodi (Set Dashboard Sales e.g. Tsh 10,000) --}}
    <div style="margin-top: 1.5rem;">
        <div class="banking-card p-3 p-md-4" style="border-radius: 16px; background: #FFFFFF; border: 1px solid #BBF7D0; box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.85rem; border-bottom: 1px solid #DCFCE7; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: #166534;">Weka Mauzo ya Dashibodi (Kianzio cha Mauzo)</h3>
                        <p style="margin: 0.15rem 0 0; font-size: 0.78rem; color: #15803D;">Weka kiwango cha kuanzia cha mauzo (mfano Tsh 10,000) ili dashibodi isomwe kiwango hicho mara moja</p>
                    </div>
                </div>
                <span class="badge" style="background: #DCFCE7; color: #166534; font-weight: 700; font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 8px;">
                    <i class="fas fa-check-circle"></i> Inasasisha Dashibodi Moja kwa Moja
                </span>
            </div>

            <div style="background: #F0FDF4; border-left: 4px solid #16A34A; padding: 0.85rem 1rem; border-radius: 8px; margin-bottom: 1.25rem;">
                <div style="font-size: 0.82rem; color: #14532D; line-height: 1.5;">
                    Kipengele hiki kinajaza miamala ya vocha zilizotumika (activated) ili dashibodi yako isomwe kiwango unachotaka mara moja (kama vile <strong>Tsh 10,000</strong>). Kila vocha mpya itakayotumiwa na mteja itaongezeka juu ya kiwango hiki kiotomatiki.
                </div>
            </div>

            <form action="{{ route('admin.settings.set_sales') }}" method="POST">
                @csrf
                <div style="display: flex; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 220px;">
                        <label style="display: block; font-size: 0.78rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.4rem;">
                            Kiasi cha Mauzo (Tsh):
                        </label>
                        <input type="number" name="target_amount" value="10000" min="500" step="500" required 
                               style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid #BBF7D0; border-radius: 10px; font-size: 1.1rem; font-weight: 800; color: #166534; background: #F8FAFC;">
                    </div>
                    <div>
                        <button type="submit" class="btn-banking" style="background: linear-gradient(135deg, #16A34A 0%, #15803D 100%); color: #ffffff; border: none; padding: 0.7rem 1.45rem; border-radius: 10px; font-weight: 800; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);">
                            <i class="fas fa-check-double"></i> Weka Tsh 10,000 Kwenye Dashibodi Sasa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Eneo la Kuanzisha Upya Takwimu za Mauzo (Reset Sales Data) --}}
    <div style="margin-top: 1.5rem;">
        <div class="banking-card p-3 p-md-4" style="border-radius: 16px; background: #FFFFFF; border: 1px solid #FECACA; box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.85rem; border-bottom: 1px solid #FEE2E2; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                        <i class="fas fa-trash-restore-alt"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: #991B1B;">Anzisha Upya Dashibodi (Soma Tsh 00)</h3>
                        <p style="margin: 0.15rem 0 0; font-size: 0.78rem; color: #B91C1C;">Weka upya miamala na matumizi ili kuanza upya kuhesabu fedha kuanzia mwanzo</p>
                    </div>
                </div>
                <span class="badge" style="background: #FEE2E2; color: #991B1B; font-weight: 700; font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 8px;">
                    <i class="fas fa-shield-alt"></i> Routers &amp; Vifurushi Vipo Salama
                </span>
            </div>

            <div style="background: #FEF2F2; border-left: 4px solid #EF4444; padding: 0.85rem 1rem; border-radius: 8px; margin-bottom: 1.25rem;">
                <div style="font-size: 0.82rem; color: #7F1D1D; line-height: 1.5;">
                    <strong>Angalizo:</strong> Kitendo hiki kitafuta miamala yote ya majaribio (transactions), matumizi (expenses), na taarifa za mfumo ili dashibodi yako isomwe <strong>Tsh 00</strong>. 
                    MikroTik routers zako, Vifurushi vya bei, Maeneo (sites), na akaunti za kuingilia <strong>HAZITAFUTWA</strong>.
                </div>
            </div>

            <form action="{{ route('admin.settings.reset_sales') }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kuweka upya takwimu za mauzo? Miamala yote itafutwa na dashibodi itasoma Tsh 00 ili uanze upya rasmi.');">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; user-select: none;">
                        <input type="checkbox" name="include_vouchers" value="1" style="width: 17px; height: 17px; accent-color: #DC2626; cursor: pointer;">
                        <span style="font-size: 0.84rem; font-weight: 600; color: var(--text-main);">
                            Futa pia vocha zote za majaribio na mabachi yaliyopo stoo (Vouchers &amp; Batches)
                        </span>
                    </label>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn-banking" style="background: #DC2626; color: #ffffff; border: none; padding: 0.65rem 1.35rem; border-radius: 10px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: opacity 0.2s;">
                            <i class="fas fa-redo-alt"></i> Weka Upya Dashibodi (Soma Tsh 00)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
