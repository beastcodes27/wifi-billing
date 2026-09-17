@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')

{{-- Page Header --}}
<div class="page-header-compact">
    <div>
        <h2><i class="fas fa-history" style="color:var(--primary-blue);"></i> Subscriptions</h2>
        <p>Vifurushi vya wateja</p>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="banking-card mb-3" style="background:#ECFDF5; border-color:#A7F3D0; color:#065F46; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.6rem; border-radius:10px;">
    <i class="fas fa-check-circle" style="color:#059669; font-size:1rem;"></i>
    <span style="font-weight:600; font-size:0.85rem;">{{ session('success') }}</span>
</div>
@endif

{{-- Stats Cards --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(115px,1fr)); gap:0.5rem; margin-bottom:1rem;">
    @php
        $statCards = [
            ['label'=>'Zote',      'value'=>$stats['total'],     'color'=>'#2563EB', 'bg'=>'rgba(37,99,235,0.1)',   'icon'=>'fa-history'],
            ['label'=>'Active',    'value'=>$stats['active'],    'color'=>'#10b981', 'bg'=>'rgba(16,185,129,0.1)',  'icon'=>'fa-check-circle'],
            ['label'=>'Expired',   'value'=>$stats['expired'],   'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,0.1)',  'icon'=>'fa-clock'],
            ['label'=>'Cancelled', 'value'=>$stats['cancelled'], 'color'=>'#ef4444', 'bg'=>'rgba(239,68,68,0.1)',   'icon'=>'fa-ban'],
        ];
    @endphp
    @foreach($statCards as $card)
    <a href="{{ request()->fullUrlWithQuery(['status' => strtolower($card['label'] === 'Zote' ? '' : $card['label'])]) }}"
       class="banking-card p-2.5"
       style="text-decoration:none; display:flex; align-items:center; gap:0.6rem; border-radius:12px; transition:box-shadow 0.2s;">
        <div style="width:32px; height:32px; border-radius:8px; background:{{ $card['bg'] }}; color:{{ $card['color'] }}; display:flex; align-items:center; justify-content:center; font-size:0.9rem; flex-shrink:0;">
            <i class="fas {{ $card['icon'] }}"></i>
        </div>
        <div style="min-width:0;">
            <div style="font-size:1.15rem; font-weight:800; color:var(--text-main); line-height:1.1;">{{ $card['value'] }}</div>
            <div style="font-size:0.65rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.3px; font-weight:700; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $card['label'] }}</div>
        </div>
    </a>
    @endforeach
</div>

{{-- Search & Filter Bar --}}
<div class="banking-card p-2.5 mb-3" style="border-radius:12px;">
    <form method="GET" action="{{ route('admin.subscriptions.index') }}" style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
        <div style="flex:1 1 200px; position:relative;">
            <i class="fas fa-search" style="position:absolute; left:0.9rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.85rem;"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Tafuta mteja, username, receipt..."
                   class="form-control"
                   style="border-radius:10px; padding:0.65rem 0.9rem 0.65rem 2.4rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
        </div>

        <select name="status" class="form-control" style="border-radius:10px; padding:0.65rem 0.9rem; font-size:0.9rem; border:1px solid var(--border-color); flex:1 1 130px; background:var(--card-bg);">
            <option value="">Status Zote</option>
            <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="expired" {{ ($status ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
            <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        
        <select name="type" class="form-control" style="border-radius:10px; padding:0.65rem 0.9rem; font-size:0.9rem; border:1px solid var(--border-color); flex:1 1 130px; background:var(--card-bg);">
            <option value="">Aina Zote</option>
            <option value="new" {{ ($type ?? '') === 'new' ? 'selected' : '' }}>New</option>
            <option value="renewal" {{ ($type ?? '') === 'renewal' ? 'selected' : '' }}>Renewal</option>
        </select>

        <div style="display:flex; gap:0.5rem; flex:1 1 120px;">
            <button type="submit" class="btn-banking btn-primary" style="flex:1; justify-content:center;">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if($search || $status || $type)
            <a href="{{ route('admin.subscriptions.index') }}" class="btn-banking btn-secondary" style="text-decoration:none; padding:0.6rem 0.8rem;" title="Futa Vichujio">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Subscriptions Card Container --}}
<div class="banking-card p-0" style="overflow:hidden;">
    <div style="padding:1.1rem 1.4rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; background:#FAFAFC;">
        <span style="font-weight:700; font-size:0.95rem; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-history" style="color:var(--primary-blue);"></i> Orodha ya Subscriptions
        </span>
        <span class="badge-status badge-secondary">{{ $subscriptions->total() }} rekodi</span>
    </div>

    {{-- Desktop Table View --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table-banking">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Package</th>
                    <th>Account</th>
                    <th>Type & Payment</th>
                    <th>Timeline</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr>
                    <td>
                        @if($sub->customer)
                        <a href="{{ route('admin.customers.show', $sub->customer) }}" style="text-decoration:none; font-weight:600; color:var(--text-main);">
                            {{ $sub->customer->name }}
                        </a>
                        <div style="font-size:0.78rem; color:var(--text-muted); font-family:monospace;">{{ $sub->customer->phone_number }}</div>
                        @else
                        <span style="color:var(--text-muted); font-style:italic;">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:700; color:var(--text-main);">{{ optional($sub->package)->name ?? '—' }}</span>
                    </td>
                    <td style="font-family:monospace; color:var(--text-muted); font-size:0.85rem;">
                        {{ optional($sub->wifiUser)->mikrotik_username ?? '—' }}
                    </td>
                    <td>
                        <span style="background:{{ $sub->typeColor }}1a; color:{{ $sub->typeColor }}; padding:0.2rem 0.5rem; border-radius:6px; font-size:0.72rem; font-weight:700; text-transform:uppercase;">
                            {{ $sub->typeLabel }}
                        </span>
                        @if($sub->transaction)
                        <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace; margin-top:0.25rem;">
                            {{ $sub->transaction->transaction_reference }}
                        </div>
                        @endif
                    </td>
                    <td style="font-size:0.8rem; color:var(--text-muted);">
                        <div><strong style="color:var(--text-main);">Inaanza:</strong> {{ $sub->starts_at ? $sub->starts_at->format('d M H:i') : '—' }}</div>
                        <div><strong style="color:var(--text-main);">Inaisha:</strong> {{ $sub->expires_at ? $sub->expires_at->format('d M H:i') : '—' }}</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge-status {{ $sub->status === 'active' ? 'badge-success' : ($sub->status === 'expired' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if($sub->status === 'active')
                        <form action="{{ route('admin.subscriptions.cancel', $sub) }}" method="POST" style="display:inline;" onsubmit="return confirm('Je, una uhakika unataka kusitisha (cancel) subscription hii?')">
                            @csrf
                            <button type="submit" title="Cancel Subscription" class="btn-banking btn-danger" style="padding:0.35rem 0.6rem; font-size:0.78rem;">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @else
                        <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:3.5rem 1rem;">
                        <div style="color:var(--text-muted);">
                            <i class="fas fa-history" style="font-size:2.6rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                            <div style="font-weight:700; font-size:1.05rem; color:var(--text-main); margin-bottom:0.3rem;">Hakuna Subscriptions zozote</div>
                            <div style="font-size:0.875rem;">Hakuna data iliyopatikana kulingana na kigezo ulichochagua</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Responsive Card Feed --}}
    <div class="d-md-none">
        @forelse($subscriptions as $sub)
        <div style="padding:0.75rem 0.9rem; border-bottom:1px solid var(--border-color); display:flex; flex-direction:column; gap:0.5rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div style="min-width:0;">
                    @if($sub->customer)
                        <a href="{{ route('admin.customers.show', $sub->customer) }}" style="text-decoration:none; font-weight:700; font-size:0.92rem; color:var(--text-main); display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $sub->customer->name }}
                        </a>
                        <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace;">
                            <i class="fas fa-phone" style="font-size:0.65rem;"></i> {{ $sub->customer->phone_number }}
                        </div>
                    @else
                        <div style="font-weight:700; color:var(--text-main); font-size:0.92rem;">N/A</div>
                    @endif
                </div>
                <div class="d-flex flex-column align-items-end gap-1">
                    <span class="badge-status {{ $sub->status === 'active' ? 'badge-success' : ($sub->status === 'expired' ? 'badge-warning' : 'badge-danger') }}" style="font-size:0.68rem;">
                        {{ ucfirst($sub->status) }}
                    </span>
                    <span style="background:{{ $sub->typeColor }}1a; color:{{ $sub->typeColor }}; padding:0.1rem 0.4rem; border-radius:4px; font-size:0.65rem; font-weight:700; text-transform:uppercase;">
                        {{ $sub->typeLabel }}
                    </span>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.4rem; background:#F8FAFC; border-radius:6px; padding:0.4rem 0.65rem; font-size:0.75rem; border:1px solid var(--border-color);">
                <div>
                    <span style="color:var(--text-muted); display:block; font-size:0.68rem;">Kifurushi</span>
                    <strong style="color:var(--text-main);">{{ optional($sub->package)->name ?? '—' }}</strong>
                </div>
                <div>
                    <span style="color:var(--text-muted); display:block; font-size:0.68rem;">Akaunti / Vocha</span>
                    <strong style="color:var(--text-main); font-family:monospace;">{{ optional($sub->wifiUser)->mikrotik_username ?? '—' }}</strong>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-1" style="font-size:0.74rem; color:var(--text-muted);">
                <div>
                    <span>{{ $sub->starts_at ? $sub->starts_at->format('d M H:i') : '—' }}</span>
                    <span style="margin:0 4px;">→</span>
                    <strong style="color:var(--text-main);">{{ $sub->expires_at ? $sub->expires_at->format('d M H:i') : '—' }}</strong>
                </div>
                @if($sub->status === 'active')
                <form action="{{ route('admin.subscriptions.cancel', $sub) }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kusitisha subscription hii?')">
                    @csrf
                    <button type="submit" class="btn-banking btn-danger" style="padding:0.25rem 0.55rem; font-size:0.72rem;">
                        <i class="fas fa-ban"></i> Sitisha
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:3rem 1rem; color:var(--text-muted);">
            <i class="fas fa-history" style="font-size:2.2rem; opacity:0.3; margin-bottom:0.75rem; display:block;"></i>
            <div style="font-weight:700; color:var(--text-main);">Hakuna Subscriptions</div>
        </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    @if($subscriptions->hasPages())
    <div style="padding:1rem 1.4rem; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem; background:#FAFAFC;">
        <div style="color:var(--text-muted); font-size:0.85rem;">
            Inaonyesha <strong>{{ $subscriptions->firstItem() }}</strong>–<strong>{{ $subscriptions->lastItem() }}</strong> kati ya <strong>{{ $subscriptions->total() }}</strong>
        </div>
        <div>
            {{ $subscriptions->links() }}
        </div>
    </div>
    @endif
</div>

@endsection
