@extends('layouts.app')

@section('title', $customer->name ?? 'Customer Profile')

@section('content')

{{-- Breadcrumb --}}
<div style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">
    <a href="{{ route('admin.customers.index') }}" style="color: var(--blue-primary); text-decoration: none;">Customers</a>
    <i class="fas fa-chevron-right" style="font-size: 0.65rem; color: var(--border-color);"></i>
    <span style="color: var(--text-main);">{{ $customer->name }}</span>
</div>

{{-- Flash --}}
@if(session('success'))
<div style="background: var(--success-light); border: 1px solid var(--success-border); color: var(--success); padding: 0.9rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.65rem; font-weight: 600; font-size: 0.88rem;">
    <i class="fas fa-check-circle" style="font-size: 1.1rem;"></i> {{ session('success') }}
</div>
@endif

{{-- Profile Header Card --}}
<div class="banking-card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.25rem;">
        {{-- Avatar + Info --}}
        <div style="display: flex; align-items: flex-start; gap: 1.2rem;">
            <div style="width: 64px; height: 64px; border-radius: 16px; background: var(--blue-light); color: var(--blue-primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.6rem; flex-shrink: 0; border: 1px solid var(--blue-subtle);">
                {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <h2 style="margin: 0; font-size: 1.45rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.4px;">{{ $customer->name ?? '—' }}</h2>
                    <span class="badge {{ $customer->status === 'active' ? 'success' : ($customer->status === 'suspended' ? 'danger' : 'warning') }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                    @if($stats['is_online'])
                    <span class="system-status-pill">
                        <span class="live-pulse-dot"></span> Online Sasa
                    </span>
                    @endif
                </div>
                <div style="margin-top: 0.6rem; display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <span style="color: var(--text-muted); font-size: 0.86rem; display: flex; align-items: center; gap: 0.4rem; font-weight: 600;">
                        <i class="fas fa-phone" style="color: var(--blue-primary); font-size: 0.75rem;"></i> {{ $customer->phone_number }}
                    </span>
                    @if($customer->email)
                    <span style="color: var(--text-muted); font-size: 0.86rem; display: flex; align-items: center; gap: 0.4rem; font-weight: 500;">
                        <i class="fas fa-envelope" style="color: var(--text-muted); font-size: 0.75rem;"></i> {{ $customer->email }}
                    </span>
                    @endif
                    <span style="color: var(--text-muted); font-size: 0.86rem; display: flex; align-items: center; gap: 0.4rem; font-weight: 500;">
                        <i class="fas fa-calendar" style="color: var(--text-muted); font-size: 0.75rem;"></i> Amejiunga {{ $customer->created_at->format('d M Y') }}
                    </span>
                </div>
                @if($customer->notes)
                <div style="margin-top: 0.65rem; font-size: 0.84rem; color: var(--text-secondary); background: var(--surface-soft); padding: 0.4rem 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: inline-block;">
                    <i class="fas fa-sticky-note" style="color: var(--warning); margin-right: 0.35rem;"></i>{{ $customer->notes }}
                </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-edit"></i> Hariri
            </a>
            @if($customer->status !== 'suspended')
            <form action="{{ route('admin.customers.suspend', $customer) }}" method="POST" style="display: inline;" onsubmit="return confirm('Sitisha mteja huyu?');">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: var(--danger-border); background: var(--danger-light);">
                    <i class="fas fa-ban"></i> Sitisha
                </button>
            </form>
            @else
            <form action="{{ route('admin.customers.activate', $customer) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-check"></i> Washa (Activate)
                </button>
            </form>
            @endif
            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" style="display: inline;"
                  onsubmit="return confirm('Je, una uhakika wa kufuta mteja {{ addslashes($customer->name) }}? Data ya malipo na miamala itabaki.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--text-muted);">
                    <i class="fas fa-trash"></i> Futa
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Profile Stats Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem;">
    @php
        $profileStats = [
            ['label'=>'Jumla ya Malipo', 'value'=>'TZS '.number_format($stats['total_spent']),   'color'=>'var(--success)',      'bg'=>'var(--success-light)', 'icon'=>'fa-money-bill-wave'],
            ['label'=>'Jumla ya Sessions','value'=>$stats['total_sessions'],                       'color'=>'var(--blue-primary)', 'bg'=>'var(--blue-light)',   'icon'=>'fa-history'],
            ['label'=>'Data Aliyotumia',  'value'=>number_format($stats['data_used_mb'],1).' MB',   'color'=>'var(--navy-primary)', 'bg'=>'#F1F5F9',              'icon'=>'fa-database'],
            ['label'=>'WiFi Accounts',    'value'=>$customer->wifiUsers->count(),                  'color'=>'var(--warning)',      'bg'=>'var(--warning-light)', 'icon'=>'fa-wifi'],
        ];
    @endphp
    @foreach($profileStats as $s)
    <div style="background: #FFFFFF; border-radius: var(--radius-md); padding: 0.9rem 1rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.75rem; box-shadow: var(--shadow-xs);">
        <div style="width: 38px; height: 38px; border-radius: 10px; background: {{ $s['bg'] }}; color: {{ $s['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
            <i class="fas {{ $s['icon'] }}"></i>
        </div>
        <div style="min-width:0;">
            <div style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); line-height: 1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $s['value'] }}</div>
            <div style="font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px; margin-top: 2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Ledger Card (Deni na Malipo) --}}
