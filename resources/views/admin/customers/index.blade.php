@extends('layouts.app')

@section('title', 'Wateja')

@section('content')
<div style="padding-bottom: 5rem;">

    {{-- Page Header --}}
    <div class="page-header-compact" style="margin-bottom: 1.25rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">Wateja</h2>
                    <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Watumiaji na akaunti zilizosajiliwa kwenye mtandao</p>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="btn-banking btn-primary mobile-btn-compact" style="text-decoration: none; font-size: 0.82rem; padding: 0.55rem 1rem; border-radius: 10px; background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%); color: #fff; border: none; display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700;">
            <i class="fas fa-user-plus"></i> Mteja Mpya
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.65rem; font-weight: 600; font-size: 0.85rem;">
        <i class="fas fa-check-circle" style="color: #059669; font-size: 1rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Customer KPI Stat Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.65rem; margin-bottom: 1.25rem;">
        @php
            $statCards = [
                ['label'=>'Jumla',       'value'=>$stats['total'],     'color'=>'#0F172A', 'bg'=>'#F1F5F9',                  'icon'=>'fa-users'],
                ['label'=>'Active',      'value'=>$stats['active'],    'color'=>'#10B981', 'bg'=>'rgba(16, 185, 129, 0.12)',  'icon'=>'fa-user-check'],
                ['label'=>'Expired',     'value'=>$stats['expired'],   'color'=>'#D97706', 'bg'=>'rgba(217, 119, 6, 0.12)',  'icon'=>'fa-user-clock'],
                ['label'=>'Suspended',   'value'=>$stats['suspended'], 'color'=>'#DC2626', 'bg'=>'rgba(220, 38, 38, 0.12)',  'icon'=>'fa-user-slash'],
                ['label'=>'Hewani Sasa', 'value'=>$stats['online'],    'color'=>'#FF3300', 'bg'=>'rgba(255, 51, 0, 0.12)',   'icon'=>'fa-wifi'],
            ];
        @endphp
        @foreach($statCards as $card)
        <a href="{{ request()->fullUrlWithQuery(['status' => strtolower($card['label'])]) }}"
           style="background: #FFFFFF; border-radius: 14px; padding: 0.75rem 0.95rem; border: 1px solid var(--border-color); text-decoration: none; display: flex; align-items: center; gap: 0.75rem; box-shadow: var(--shadow-xs); transition: all 0.2s;"
           onmouseover="this.style.boxShadow='var(--shadow-sm)'; this.style.borderColor='#CBD5E1';" onmouseout="this.style.boxShadow='var(--shadow-xs)'; this.style.borderColor='var(--border-color)';">
            <div style="width: 34px; height: 34px; border-radius: 9px; background: {{ $card['bg'] }}; color: {{ $card['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0;">
                <i class="fas {{ $card['icon'] }}"></i>
            </div>
            <div>
                <div style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); line-height: 1.1; letter-spacing: -0.02em;">{{ number_format($card['value']) }}</div>
                <div style="font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px; margin-top: 2px;">{{ $card['label'] }}</div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Search & Filter Bar --}}
    <div class="banking-card" style="padding: 1rem; margin-bottom: 1.25rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color);">
        <form method="GET" action="{{ route('admin.customers.index') }}" style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 2 1 200px; position: relative; width: 100%;">
                <i class="fas fa-search" style="position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Tafuta jina, namba ya simu..." class="form-control" style="padding-left: 2.3rem; height: 38px; border-radius: 10px; font-size: 0.85rem; border: 1px solid var(--border-color); width: 100%;">
            </div>

            <select name="status" class="form-control" style="flex: 1 1 130px; height: 38px; border-radius: 10px; font-size: 0.85rem; border: 1px solid var(--border-color); background: #FFFFFF;">
                <option value="">Hali Zote</option>
                @foreach(['active'=>'Active','expired'=>'Expired','suspended'=>'Suspended','blocked'=>'Blocked'] as $key => $lbl)
                <option value="{{ $key }}" {{ ($status ?? '') === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>

            <select name="sort" class="form-control" style="flex: 1 1 140px; height: 38px; border-radius: 10px; font-size: 0.85rem; border: 1px solid var(--border-color); background: #FFFFFF;">
                <option value="created_at" {{ ($sort ?? 'created_at') === 'created_at' ? 'selected' : '' }}>Tarehe (Mpya)</option>
                <option value="name" {{ ($sort ?? '') === 'name' ? 'selected' : '' }}>Jina (A-Z)</option>
                <option value="phone_number" {{ ($sort ?? '') === 'phone_number' ? 'selected' : '' }}>Namba ya Simu</option>
                <option value="status" {{ ($sort ?? '') === 'status' ? 'selected' : '' }}>Hali (Status)</option>
            </select>

            <div style="display: flex; gap: 0.5rem; flex: 1 1 140px;">
                <button type="submit" class="btn-banking btn-primary" style="flex: 1; height: 38px; border-radius: 10px; font-size: 0.85rem; font-weight: 700; background: linear-gradient(135deg, #FF3300 0%, #ff5722 100%); color: #fff; border: none; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                    <i class="fas fa-filter"></i> Chuja
                </button>
                @if($search || $status)
                <a href="{{ route('admin.customers.index') }}" class="btn-banking btn-secondary" style="height: 38px; padding: 0 0.85rem; border-radius: 10px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="Weka Upya">
                    <i class="fas fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Customers List Card Container --}}
    <div class="banking-card p-0" style="overflow: hidden; border-radius: 16px; border: 1px solid var(--border-color); background: #FFFFFF; box-shadow: var(--shadow-xs);">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-address-book" style="color: #FF3300;"></i>
                <span style="font-weight: 800; font-size: 0.95rem; color: var(--text-main);">Orodha ya Wateja</span>
            </div>
            <span style="background: rgba(15, 23, 42, 0.08); color: var(--text-main); font-weight: 700; font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 20px;">
                {{ number_format($customers->total()) }} Wateja
            </span>
        </div>

        {{-- Desktop Table View --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table-banking" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; width: 45px;">#</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Mteja</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Simu</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Hali</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Kifurushi</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: center;">Accounts</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Tarehe</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: center;">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-weight: 700; font-size: 0.78rem;">{{ $customer->id }}</td>
                        <td style="padding: 0.85rem 1rem;">
                            <a href="{{ route('admin.customers.show', $customer) }}" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.88rem; flex-shrink: 0;">
                                    {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">{{ $customer->name ?? '—' }}</div>
                                    @if($customer->email)
                                    <div style="color: var(--text-muted); font-size: 0.74rem;">{{ $customer->email }}</div>
                                    @endif
                                </div>
                            </a>
                        </td>
                        <td style="padding: 0.85rem 1rem; font-family: monospace; font-size: 0.85rem; font-weight: 600; color: var(--text-main);">
                            {{ $customer->phone_number }}
                        </td>
                        <td style="padding: 0.85rem 1rem;">
                            <span class="badge-status {{ $customer->status === 'active' ? 'badge-success' : ($customer->status === 'suspended' ? 'badge-danger' : 'badge-warning') }}" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        <td style="padding: 0.85rem 1rem; font-size: 0.84rem; font-weight: 600; color: var(--text-secondary);">
                            {{ optional(optional($customer->activeWifiUser)->package)->name ?? '—' }}
                        </td>
                        <td style="padding: 0.85rem 1rem; text-align: center;">
                            <span class="badge-status badge-secondary" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">
                                {{ $customer->wifi_users_count ?? 0 }}
                            </span>
                        </td>
                        <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.8rem; white-space: nowrap;">
                            {{ $customer->created_at->format('d M Y') }}
                        </td>
                        <td style="padding: 0.85rem 1rem; text-align: center;">
                            <div style="display: flex; gap: 0.4rem; justify-content: center; align-items: center;">
                                <a href="{{ route('admin.customers.show', $customer) }}" title="Tazama" class="btn-banking btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; border-radius: 8px;">
                                    <i class="fas fa-eye" style="color: #2563EB;"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" title="Hariri" class="btn-banking btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; border-radius: 8px;">
                                    <i class="fas fa-edit" style="color: #D97706;"></i>
                                </a>
                                @if($customer->status !== 'suspended')
                                <form action="{{ route('admin.customers.suspend', $customer) }}" method="POST" style="display: inline;" onsubmit="return confirm('Una uhakika unataka kumsitisha huyu mteja?');">
                                    @csrf
                                    <button type="submit" title="Sitisha (Suspend)" class="btn-banking btn-danger" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; border-radius: 8px;">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.customers.activate', $customer) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" title="Washa (Activate)" class="btn-banking btn-primary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; border-radius: 8px; background: #10B981; border: none;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 3.5rem 1rem; text-align: center;">
                            <div style="width: 56px; height: 56px; border-radius: 50%; background: #F1F5F9; color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1rem auto;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div style="font-weight: 800; font-size: 1rem; color: var(--text-main); margin-bottom: 0.25rem;">Hakuna wateja waliopatikana</div>
                            <div style="font-size: 0.82rem; color: var(--text-muted);">
                                @if($search || $status)
                                Jaribu kubadilisha filter au maneno ya kutafuta.
                                @else
                                Anza kwa kusajili mteja wako wa kwanza.
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card Feed --}}
        <div class="d-md-none">
            @forelse($customers as $customer)
            <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.65rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem;">
                    <a href="{{ route('admin.customers.show', $customer) }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem; flex-shrink: 0;">
                            {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
                        </div>
                        <div style="min-width: 0;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $customer->name ?? 'Mteja #' . $customer->id }}
                            </div>
                            <div style="font-family: monospace; font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                                <i class="fas fa-phone-alt" style="font-size: 0.72rem; margin-right: 2px;"></i> {{ $customer->phone_number }}
                            </div>
                        </div>
                    </a>
                    <div style="flex-shrink: 0;">
                        @if($customer->status === 'active')
                            <span class="badge-status badge-success" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">Active</span>
                        @elseif($customer->status === 'suspended')
                            <span class="badge-status badge-danger" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">Suspended</span>
                        @else
                            <span class="badge-status badge-warning" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">{{ ucfirst($customer->status) }}</span>
                        @endif
                    </div>
                </div>

                <div style="background: #F8FAFC; padding: 0.6rem 0.85rem; border-radius: 10px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                    <span style="color: var(--text-muted);">Kifurushi:</span>
                    <span style="font-weight: 700; color: #0F172A;">{{ optional(optional($customer->activeWifiUser)->package)->name ?? 'Bila Kifurushi' }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-top: 1px dashed var(--border-color);">
                    <span style="font-size: 0.76rem; color: var(--text-muted);">
                        <i class="fas fa-calendar-alt" style="margin-right: 3px;"></i> {{ $customer->created_at->format('d M Y') }}
                    </span>
                    <div style="display: flex; gap: 0.4rem;">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn-banking btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; text-decoration: none; border-radius: 8px;">
                            <i class="fas fa-eye" style="color: #2563EB;"></i> Tazama
                        </a>
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn-banking btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; text-decoration: none; border-radius: 8px;">
                            <i class="fas fa-edit" style="color: #D97706;"></i>
                        </a>
                        @if($customer->status !== 'suspended')
                        <form action="{{ route('admin.customers.suspend', $customer) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kumsitisha mteja huyu?');" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-banking btn-danger" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; border-radius: 8px;" title="Sitisha">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.customers.activate', $customer) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-banking btn-primary" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; border-radius: 8px; background: #10B981; border: none;" title="Washa">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
                <i class="fas fa-users" style="font-size: 2.2rem; opacity: 0.3; margin-bottom: 0.75rem; display: block;"></i>
                <div style="font-weight: 700; color: var(--text-main);">Hakuna Wateja Waliopatikana</div>
                <div style="font-size: 0.8rem; margin-top: 0.25rem;">Jaribu kubadilisha vigezo au sajili mteja mpya.</div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($customers->hasPages())
        <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; background: #F8FAFC;">
            <div style="color: var(--text-muted); font-size: 0.82rem; font-weight: 600;">
                Inaonyesha <strong>{{ $customers->firstItem() }}</strong>–<strong>{{ $customers->lastItem() }}</strong> kati ya <strong>{{ $customers->total() }}</strong>
            </div>
            <div>
                {{ $customers->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
