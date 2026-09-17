@extends('layouts.app')

@section('title', 'Vocha')

@section('content')
<style>
    /* Vouchers Module Styling */
    .vouchers-wrapper {
        width: 100%;
        margin: 0 auto;
        padding-bottom: 5rem;
    }

    .vouchers-top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 0.75rem;
    }

    .vouchers-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .vouchers-header-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(255, 51, 0, 0.1);
        color: #FF3300;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .vouchers-header-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.2;
    }

    .vouchers-header-sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0.15rem 0 0 0;
    }

    /* KPI Grid */
    .vouchers-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.15rem;
    }

    .voucher-kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: var(--shadow-xs);
        min-width: 0;
    }

    .voucher-kpi-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .voucher-kpi-icon.orange { background: rgba(255, 51, 0, 0.1); color: #FF3300; }
    .voucher-kpi-icon.indigo { background: rgba(99, 102, 241, 0.1); color: #6366F1; }
    .voucher-kpi-icon.green  { background: rgba(16, 185, 129, 0.1); color: #10B981; }

    .voucher-kpi-number {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    .voucher-kpi-label {
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-top: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Generator Box */
    .voucher-gen-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.15rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-xs);
    }

    .gen-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.85rem;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .gen-card-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin: 0;
    }

    .gen-card-title i {
        color: #FF3300;
    }

    .gen-a4-pill {
        font-size: 0.72rem;
        font-weight: 700;
        color: #0F172A;
        background: #F8FAFC;
        border: 1px solid var(--border-color);
        padding: 0.18rem 0.55rem;
        border-radius: 8px;
    }

    .creation-input {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        font-size: 0.88rem;
        border: 1px solid var(--border-color);
        padding: 0 0.8rem;
        background: #FFFFFF;
        color: var(--text-main);
        outline: none;
        transition: border-color 0.15s ease;
    }

    .creation-input:focus {
        border-color: #FF3300;
        box-shadow: 0 0 0 3px rgba(255, 51, 0, 0.1);
    }

    .font-mono {
        font-family: monospace;
        font-weight: 800;
    }

    .preset-pill-group {
        display: flex;
        gap: 0.35rem;
        margin-top: 0.45rem;
        flex-wrap: wrap;
    }

    .qty-preset-btn {
        background: #F8FAFC;
        border: 1px solid var(--border-color);
        border-radius: 7px;
        padding: 0.25rem 0.55rem;
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--text-main);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .qty-preset-btn:hover {
        background: rgba(255, 51, 0, 0.08);
        border-color: #FF3300;
        color: #FF3300;
    }

    .btn-create-batch {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.65rem 1.25rem;
        background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%);
        color: #FFFFFF !important;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 800;
        border: none;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(255, 51, 0, 0.28);
        transition: all 0.2s ease;
        width: 100%;
        min-height: 42px;
    }

    .btn-create-batch:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(255, 51, 0, 0.38);
    }

    /* Success / Ready Cards */
    .app-toast-card {
        background: #FFFFFF;
        border: 1px solid #A7F3D0;
        border-left: 4px solid #10B981;
        border-radius: 14px;
        padding: 0.95rem 1.25rem;
        margin-bottom: 1.15rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        box-shadow: 0 4px 18px rgba(16, 185, 129, 0.12);
        animation: toastSlideDown 0.3s ease-out;
    }

    .app-toast-card.app-toast-error {
        border-color: #FECACA;
        border-left-color: #DC2626;
        box-shadow: 0 4px 18px rgba(220, 38, 38, 0.12);
    }

    @keyframes toastSlideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .toast-left {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-width: 0;
    }

    .toast-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.14);
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .toast-icon.error {
        background: rgba(220, 38, 38, 0.12);
        color: #DC2626;
    }

    .toast-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #065F46;
        letter-spacing: -0.01em;
    }

    .toast-subtitle {
        font-size: 0.78rem;
        color: #047857;
        margin-top: 1px;
    }

    .toast-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .toast-print-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 1.15rem;
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: #FFFFFF !important;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .toast-print-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45);
    }

    .toast-close-btn {
        background: transparent;
        border: none;
        color: #9CA3AF;
        font-size: 1rem;
        padding: 0.35rem 0.5rem;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s;
    }

    .toast-close-btn:hover {
        color: #4B5563;
    }

    /* Single Voucher Result Card (Refined & Modern Mobile-First UI) */
    .single-voucher-box {
        background: #FFFFFF;
        border: 1px solid #BBF7D0;
        border-left: 4px solid #10B981;
        border-radius: 16px;
        padding: 1.15rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.12);
        animation: toastSlideDown 0.3s ease-out;
    }

    .single-voucher-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.85rem;
    }

    .single-voucher-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: #065F46;
        font-weight: 800;
        font-size: 0.95rem;
        margin-bottom: 0.2rem;
    }

    .single-voucher-pkg-desc {
        font-size: 0.78rem;
        color: #047857;
        font-weight: 600;
    }

    /* Ticket PIN Box */
    .voucher-ticket-preview {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.85rem;
        gap: 0.75rem;
    }

    .voucher-ticket-info {
        display: flex;
        flex-direction: column;
    }

    .voucher-ticket-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-bottom: 0.15rem;
    }

    .single-pin-display {
        font-family: monospace;
        font-size: 1.5rem;
        font-weight: 900;
        color: #0F172A;
        letter-spacing: 0.08em;
        line-height: 1.1;
    }

    .btn-copy-pin {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.85rem;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        color: #334155;
        border-radius: 9px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }

    .btn-copy-pin:hover {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #94A3B8;
    }

    /* Unified SMS Dispatch Bar */
    .voucher-sms-dispatch-bar {
        display: flex;
        align-items: stretch;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.75rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .voucher-sms-dispatch-bar:focus-within {
        border-color: #2563EB;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .sms-input-prefix {
        display: flex;
        align-items: center;
        padding-left: 0.85rem;
        color: #94A3B8;
        font-size: 0.88rem;
    }

    .sms-phone-field {
        flex: 1;
        min-width: 0;
        border: none;
        outline: none;
        padding: 0.65rem 0.75rem;
        font-size: 0.88rem;
        color: var(--text-main);
        background: transparent;
    }

    .sms-phone-field::placeholder {
        color: #94A3B8;
        font-size: 0.82rem;
    }

    .btn-submit-sms {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0 1.15rem;
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        color: #FFFFFF !important;
        border: none;
        font-size: 0.82rem;
        font-weight: 800;
        cursor: pointer;
        transition: background 0.15s ease, opacity 0.15s ease;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .btn-submit-sms:hover {
        opacity: 0.95;
    }

    /* Secondary 2-Column Action Grid */
    .voucher-aux-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.55rem;
    }

    .btn-aux-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.55rem 0.75rem;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        color: #334155;
        border-radius: 9px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none !important;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .btn-aux-action:hover {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #CBD5E1;
    }

    .btn-aux-action i {
        font-size: 0.85rem;
        color: #64748B;
    }

    /* History Table */
    .vouchers-main-panel {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-xs);
        overflow: hidden;
    }

    .vouchers-panel-header {
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid var(--border-color);
        background: #FAFAFC;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .vouchers-panel-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin: 0;
    }

    .vouchers-panel-title i {
        color: #6366F1;
    }

    .voucher-mobile-card {
        padding: 1rem 1.05rem;
        border-bottom: 1px solid var(--border-color);
        background: #FFFFFF;
    }

    .voucher-mobile-card:last-child {
        border-bottom: none;
    }

    .btn-print-voucher {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.42rem 0.85rem;
        background: #F0FDF4;
        border: 1px solid #BBF7D0;
        color: #166534;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-print-voucher:hover {
        background: #DCFCE7;
        color: #14532D;
    }

    .btn-sms-voucher {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.42rem 0.8rem;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        color: #1D4ED8;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-sms-voucher:hover {
        background: #DBEAFE;
        color: #1E40AF;
    }

    @media (max-width: 768px) {
        .vouchers-wrapper {
            padding-bottom: 5rem !important;
        }
        .vouchers-header-sub {
            display: none !important;
        }
        .vouchers-header-title {
            font-size: 1.15rem !important;
        }
        .vouchers-kpi-grid {
            gap: 0.45rem !important;
            margin-bottom: 0.75rem !important;
        }
        .voucher-kpi-card {
            padding: 0.65rem 0.55rem !important;
            border-radius: 12px !important;
            gap: 0.45rem !important;
            min-width: 0 !important;
        }
        .voucher-kpi-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
            flex-shrink: 0 !important;
        }
        .voucher-kpi-number {
            font-size: 1.1rem !important;
            font-weight: 800 !important;
            line-height: 1.1 !important;
        }
        .voucher-kpi-label {
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            white-space: nowrap !important;
        }
    }