<div class="banking-card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
    <div class="banking-card-header" style="padding: 1.1rem 1.4rem; margin-bottom: 0; background: var(--surface-soft); flex-wrap: wrap; gap: 0.85rem; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h3 class="card-title" style="margin:0; font-size:1rem;">
                <i class="fas fa-book-open" style="color: var(--warning); font-size: 0.95rem;"></i> Daftari la Deni &amp; Malipo (Ledger)
            </h3>
            <div style="display: flex; gap: 1.2rem; margin-top: 0.5rem; flex-wrap: wrap; align-items: baseline;">
                <div>
                    <span style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px;">Salio la Deni</span>
                    <div style="font-size: 1.25rem; font-weight: 800; color: {{ $customer->ledger_balance > 0 ? 'var(--danger)' : 'var(--success)' }};">
                        TZS {{ number_format($customer->ledger_balance) }}
                    </div>
                </div>
                <div>
                    <span style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px;">Jumla ya Deni</span>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">TZS {{ number_format($customer->total_debt) }}</div>
                </div>
                <div>
                    <span style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px;">Iliyolipwa</span>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">TZS {{ number_format($customer->total_paid_ledger) }}</div>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button onclick="document.getElementById('addDebtModal').style.display='flex'" class="btn-banking btn-danger" style="font-size:0.8rem; padding:0.4rem 0.8rem;">
                <i class="fas fa-plus-circle"></i> Ongeza Deni
            </button>
            <button onclick="document.getElementById('addPaymentModal').style.display='flex'" class="btn-banking btn-primary" style="font-size:0.8rem; padding:0.4rem 0.8rem;">
                <i class="fas fa-hand-holding-usd"></i> Sajili Malipo
            </button>
        </div>
    </div>
    
    <div class="table-responsive" style="border: none; border-radius: 0; box-shadow: none;">
        <table class="table-banking" style="margin:0; width:100%;">
            <thead>
                <tr>
                    <th>Tarehe</th>
                    <th>Aina</th>
                    <th>Kiasi</th>
                    <th>Maelezo</th>
                    <th>Aliyeweka</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->ledgers as $ledger)
                <tr>
                    <td style="color: var(--text-main); font-weight: 600; font-size: 0.82rem; white-space:nowrap;">{{ $ledger->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <span class="badge-status {{ $ledger->type === 'debt' ? 'badge-danger' : 'badge-success' }}" style="font-size:0.72rem;">
                            {{ $ledger->type === 'debt' ? 'Deni' : 'Malipo' }}
                        </span>
                    </td>
                    <td style="font-weight: 800; color: {{ $ledger->type === 'debt' ? 'var(--danger)' : 'var(--success)' }}; white-space:nowrap;">
                        {{ $ledger->type === 'debt' ? '+' : '-' }} TZS {{ number_format($ledger->amount) }}
                    </td>
                    <td style="color: var(--text-muted); font-size: 0.84rem;">{{ $ledger->description }}</td>
                    <td style="color: var(--text-muted); font-size: 0.82rem; white-space:nowrap;">{{ optional($ledger->creator)->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:2rem 1rem;">
                        <div class="empty-state">
                            <div class="empty-state-title" style="font-size: 0.95rem; font-weight:700; color:var(--text-main);">Hakuna kumbukumbu za madeni au malipo</div>
                            <div class="empty-state-desc" style="font-size: 0.8rem; color:var(--text-muted);">Tumia vitufe hapo juu kuongeza deni au kusajili malipo ya awamu.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Two Column Layout: Subscriptions & Payment History --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;" class="customer-sub-grid">
    {{-- Subscription History --}}
    <div class="banking-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div class="banking-card-header" style="padding: 1.15rem 1.4rem; margin-bottom: 0; background: var(--surface-soft);">
            <h3 class="card-title">
                <i class="fas fa-history" style="color: var(--blue-primary); font-size: 1rem;"></i> Historia ya Vifurushi (Subscriptions)
            </h3>
        </div>
        <div style="overflow-y: auto; max-height: 320px;">
            @forelse($customer->subscriptions as $sub)
            <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                <div>
                    <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-main);">{{ optional($sub->package)->name ?? '—' }}</div>
                    <div style="font-size: 0.76rem; color: var(--text-muted); margin-top: 2px;">
                        {{ $sub->starts_at ? $sub->starts_at->format('d M Y') : '—' }}
                        @if($sub->expires_at) &rarr; {{ $sub->expires_at->format('d M Y') }} @endif
                    </div>
                </div>
                <span class="badge {{ $sub->status === 'active' ? 'success' : 'neutral' }}">
                    {{ ucfirst($sub->status) }}
                </span>
            </div>
            @empty
            <div class="empty-state" style="padding: 2.5rem 1rem;">
                <div class="empty-state-desc">Hakuna subscription history</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Payment History --}}
    <div class="banking-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div class="banking-card-header" style="padding: 1.15rem 1.4rem; margin-bottom: 0; background: var(--surface-soft);">
            <h3 class="card-title">
                <i class="fas fa-receipt" style="color: var(--success); font-size: 1rem;"></i> Historia ya Malipo (Transactions)
            </h3>
        </div>
        <div style="overflow-y: auto; max-height: 320px;">
            @forelse($customer->transactions->sortByDesc('created_at') as $txn)
            <div style="padding: 0.9rem 1.25rem; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-weight: 800; font-size: 0.92rem; color: var(--success);">TZS {{ number_format($txn->amount) }}</div>
                    <div style="font-size: 0.74rem; color: var(--text-muted); font-family: monospace; margin-top: 1px;">{{ $txn->transaction_reference }}</div>
                    <div style="font-size: 0.76rem; color: var(--text-secondary); margin-top: 2px;">{{ optional($txn->package)->name ?? '—' }}</div>
                </div>
                <div style="text-align: right;">
                    <span class="badge {{ $txn->status === 'success' ? 'success' : ($txn->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($txn->status) }}
                    </span>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px;">{{ $txn->created_at->format('d M Y') }}</div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding: 2.5rem 1rem;">
                <div class="empty-state-desc">Hakuna malipo yaliyofanyika bado</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modals for Ledger --}}
