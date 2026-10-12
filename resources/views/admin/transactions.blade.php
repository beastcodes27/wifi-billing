@extends('layouts.app')

@section('title', 'Miamala')

@section('content')
<div style="padding-bottom: 5rem;">

    {{-- Page Header --}}
    <div class="page-header-compact" style="margin-bottom: 1.25rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">Miamala</h2>
                    <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Kumbukumbu zote za malipo ya vifurushi na vocha</p>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="{{ route('admin.vouchers.index') }}" class="btn-banking btn-secondary mobile-btn-compact" style="text-decoration: none; font-size: 0.82rem; padding: 0.55rem 0.9rem; border-radius: 10px;">
                <i class="fas fa-ticket-alt" style="color: #FF3300;"></i> Vocha
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn-banking btn-secondary mobile-btn-compact" style="text-decoration: none; font-size: 0.82rem; padding: 0.55rem 0.9rem; border-radius: 10px;">
                <i class="fas fa-chart-line" style="color: #3B82F6;"></i> Ripoti
            </a>
        </div>
    </div>

    {{-- Financial KPI Metric Cards (Responsive 2-col on mobile, 4-col on desktop) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(145px, 1fr)); gap: 0.75rem; margin-bottom: 1.25rem;">
        {{-- Mapato Yote --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Mapato Yote</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #059669; line-height: 1.2; letter-spacing: -0.02em;">
                TSh {{ number_format($stats['revenue']) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.3rem;">Jumla ya fedha zilizolipwa</div>
        </div>

        {{-- Mapato ya Leo --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Mapato ya Leo</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255, 51, 0, 0.12); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #FF3300; line-height: 1.2; letter-spacing: -0.02em;">
                TSh {{ number_format($stats['today_rev']) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.3rem;">Miamala ya saa 24 zilizopita</div>
        </div>

        {{-- Miamala Yote --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Miamala Yote</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 23, 42, 0.08); color: #0F172A; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <div style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); line-height: 1.2; letter-spacing: -0.02em;">
                {{ number_format($stats['total']) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.3rem;">Idadi ya majaribio ya malipo</div>
        </div>

        {{-- Iliyofanikiwa --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Iliyofanikiwa</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59, 130, 246, 0.12); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #2563EB; line-height: 1.2; letter-spacing: -0.02em;">
                {{ number_format($stats['success']) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.3rem;">Miamala iliyopokea vocha</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="banking-card" style="padding: 1rem; margin-bottom: 1.25rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color);">
        <form method="GET" action="{{ route('admin.transactions') }}" style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 2 1 220px; position: relative; width: 100%;">
                <i class="fas fa-search" style="position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tafuta kwa simu, ref, vocha au mteja..." class="form-control" style="border-radius: 10px; padding: 0.65rem 0.9rem 0.65rem 2.4rem; font-size: 0.85rem; border: 1px solid var(--border-color); width: 100%;">
            </div>

            <select name="status" class="form-control" style="flex: 1 1 130px; border-radius: 10px; padding: 0.65rem 0.9rem; font-size: 0.85rem; border: 1px solid var(--border-color); background: #FFFFFF;">
                <option value="">Hali Zote (Status)</option>
                <option value="success" {{ ($status ?? '') === 'success' ? 'selected' : '' }}>Successful</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ ($status ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ ($status ?? '') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            <select name="method" class="form-control" style="flex: 1 1 130px; border-radius: 10px; padding: 0.65rem 0.9rem; font-size: 0.85rem; border: 1px solid var(--border-color); background: #FFFFFF;">
                <option value="">Njia Zote za Malipo</option>
                <option value="sonicpesa" {{ ($method ?? '') === 'sonicpesa' ? 'selected' : '' }}>SonicPesa</option>
                <option value="palm_pesa" {{ ($method ?? '') === 'palm_pesa' ? 'selected' : '' }}>Palm Pesa (legacy)</option>
                <option value="mpesa" {{ ($method ?? '') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                <option value="airtel" {{ ($method ?? '') === 'airtel' ? 'selected' : '' }}>Airtel Money</option>
                <option value="halo" {{ ($method ?? '') === 'halo' ? 'selected' : '' }}>HaloPesa</option>
                <option value="cash" {{ ($method ?? '') === 'cash' ? 'selected' : '' }}>Cash / Vocha</option>
            </select>

            <div style="display: flex; gap: 0.5rem; flex: 1 1 160px;">
                <button type="submit" class="btn-banking btn-primary" style="flex: 1; padding: 0.65rem 1.25rem; font-size: 0.85rem; font-weight: 700; border-radius: 10px; background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%); color: #fff; border: none; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                    <i class="fas fa-filter"></i> Chuja
                </button>

                @if(!empty($search) || !empty($status) || !empty($method))
                <a href="{{ route('admin.transactions') }}" class="btn-banking btn-secondary" style="text-decoration: none; padding: 0.65rem 1rem; font-size: 0.85rem; border-radius: 10px; display: flex; align-items: center; justify-content: center;" title="Weka Upya">
                    <i class="fas fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <style>
        .highlighted-txn-row {
            background: #F0FDF4 !important;
            box-shadow: inset 4px 0 0 #10B981;
            position: relative;
        }
        .highlighted-txn-card {
            border: 2px solid #10B981 !important;
            background: #F0FDF4 !important;
            border-radius: 12px;
            margin: 6px 8px;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);
        }
    </style>

    @if(!empty($highlightId))
    <div style="background: #ECFDF5; border: 1.5px solid #10B981; border-radius: 14px; padding: 0.85rem 1.15rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);">
        <div style="display: flex; align-items: center; gap: 0.65rem; color: #065F46; font-size: 0.86rem; font-weight: 700;">
            <div style="width: 28px; height: 28px; border-radius: 50%; background: #10B981; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0;">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <div>Umetoka kwenye Taarifa: Unaangalia muamala huu mahsusi.</div>
                <div style="font-size: 0.74rem; color: #047857; font-weight: 500;">Taarifa imetiwa alama kuwa imesomwa.</div>
            </div>
        </div>
        <a href="{{ route('admin.transactions') }}" class="btn-banking btn-secondary" style="font-size: 0.8rem; padding: 0.45rem 0.95rem; border-radius: 10px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; background: #FFFFFF; border: 1px solid #A7F3D0; color: #065F46;">
            <i class="fas fa-list-ul"></i> Onyesha Miamala Yote
        </a>
    </div>
    @endif

    {{-- Transactions Card Container --}}
    <div class="banking-card p-0" style="overflow: hidden; border-radius: 16px; border: 1px solid var(--border-color); background: #FFFFFF; box-shadow: var(--shadow-xs);">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-list-ul" style="color: #FF3300;"></i>
                <span style="font-weight: 800; font-size: 0.95rem; color: var(--text-main);">Orodha ya Miamala</span>
            </div>
            <span style="background: rgba(15, 23, 42, 0.08); color: var(--text-main); font-weight: 700; font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 20px;">
                {{ number_format($transactions->total()) }} Miamala
            </span>
        </div>

        {{-- Desktop Table View --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table-banking" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Tarehe na Saa</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Kumbukumbu (Ref)</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Mteja / Simu</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Kifurushi &amp; Njia</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: right;">Kiasi</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: center;">Hali</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Vocha ya MikroTik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr class="{{ !empty($highlightId) && $txn->id == $highlightId ? 'highlighted-txn-row' : '' }}" style="border-bottom: 1px solid var(--border-color); transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 0.85rem 1rem; white-space: nowrap;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.86rem;">{{ $txn->created_at->format('d M Y') }}</div>
                            <div style="font-family: monospace; color: var(--text-muted); font-size: 0.74rem; margin-top: 2px;">{{ $txn->created_at->format('H:i:s') }}</div>
                        </td>
                        <td style="padding: 0.85rem 1rem;">
                            <div style="font-family: monospace; font-weight: 700; color: #0F172A; font-size: 0.84rem;">{{ $txn->transaction_reference }}</div>
                            @if($txn->palm_pesa_order_id)
                                <div style="font-family: monospace; font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Ext: {{ $txn->palm_pesa_order_id }}</div>
                            @endif
                        </td>
                        <td style="padding: 0.85rem 1rem;">
                            @if($txn->customer)
                                <a href="{{ route('admin.customers.show', $txn->customer) }}" style="text-decoration: none; font-weight: 700; color: var(--text-main); font-size: 0.88rem; display: block;">
                                    {{ $txn->customer->name }}
                                </a>
                                <div style="font-size: 0.76rem; color: var(--text-muted); font-family: monospace;">{{ $txn->phone_number }}</div>
                            @else
                                <div style="font-weight: 700; color: var(--text-main); font-family: monospace; font-size: 0.85rem;">
                                    {{ $txn->phone_number == 'Batch Print' ? 'Vocha (Batch Print)' : ($txn->phone_number == 'Mteja wa Cash' ? 'Vocha (Moja)' : $txn->phone_number) }}
                                </div>
                                <div style="font-size: 0.72rem; color: var(--text-muted);">Mteja wa Wi-Fi</div>
                            @endif
                        </td>
                        <td style="padding: 0.85rem 1rem;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.86rem;">{{ optional($txn->package)->name ?? 'Kifurushi' }}</div>
                            <div style="margin-top: 3px;">
                                @if($txn->payment_method == 'cash' || in_array($txn->phone_number, ['Batch Print', 'Mteja wa Cash']))
                                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #FEF3C7; color: #B45309; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">
                                        <i class="fas fa-money-bill-wave"></i> Cash / Vocha
                                    </span>
                                @elseif(strtolower($txn->payment_method) == 'mpesa')
                                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #DCFCE7; color: #15803D; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">
                                        <i class="fas fa-mobile-alt"></i> M-Pesa
                                    </span>
                                @elseif(strtolower($txn->payment_method) == 'sonicpesa')
                                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: rgba(255, 51, 0, 0.1); color: #FF3300; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">
                                        <i class="fas fa-bolt"></i> SonicPesa
                                    </span>
                                @elseif(strtolower($txn->payment_method) == 'fimipay')
                                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: rgba(255, 51, 0, 0.1); color: #FF3300; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">
                                        <i class="fas fa-bolt"></i> FimiPay
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #EFF6FF; color: #1D4ED8; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">
                                        <i class="fas fa-globe"></i> {{ strtoupper($txn->payment_method ?? 'Gateway') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 0.85rem 1rem; text-align: right; white-space: nowrap;">
                            <span style="font-weight: 900; font-size: 0.95rem; font-family: monospace; color: {{ $txn->status === 'success' ? '#059669' : 'var(--text-main)' }};">
                                {{ $txn->status === 'success' ? '+' : '' }} TSh {{ number_format($txn->amount) }}
                            </span>
                        </td>
                        <td style="padding: 0.85rem 1rem; text-align: center; white-space: nowrap;">
                            @if($txn->status === 'success')
                                <span class="badge-status badge-success" style="font-size: 0.72rem; padding: 0.25rem 0.65rem;">
                                    <i class="fas fa-check" style="font-size: 0.65rem;"></i> Success
                                </span>
                            @elseif($txn->status === 'pending')
                                <span class="badge-status badge-warning" style="font-size: 0.72rem; padding: 0.25rem 0.65rem;">
                                    <i class="fas fa-clock" style="font-size: 0.65rem;"></i> Pending
                                </span>
                            @else
                                <span class="badge-status badge-danger" style="font-size: 0.72rem; padding: 0.25rem 0.65rem;">
                                    <i class="fas fa-times" style="font-size: 0.65rem;"></i> {{ ucfirst($txn->status) }}
                                </span>
                            @endif
                        </td>
                        <td style="padding: 0.85rem 1rem; white-space: nowrap;">
                            @if($txn->mikrotik_voucher)
                                <span style="background: rgba(255, 51, 0, 0.08); color: #FF3300; padding: 0.3rem 0.65rem; border-radius: 8px; font-family: monospace; font-weight: 800; font-size: 0.82rem; border: 1px solid rgba(255, 51, 0, 0.18); display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <i class="fas fa-ticket-alt" style="font-size: 0.7rem;"></i> {{ $txn->mikrotik_voucher }}
                                </span>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.82rem;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3.5rem 1rem;">
                            <div style="color: var(--text-muted);">
                                <div style="width: 56px; height: 56px; border-radius: 50%; background: #F1F5F9; color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1rem auto;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div style="font-weight: 800; font-size: 1rem; color: var(--text-main); margin-bottom: 0.25rem;">Hakuna Miamala Iliyopatikana</div>
                                <div style="font-size: 0.82rem;">Miamala ya wateja wanaonunua vifurushi itaonekana hapa.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card Feed --}}
        <div class="d-md-none">
            @forelse($transactions as $txn)
            <div class="{{ !empty($highlightId) && $txn->id == $highlightId ? 'highlighted-txn-card' : '' }}" style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.6rem;">
                {{-- Header row: Customer + Amount --}}
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem;">
                    <div style="min-width: 0; flex: 1;">
                        <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            @if($txn->customer)
                                {{ $txn->customer->name }}
                            @else
                                {{ $txn->phone_number == 'Batch Print' ? 'Vocha (Batch Print)' : ($txn->phone_number == 'Mteja wa Cash' ? 'Vocha (Moja)' : $txn->phone_number) }}
                            @endif
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace; margin-top: 2px;">
                            {{ $txn->transaction_reference }}
                        </div>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <div style="font-weight: 900; font-size: 1.05rem; font-family: monospace; color: {{ $txn->status === 'success' ? '#059669' : 'var(--text-main)' }};">
                            {{ $txn->status === 'success' ? '+' : '' }} TSh {{ number_format($txn->amount) }}
                        </div>
                        <div style="margin-top: 3px;">
                            @if($txn->status === 'success')
                                <span class="badge-status badge-success" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">Success</span>
                            @elseif($txn->status === 'pending')
                                <span class="badge-status badge-warning" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">Pending</span>
                            @else
                                <span class="badge-status badge-danger" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">{{ ucfirst($txn->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Middle row: Package & Method Badges --}}
                <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; align-items: center;">
                    <span style="background: #F1F5F9; color: var(--text-main); font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 6px;">
                        <i class="fas fa-box" style="font-size: 0.65rem; color: var(--text-muted); margin-right: 2px;"></i> {{ optional($txn->package)->name ?? 'Kifurushi' }}
                    </span>

                    @if($txn->payment_method == 'cash' || in_array($txn->phone_number, ['Batch Print', 'Mteja wa Cash']))
                        <span style="background: #FEF3C7; color: #B45309; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px;">
                            <i class="fas fa-money-bill-wave"></i> Cash
                        </span>
                    @elseif(strtolower($txn->payment_method) == 'sonicpesa')
                        <span style="background: rgba(255, 51, 0, 0.1); color: #FF3300; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px;">
                            <i class="fas fa-bolt"></i> SonicPesa
                        </span>
                    @elseif(strtolower($txn->payment_method) == 'fimipay')
                        <span style="background: rgba(255, 51, 0, 0.1); color: #FF3300; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px;">
                            <i class="fas fa-bolt"></i> FimiPay (legacy)
                        </span>
                    @else
                        <span style="background: #EFF6FF; color: #1D4ED8; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px;">
                            {{ strtoupper($txn->payment_method ?? 'Online') }}
                        </span>
                    @endif
                </div>

                {{-- Bottom row: Date & Voucher Code --}}
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.45rem; border-top: 1px dashed var(--border-color); font-size: 0.76rem;">
                    <span style="color: var(--text-muted);">
                        <i class="fas fa-clock" style="margin-right: 3px;"></i> {{ $txn->created_at->format('d M, H:i') }}
                    </span>
                    @if($txn->mikrotik_voucher)
                        <span style="font-family: monospace; font-weight: 800; color: #FF3300; background: rgba(255, 51, 0, 0.08); padding: 0.2rem 0.5rem; border-radius: 6px; border: 1px solid rgba(255, 51, 0, 0.15);">
                            Vocha: {{ $txn->mikrotik_voucher }}
                        </span>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <i class="fas fa-receipt" style="font-size: 2.2rem; opacity: 0.3; margin-bottom: 0.75rem; display: block;"></i>
                <div style="font-weight: 700; color: var(--text-main);">Hakuna Miamala</div>
                <div style="font-size: 0.8rem; margin-top: 0.25rem;">Miamala itaonekana hapa punde ikifanyika.</div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; background: #F8FAFC;">
            <div style="color: var(--text-muted); font-size: 0.82rem; font-weight: 600;">
                Inaonyesha <strong>{{ $transactions->firstItem() }}</strong>–<strong>{{ $transactions->lastItem() }}</strong> kati ya <strong>{{ $transactions->total() }}</strong>
            </div>
            <div>
                {{ $transactions->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