</style>

<div class="vouchers-wrapper">

    {{-- Top Header --}}
    <div class="vouchers-top-header">
        <div class="vouchers-title-wrap">
            <div class="vouchers-header-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div>
                <h1 class="vouchers-header-title">Vocha</h1>
                <p class="vouchers-header-sub">Tengeneza na chapisha vocha za mtandao</p>
            </div>
        </div>
    </div>

    {{-- Success: Single Voucher Generated (with SMS action) --}}
    @if(session('voucher_success') && session('voucher_quantity') == 1 && session('single_code'))
    <div class="single-voucher-box" id="singleVoucherBox">
        {{-- Card Header --}}
        <div class="single-voucher-top">
            <div>
                <div class="single-voucher-badge">
                    <i class="fas fa-check-circle" style="color: #10B981;"></i>
                    <span>Vocha 1 Iko Tayari!</span>
                </div>
                <div class="single-voucher-pkg-desc">
                    {{ session('package_name') }} &bull; TSh {{ number_format(session('package_price', 0)) }}
                </div>
            </div>
            <button type="button" onclick="document.getElementById('singleVoucherBox').remove()" class="toast-close-btn" title="Funga">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Ticket PIN Box --}}
        <div class="voucher-ticket-preview">
            <div class="voucher-ticket-info">
                <span class="voucher-ticket-label">PIN ya Vocha</span>
                <span class="single-pin-display">{{ session('single_code') }}</span>
            </div>
            <button type="button" onclick="copyPin('{{ session('single_code') }}', this)" class="btn-copy-pin">
                <i class="fas fa-copy"></i>
                <span>Kopi PIN</span>
            </button>
        </div>

        {{-- Unified SMS Dispatch Bar (Phone Input + Tuma SMS Button in one seamless bar) --}}
        <div class="voucher-sms-dispatch-bar">
            <div class="sms-input-prefix">
                <i class="fas fa-phone-alt"></i>
            </div>
            <input type="tel" id="singleSmsPhone" placeholder="Namba ya mteja (mf. 07XXXXXXXX)..." class="sms-phone-field">
            <button type="button" onclick="sendVoucherSmsFromInput('{{ session('single_code') }}', '{{ addslashes(session('package_name', 'Wi-Fi')) }}', '{{ session('package_price', 0) }}', 'singleSmsPhone')" class="btn-submit-sms">
                <i class="fas fa-paper-plane"></i>
                <span>Tuma SMS</span>
            </button>
        </div>

        {{-- Secondary Action Buttons (50% / 50% Clean Row) --}}
        <div class="voucher-aux-actions-grid">
            <button type="button" onclick="copySmsText('{{ session('single_code') }}', '{{ addslashes(session('package_name', 'Wi-Fi')) }}', '{{ session('package_price', 0) }}', this)" class="btn-aux-action">
                <i class="fas fa-comment-dots"></i>
                <span>Nakili Ujumbe</span>
            </button>
            @if(session('batch_id'))
            <a href="{{ route('admin.vouchers.print', session('batch_id')) }}" target="_blank" class="btn-aux-action">
                <i class="fas fa-print"></i>
                <span>Chapisha (PDF)</span>
            </a>
            @else
            <button type="button" onclick="window.print()" class="btn-aux-action">
                <i class="fas fa-print"></i>
                <span>Chapisha</span>
            </button>
            @endif
        </div>
    </div>
    @elseif(session('voucher_success') && session('voucher_quantity') > 1)
    {{-- Success: Batch Generated (Strictly for Printing) --}}
    <div class="app-toast-card" id="batchPrintToast">
        <div class="toast-left">
            <div class="toast-icon">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <div class="toast-title">
                    Kundi la Vocha {{ session('voucher_quantity') }} Liko Tayari!
                </div>
                <div class="toast-subtitle">
                    Kifurushi: {{ session('package_name') }} &bull; Ziko tayari kuchapishwa kwenye karatasi ya A4.
                </div>
            </div>
        </div>
        <div class="toast-actions">
            @if(session('batch_id'))
            <a href="{{ route('admin.vouchers.print', session('batch_id')) }}" target="_blank" class="toast-print-btn">
                <i class="fas fa-print"></i> Chapisha A4 (PDF)
            </a>
            @endif
            <button type="button" onclick="document.getElementById('batchPrintToast').remove()" class="toast-close-btn" title="Funga">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- Error Banner --}}
    @if(session('error'))
    <div class="app-toast-card app-toast-error" id="voucherErrorToast">
        <div class="toast-left">
            <div class="toast-icon error">
                <i class="fas fa-exclamation"></i>
            </div>
            <div>
                <div class="toast-title" style="color: #991B1B;">Hitilafu</div>
                <div class="toast-subtitle" style="color: #B91C1C;">{{ session('error') }}</div>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('voucherErrorToast').remove()" class="toast-close-btn" title="Funga">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    {{-- KPI Overview Grid --}}
    <div class="vouchers-kpi-grid">
        <div class="voucher-kpi-card">
            <div class="voucher-kpi-icon orange">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div class="voucher-kpi-number">{{ $batches->total() }}</div>
                <div class="voucher-kpi-label">Batches</div>
            </div>
        </div>

        <div class="voucher-kpi-card">
            <div class="voucher-kpi-icon indigo">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div>
                <div class="voucher-kpi-number">{{ $batches->sum('quantity') }}</div>
                <div class="voucher-kpi-label">Vocha</div>
            </div>
        </div>

        <div class="voucher-kpi-card">
            <div class="voucher-kpi-icon green">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div>
                <div class="voucher-kpi-number">{{ ceil($batches->sum('quantity') / 18) }}</div>
                <div class="voucher-kpi-label">Karatasi</div>
            </div>
        </div>
    </div>

    {{-- Single Unified Generator Form --}}
    <div class="voucher-gen-card">
        <div class="gen-card-header">
            <h2 class="gen-card-title">
                <i class="fas fa-magic"></i>
                <span>Tengeneza Vocha</span>
            </h2>
            <span class="gen-a4-pill" id="a4CalculationPill">1 Karatasi ya A4 = Kadi 18</span>
        </div>

        <form action="{{ route('admin.vouchers.generateBatch') }}" method="POST">
            @csrf
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:0.85rem; align-items:flex-end;">
                <div>
                    <label style="display:block; margin-bottom:0.35rem; font-size:0.78rem; font-weight:700; color:var(--text-main);">
                        Kifurushi (Package) <span style="color:var(--danger);">*</span>
                    </label>
                    <select name="package_id" required class="creation-input">
                        <option value="">-- Chagua Kifurushi --</option>
                        @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}">{{ $pkg->name }} — TSh {{ number_format($pkg->price) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.35rem;">
                        <label style="margin:0; font-size:0.78rem; font-weight:700; color:var(--text-main);">
                            Idadi ya Vocha <span style="color:var(--danger);">*</span>
                        </label>
                        <span style="font-size:0.7rem; color:var(--text-muted);">Weka idadi (1–500)</span>
                    </div>

                    <input type="number" id="voucherQuantity" name="quantity" min="1" max="500" value="18" required class="creation-input font-mono" oninput="updateA4Calc()">

                    <div class="preset-pill-group">
                        <button type="button" onclick="setQty(1)" class="qty-preset-btn" style="color:#2563EB; font-weight:800;">1 (SMS)</button>
                        <button type="button" onclick="setQty(18)" class="qty-preset-btn" style="color:#FF3300; font-weight:800;">18 (A4 1)</button>
                        <button type="button" onclick="setQty(36)" class="qty-preset-btn">36 (A4 2)</button>
                        <button type="button" onclick="setQty(54)" class="qty-preset-btn">54 (A4 3)</button>
                        <button type="button" onclick="setQty(100)" class="qty-preset-btn">100</button>
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn-create-batch">
                        <i class="fas fa-magic"></i>
                        <span>Tengeneza Vocha</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Batches History Panel --}}
    <div class="vouchers-main-panel">
        <div class="vouchers-panel-header">
            <h2 class="vouchers-panel-title">
                <i class="fas fa-history"></i>
                <span>Historia ya Batches za Vocha</span>
            </h2>
            <span class="badge-status badge-secondary" style="font-size:0.74rem;">{{ $batches->total() }} batches</span>
        </div>

        {{-- Desktop Table View --}}
        <div class="table-responsive d-none d-md-block" style="margin:0;">
            <table class="table-banking">
                <thead>
                    <tr>
                        <th>Batch Ref</th>
                        <th>Tarehe</th>
                        <th>Kifurushi</th>
                        <th style="text-align:center;">Idadi ya Vocha</th>
                        <th style="text-align:right;">Kitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr>
                        <td>
                            <div style="font-weight:800; color:var(--text-main); font-family:monospace; font-size:0.88rem;">
                                {{ $batch->batch_reference }}
                            </div>
                        </td>
                        <td>
                            <span style="font-size:0.82rem; color:var(--text-muted);">{{ $batch->created_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td>
                            <span class="badge-status badge-info" style="font-size:0.8rem;">
                                {{ $batch->package ? $batch->package->name : 'N/A' }}
                            </span>
                        </td>
                        <td style="text-align:center; font-weight:800; color:var(--text-main); font-size:0.92rem;">
                            {{ $batch->quantity }}
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:0.4rem; justify-content:flex-end;">
                                @if($batch->quantity == 1)
                                <button type="button" onclick="openSingleBatchSmsModal('{{ $batch->batch_reference }}')" class="btn-sms-voucher" title="Tuma kwa SMS">
                                    <i class="fas fa-sms"></i>
                                    <span>SMS</span>
                                </button>
                                @endif
                                <a href="{{ route('admin.vouchers.print', $batch->batch_reference) }}" target="_blank" class="btn-print-voucher" title="Chapisha PDF">
                                    <i class="fas fa-print"></i>
                                    <span>Print PDF</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:3.5rem 1rem;">
                            <div style="color:var(--text-muted);">
                                <i class="fas fa-ticket-alt" style="font-size:2.6rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                                <div style="font-weight:800; font-size:1.05rem; color:var(--text-main); margin-bottom:0.3rem;">Hakuna Batches za Vocha Bado</div>
                                <div style="font-size:0.85rem;">Tengeneza kundi lako la kwanza kwa kutumia fomu hapo juu.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card Feed --}}
        <div class="d-md-none">
            @forelse($batches as $batch)
            <div class="voucher-mobile-card">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                    <div>
                        <div style="font-weight:800; font-family:monospace; font-size:0.92rem; color:var(--text-main);">
                            {{ $batch->batch_reference }}
                        </div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                            <i class="fas fa-clock" style="margin-right:3px;"></i> {{ $batch->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                    <span class="badge-status badge-secondary" style="font-size:0.75rem; font-weight:800;">
                        {{ $batch->quantity }} Vocha
                    </span>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #F1F5F9; padding-top:0.55rem; margin-top:0.4rem;">
                    <span class="badge-status badge-info" style="font-size:0.75rem;">
                        {{ $batch->package ? $batch->package->name : 'N/A' }}
                    </span>
                    <div style="display:flex; gap:0.4rem;">
                        @if($batch->quantity == 1)
                        <button type="button" onclick="openSingleBatchSmsModal('{{ $batch->batch_reference }}')" class="btn-sms-voucher">
                            <i class="fas fa-sms"></i>
                            <span>SMS</span>
                        </button>
                        @endif
                        <a href="{{ route('admin.vouchers.print', $batch->batch_reference) }}" target="_blank" class="btn-print-voucher">
                            <i class="fas fa-print"></i>
                            <span>Print PDF</span>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:2.5rem 1rem; text-align:center; color:var(--text-muted);">
                <i class="fas fa-ticket-alt" style="font-size:2.2rem; opacity:0.3; margin-bottom:0.75rem; display:block;"></i>
                <div style="font-weight:800; font-size:0.95rem; color:var(--text-main);">Hakuna Batches Bado</div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($batches->hasPages())
        <div style="padding:0.85rem 1.15rem; border-top:1px solid var(--border-color); display:flex; justify-content:center; background:#FAFAFC;">
            {{ $batches->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<script>
function setQty(val) {
    document.getElementById('voucherQuantity').value = val;
    updateA4Calc();
}

function updateA4Calc() {
    const qtyInput = document.getElementById('voucherQuantity');
    const pill = document.getElementById('a4CalculationPill');
    
    let qty = parseInt(qtyInput.value);
    if (isNaN(qty) || qty <= 0) {
        pill.innerText = 'Weka idadi';
        return;
    }
    
    if (qty === 1) {
        pill.innerText = 'Vocha 1 Pekee (SMS / Print)';
    } else {
        const pages = Math.ceil(qty / 18);
        pill.innerText = pages + (pages === 1 ? ' Karatasi ya A4' : ' Karatasi za A4');
    }
}

function formatVoucherSms(pin, pkgName, price) {
    let priceText = '';
    if (price && Number(price) > 0) {
        priceText = ' (TSh ' + Number(price).toLocaleString() + ')';
    }
    return 'Vocha ya Wi-Fi: ' + pin + '\n' +
           'Kifurushi: ' + (pkgName || 'Wi-Fi') + priceText + '\n' +
           'Unganisha Wi-Fi kisha ingiza PIN kuanza. Asante!';
}

function sendVoucherSmsFromInput(pin, pkgName, price, inputId) {
    let phone = '';
    if (inputId) {
        const input = document.getElementById(inputId);
        if (input) phone = input.value.trim();
    }
    sendVoucherSms(pin, pkgName, price, phone);
}

function sendVoucherSms(pin, pkgName, price, phone) {
    const cleanPhone = (phone || '').replace(/[\s\-\+]/g, '');
    const msg = formatVoucherSms(pin, pkgName, price);
    const isIos = /iPhone|iPad|iPod/i.test(navigator.userAgent);
    const separator = isIos ? '&' : '?';
    
    let smsUrl = '';
    if (cleanPhone.length > 0) {
        smsUrl = 'sms:' + encodeURIComponent(cleanPhone) + separator + 'body=' + encodeURIComponent(msg);
    } else {
        smsUrl = 'sms:' + separator + 'body=' + encodeURIComponent(msg);
    }
    
    window.location.href = smsUrl;
}

function copySmsText(pin, pkgName, price, btn) {
    const msg = formatVoucherSms(pin, pkgName, price);
    copyToClipboard(msg, btn, 'Ujumbe wa SMS Umenakiliwa!');
}

function copyPin(pin, btn) {
    copyToClipboard(pin, btn, 'PIN Imenakiliwa!');
}

function copyToClipboard(text, btn, successMsg) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showMiniToast(successMsg || 'Imenakiliwa!');
            if (btn) flashButtonSuccess(btn);
        }).catch(() => fallbackCopy(text, btn, successMsg));
    } else {
        fallbackCopy(text, btn, successMsg);
    }
}