<!-- Add Debt Modal -->
<div id="addDebtModal" class="mobile-modal-backdrop" onclick="if(event.target===this) this.style.display='none'">
    <div class="mobile-modal-dialog">
        <div style="padding:1.2rem 1.4rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; background:#FAFAFC;">
            <h3 style="margin: 0; color: var(--danger); font-size: 1.05rem; font-weight: 700; display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-plus-circle"></i> Ongeza Deni Jipya
            </h3>
            <button type="button" onclick="document.getElementById('addDebtModal').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
        </div>
        <form action="{{ route('admin.customers.debt', $customer) }}" method="POST" style="padding:1.4rem;">
            @csrf
            <div style="margin-bottom:1.1rem;">
                <label class="form-label" style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:600; color:var(--text-main);">Kiasi cha Deni (TZS) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="amount" required min="1" class="form-control" placeholder="Mfano: 5000" style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
            </div>
            <div style="margin-bottom:1.4rem;">
                <label class="form-label" style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:600; color:var(--text-main);">Maelezo (Hiari)</label>
                <input type="text" name="description" placeholder="Mfano: Vocha ya wiki moja" class="form-control" style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.65rem;">
                <button type="button" onclick="document.getElementById('addDebtModal').style.display='none'" class="btn-banking btn-secondary" style="flex:1; max-width:120px; justify-content:center;">Ghairi</button>
                <button type="submit" class="btn-banking btn-danger" style="flex:2; justify-content:center;">Ongeza Deni</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Payment Modal -->
<div id="addPaymentModal" class="mobile-modal-backdrop" onclick="if(event.target===this) this.style.display='none'">
    <div class="mobile-modal-dialog">
        <div style="padding:1.2rem 1.4rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; background:#FAFAFC;">
            <h3 style="margin: 0; color: var(--success); font-size: 1.05rem; font-weight: 700; display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-hand-holding-usd"></i> Sajili Malipo ya Awamu
            </h3>
            <button type="button" onclick="document.getElementById('addPaymentModal').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
        </div>
        <form action="{{ route('admin.customers.payment', $customer) }}" method="POST" style="padding:1.4rem;">
            @csrf
            <div style="margin-bottom:1.1rem;">
                <label class="form-label" style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:600; color:var(--text-main);">Kiasi Kilicholipwa (TZS) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="amount" required min="1" class="form-control" placeholder="Mfano: 5000" style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
            </div>
            <div style="margin-bottom:1.4rem;">
                <label class="form-label" style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:600; color:var(--text-main);">Maelezo (Hiari)</label>
                <input type="text" name="description" placeholder="Mfano: Malipo ya awamu ya kwanza" class="form-control" style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.65rem;">
                <button type="button" onclick="document.getElementById('addPaymentModal').style.display='none'" class="btn-banking btn-secondary" style="flex:1; max-width:120px; justify-content:center;">Ghairi</button>
                <button type="submit" class="btn-banking btn-primary" style="flex:2; justify-content:center;">Sajili Malipo</button>
            </div>
        </form>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .customer-sub-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

@endsection