function fallbackCopy(text, btn, successMsg) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showMiniToast(successMsg || 'Imenakiliwa!');
        if (btn) flashButtonSuccess(btn);
    } catch (err) {
        prompt('Nakili hapa:', text);
    }
    document.body.removeChild(textArea);
}

function flashButtonSuccess(btn) {
    if (!btn) return;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check" style="color:#10B981;"></i> Sawa!';
    btn.style.borderColor = '#10B981';
    setTimeout(() => {
        btn.innerHTML = originalHtml;
        btn.style.borderColor = '';
    }, 1800);
}

function showMiniToast(message) {
    let t = document.getElementById('miniToastPopup');
    if (!t) {
        t = document.createElement('div');
        t.id = 'miniToastPopup';
        t.style.position = 'fixed';
        t.style.bottom = '80px';
        t.style.right = '24px';
        t.style.background = '#0F172A';
        t.style.color = '#FFFFFF';
        t.style.padding = '10px 18px';
        t.style.borderRadius = '10px';
        t.style.fontSize = '0.82rem';
        t.style.fontWeight = '700';
        t.style.boxShadow = '0 6px 20px rgba(0,0,0,0.25)';
        t.style.zIndex = '999999';
        t.style.transition = 'all 0.25s ease';
        t.style.display = 'flex';
        t.style.alignItems = 'center';
        t.style.gap = '8px';
        document.body.appendChild(t);
    }
    t.innerHTML = '<i class="fas fa-check-circle" style="color: #10B981;"></i> ' + message;
    t.style.opacity = '1';
    t.style.transform = 'translateY(0)';
    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translateY(10px)';
    }, 2500);
}

function openSingleBatchSmsModal(batchRef) {
    fetch('/admin/vouchers/batch/' + encodeURIComponent(batchRef) + '/json', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.vouchers && data.vouchers.length > 0) {
            const v = data.vouchers[0];
            const phone = prompt('Weka namba ya simu ya mteja kutuma SMS:', '');
            if (phone !== null) {
                sendVoucherSms(v.code, data.package_name, data.package_price, phone);
            }
        }
    })
    .catch(err => alert('Hitilafu: ' . err.message));
}
</script>
@endsection
