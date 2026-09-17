@extends('layouts.app')

@section('title', 'Matumizi')

@section('content')
<div class="content-container" style="padding-bottom: 5rem;">

    {{-- 1. Page Header --}}
    <div class="page-header-compact">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">
                    Matumizi
                </h1>
            </div>
            <p>Gharama za uendeshaji na mtandao</p>
        </div>

        <button type="button" onclick="openExpenseModal()" class="btn-primary mobile-btn-compact" style="background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.22); display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1rem; font-weight: 700; border-radius: 10px; font-size: 0.82rem; white-space: nowrap;">
            <i class="fas fa-plus"></i> Sajili Matumizi
        </button>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; padding: 0.65rem 1rem; border-radius: 10px; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.65rem; font-size: 0.84rem; font-weight: 600;">
            <i class="fas fa-check-circle" style="color: #16A34A; font-size: 1rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 0.65rem 1rem; border-radius: 10px; margin-bottom: 0.85rem; font-size: 0.82rem;">
            <div style="font-weight: 700; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="fas fa-exclamation-triangle"></i> Kuna hitilafu kwenye fomu:
            </div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. Financial KPI Metric Cards (Compact) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.65rem; margin-bottom: 1rem;">
        {{-- Matumizi Leo --}}
        <div class="banking-card" style="padding: 0.85rem 1rem; background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 14px; box-shadow: var(--shadow-xs);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                <span style="font-size: 0.68rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Leo</span>
                <span style="background: #FEE2E2; color: #DC2626; font-size: 0.64rem; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 5px;">Leo</span>
            </div>
            <div style="font-size: 1.25rem; font-weight: 900; color: #DC2626; font-family: monospace; letter-spacing: -0.02em;">
                Tsh {{ number_format($expensesToday) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">
                Gharama za leo
            </div>
        </div>

        {{-- Matumizi Mwezi Huu --}}
        <div class="banking-card" style="padding: 0.85rem 1rem; background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 14px; box-shadow: var(--shadow-xs);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                <span style="font-size: 0.68rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Mwezi Huu</span>
                <span style="background: #FEF3C7; color: #D97706; font-size: 0.64rem; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 5px;">{{ date('M') }}</span>
            </div>
            <div style="font-size: 1.25rem; font-weight: 900; color: #D97706; font-family: monospace; letter-spacing: -0.02em;">
                Tsh {{ number_format($expensesThisMonth) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">
                Jumla ya mwezi
            </div>
        </div>

        {{-- Jumla Yote ya Matumizi --}}
        <div class="banking-card" style="padding: 0.85rem 1rem; background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 14px; box-shadow: var(--shadow-xs);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                <span style="font-size: 0.68rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Jumla Yote</span>
                <span style="background: #F1F5F9; color: var(--text-muted); font-size: 0.64rem; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 5px;">Yote</span>
            </div>
            <div style="font-size: 1.25rem; font-weight: 900; color: var(--text-main); font-family: monospace; letter-spacing: -0.02em;">
                Tsh {{ number_format($totalExpenses) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">
                Yaliyosajiliwa
            </div>
        </div>

        {{-- Idadi ya Rekodi --}}
        <div class="banking-card" style="padding: 0.85rem 1rem; background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 14px; box-shadow: var(--shadow-xs);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                <span style="font-size: 0.68rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Rekodi</span>
                <span style="background: #EFF6FF; color: #2563EB; font-size: 0.64rem; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 5px;">Risiti</span>
            </div>
            <div style="font-size: 1.25rem; font-weight: 900; color: #2563EB; font-family: monospace; letter-spacing: -0.02em;">
                {{ number_format($expensesCount) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">
                Idadi ya matumizi
            </div>
        </div>
    </div>

    {{-- 3. Category Breakdown Quick Pills (if any) --}}
    @if($categoryBreakdown->count() > 0)
    <div style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 16px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; box-shadow: var(--shadow-xs);">
        <div style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.65rem;">
            Matumizi kwa Makundi Makuu:
        </div>
        <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.25rem;" class="no-scrollbar">
            @foreach($categoryBreakdown as $cat)
                <a href="{{ route('admin.expenses.index', ['category' => $cat->category]) }}" style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 0.4rem 0.75rem; text-decoration: none; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.78rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#CBD5E1'" onmouseout="this.style.borderColor='var(--border-color)'">
                    <span style="font-weight: 700; color: var(--text-main);">{{ $cat->category }}</span>
                    <span style="background: #FEE2E2; color: #DC2626; font-weight: 800; font-family: monospace; padding: 0.1rem 0.45rem; border-radius: 6px; font-size: 0.72rem;">
                        Tsh {{ number_format($cat->total_amount) }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 4. Filters Bar --}}
    <div style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 16px; padding: 1rem; margin-bottom: 1.25rem; box-shadow: var(--shadow-xs);">
        <form method="GET" action="{{ route('admin.expenses.index') }}" style="margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; align-items: flex-end;">
            {{-- Search Input --}}
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Tafuta</label>
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Maelezo, namba ya risiti..." class="form-control" style="padding-left: 2.25rem; width: 100%; border-radius: 10px; font-size: 0.84rem; height: 38px;">
                </div>
            </div>

            {{-- Category Filter --}}
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Kundi la Matumizi</label>
                <select name="category" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.84rem; height: 38px;">
                    <option value="">Makundi Yote</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Payment Method --}}
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Njia ya Malipo</label>
                <select name="payment_method" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.84rem; height: 38px;">
                    <option value="">Njia Zote</option>
                    @foreach($paymentMethods as $key => $label)
                        <option value="{{ $key }}" {{ request('payment_method') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Period Filter --}}
            <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Kipindi</label>
                <select name="period" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.84rem; height: 38px;">
                    <option value="">Kipindi Chote</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Leo Tu</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Wiki Hii</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Mwezi Huu</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn-primary" style="flex: 1; height: 38px; border-radius: 10px; font-size: 0.84rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                    <i class="fas fa-filter"></i> Chuja
                </button>
                @if(request()->anyFilled(['search', 'category', 'payment_method', 'period', 'date_from', 'date_to']))
                    <a href="{{ route('admin.expenses.index') }}" class="btn-secondary" style="height: 38px; padding: 0 0.85rem; border-radius: 10px; font-size: 0.84rem; font-weight: 700; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="Ondoa Vichungi">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 5. Expenses Listing --}}
    @if($expenses->count() > 0)
        {{-- DESKTOP TABLE VIEW --}}
        <div class="desktop-only-table" style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-xs); margin-bottom: 1.5rem;">
            <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Tarehe</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Maelezo ya Matumizi</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Kundi</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Njia &amp; Kumbukumbu</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: right;">Kiasi (Tsh)</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Mrekodi / Site</th>
                        <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: right;">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $exp)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 0.85rem 1rem; font-size: 0.82rem; font-weight: 600; color: var(--text-main); white-space: nowrap;">
                            <div style="font-weight: 700;">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</div>
                            <div style="font-size: 0.72rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($exp->expense_date)->diffForHumans() }}</div>
                        </td>
                        <td style="padding: 0.85rem 1rem;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">{{ $exp->title }}</div>
                            @if($exp->notes)
                                <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($exp->notes, 60) }}</div>
                            @endif
                        </td>
                        <td style="padding: 0.85rem 1rem; white-space: nowrap;">
                            <span style="background: #F1F5F9; color: #334155; font-size: 0.74rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 8px; border: 1px solid #E2E8F0; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <i class="fas fa-tag" style="color: #64748B; font-size: 0.65rem;"></i>
                                {{ $exp->category }}
                            </span>
                        </td>
                        <td style="padding: 0.85rem 1rem; white-space: nowrap;">
                            <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 700; color: var(--text-main);">
                                <i class="fas {{ $exp->payment_method == 'Cash' ? 'fa-money-bill-wave text-blue-500' : 'fa-mobile-screen-button text-emerald-500' }}"></i>
                                <span>{{ $exp->payment_method }}</span>
                            </div>
                            @if($exp->reference_no)
                                <div style="font-size: 0.72rem; font-family: monospace; color: var(--text-muted); margin-top: 2px;">
                                    Ref: {{ $exp->reference_no }}
                                </div>
                            @endif
                        </td>
                        <td style="padding: 0.85rem 1rem; text-align: right; white-space: nowrap;">
                            <span style="font-size: 0.95rem; font-weight: 900; color: #DC2626; font-family: monospace;">
                                -Tsh {{ number_format($exp->amount, 2) }}
                            </span>
                        </td>
                        <td style="padding: 0.85rem 1rem; font-size: 0.78rem;">
                            <div style="font-weight: 600; color: var(--text-main);">
                                {{ $exp->recordedBy ? $exp->recordedBy->name : 'Msimamizi' }}
                            </div>
                            @if($exp->site)
                                <div style="font-size: 0.72rem; color: #6366F1; font-weight: 600;">
                                    <i class="fas fa-location-dot" style="font-size: 0.65rem;"></i> {{ $exp->site->name }}
                                </div>
                            @endif
                        </td>
                        <td style="padding: 0.85rem 1rem; text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                                <button type="button" 
                                    onclick="editExpense({{ json_encode($exp) }})"
                                    style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-main); width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s;"
                                    title="Hariri">
                                    <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                </button>

                                <form action="{{ route('admin.expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kufuta matumizi haya ya Tsh {{ number_format($exp->amount) }}? Kiasi hiki kitarudishwa kwenye salio.');" style="margin: 0; display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        style="background: #FEE2E2; border: 1px solid #FECACA; color: #DC2626; width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s;"
                                        title="Futa">
                                        <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MOBILE CARD FEED (For Mobile Screens) --}}
        <div class="mobile-card-feed">
            @foreach($expenses as $exp)
            <div class="mobile-user-card" style="border-left: 4px solid #DC2626;">
                <div class="card-header-row" style="margin-bottom: 0.5rem;">
                    <div>
                        <div style="font-size: 0.94rem; font-weight: 800; color: var(--text-main); line-height: 1.25;">
                            {{ $exp->title }}
                        </div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 3px;">
                            <i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}
                            @if($exp->site)
                                &bull; <i class="fas fa-location-dot" style="color: #6366F1;"></i> {{ $exp->site->name }}
                            @endif
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.05rem; font-weight: 900; color: #DC2626; font-family: monospace;">
                            -Tsh {{ number_format($exp->amount) }}
                        </div>
                    </div>
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; align-items: center; margin: 0.5rem 0;">
                    <span style="background: #F1F5F9; color: #334155; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px; border: 1px solid #E2E8F0;">
                        <i class="fas fa-tag text-[9px] text-slate-400"></i> {{ $exp->category }}
                    </span>
                    <span style="background: #EFF6FF; color: #1D4ED8; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px; border: 1px solid #DBEAFE;">
                        {{ $exp->payment_method }}
                    </span>
                    @if($exp->reference_no)
                    <span style="background: #F8FAFC; color: var(--text-muted); font-size: 0.7rem; font-family: monospace; padding: 0.2rem 0.5rem; border-radius: 6px; border: 1px solid #E2E8F0;">
                        Ref: {{ $exp->reference_no }}
                    </span>
                    @endif
                </div>

                @if($exp->notes)
                <div style="font-size: 0.76rem; color: var(--text-secondary); background: #F8FAFC; padding: 0.45rem 0.65rem; border-radius: 8px; margin-bottom: 0.65rem; border: 1px solid #F1F5F9;">
                    {{ $exp->notes }}
                </div>
                @endif

                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-top: 1px solid var(--border-color); margin-top: 0.5rem;">
                    <span style="font-size: 0.72rem; color: var(--text-muted);">
                        Mrekodi: <strong>{{ $exp->recordedBy ? $exp->recordedBy->name : 'Msimamizi' }}</strong>
                    </span>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" 
                            onclick="editExpense({{ json_encode($exp) }})"
                            style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.78rem; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 8px; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i class="fas fa-edit"></i> Hariri
                        </button>
                        <form action="{{ route('admin.expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Una uhakika unataka kufuta matumizi haya ya Tsh {{ number_format($exp->amount) }}?');" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                style="background: #FEE2E2; border: 1px solid #FECACA; color: #DC2626; font-size: 0.78rem; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 8px; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <i class="fas fa-trash-alt"></i> Futa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 1.25rem;">
            {{ $expenses->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 3.5rem 1.5rem; background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 16px; margin-bottom: 1.5rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1.25rem auto;">
                <i class="fas fa-receipt"></i>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.4rem;">
                Hakuna Matumizi Yaliyopatikana
            </h3>
            <p style="font-size: 0.84rem; color: var(--text-muted); max-width: 420px; margin: 0 auto 1.25rem auto;">
                Hujasajili matumizi yoyote bado au hakuna rekodi inayolingana na vichungi ulivyochagua.
            </p>
            <button type="button" onclick="openExpenseModal()" class="btn-primary" style="background: #DC2626; padding: 0.65rem 1.35rem; font-weight: 700; border-radius: 12px; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i> Sajili Matumizi Sasa
            </button>
        </div>
    @endif

</div>

{{-- ======================================================== --}}
{{-- MODAL: SAJILI MATUMIZI MAPYA                              --}}
{{-- ======================================================== --}}
<div class="mobile-modal-backdrop" id="addExpenseModal" style="display: none;">
    <div class="mobile-modal-dialog" style="max-width: 540px; background: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #FFF5F5;">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #991B1B; margin: 0;">Sajili Matumizi Mapya</h3>
                    <p style="font-size: 0.72rem; color: #B91C1C; margin: 0;">Pesa itapungua moja kwa moja kwenye dashibodi</p>
                </div>
            </div>
            <button type="button" onclick="closeExpenseModal()" style="background: none; border: none; font-size: 1.35rem; color: var(--text-muted); cursor: pointer; padding: 0.25rem;">&times;</button>
        </div>

        <form action="{{ route('admin.expenses.store') }}" method="POST" style="margin: 0; padding: 1.35rem 1.5rem;">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr; gap: 0.9rem;">
                {{-- Jina / Maelezo ya Matumizi --}}
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                        Maelezo ya Matumizi <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="title" required placeholder="Mf. Kununua LUKU Site A, Fiber ya mwezi, posho ya fundi..." class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.88rem; padding: 0.65rem 0.85rem;">
                </div>

                {{-- Kiasi & Tarehe --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Kiasi cha Pesa (Tsh) <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="number" step="0.01" min="1" name="amount" required placeholder="Mf. 25000" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.88rem; padding: 0.65rem 0.85rem; font-family: monospace; font-weight: 700;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Tarehe ya Matumizi <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.88rem; padding: 0.65rem 0.85rem;">
                    </div>
                </div>

                {{-- Kundi la Matumizi & Njia ya Malipo --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Kundi (Category) <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="category" required class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;">
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Njia ya Malipo <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="payment_method" required class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;">
                            @foreach($paymentMethods as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Kumbukumbu / Ref & Site --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Namba ya Kumbukumbu / Risiti
                        </label>
                        <input type="text" name="reference_no" placeholder="Mf. M-Pesa ID, Token No..." class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Site Husika (Hiari)
                        </label>
                        <select name="site_id" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;">
                            <option value="">-- Hakuna Site Mahsusi --</option>
                            @foreach($sites as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Maelezo ya Ziada --}}
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                        Maelezo ya Ziada (Hiari)
                    </label>
                    <textarea name="notes" rows="2" placeholder="Andika maelezo yoyote muhimu hapa..." class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;"></textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.35rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <button type="button" onclick="closeExpenseModal()" class="btn-secondary" style="padding: 0.65rem 1.15rem; border-radius: 10px; font-weight: 700; font-size: 0.84rem;">
                    Ghairi
                </button>
                <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%); padding: 0.65rem 1.45rem; border-radius: 10px; font-weight: 700; font-size: 0.84rem; display: inline-flex; align-items: center; gap: 0.45rem;">
                    <i class="fas fa-check"></i> Hifadhi Matumizi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================== --}}
{{-- MODAL: HARIRI MATUMIZI                                    --}}
{{-- ======================================================== --}}
<div class="mobile-modal-backdrop" id="editExpenseModal" style="display: none;">
    <div class="mobile-modal-dialog" style="max-width: 540px; background: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                    <i class="fas fa-edit"></i>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0;">Hariri Rekodi ya Matumizi</h3>
            </div>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 1.35rem; color: var(--text-muted); cursor: pointer; padding: 0.25rem;">&times;</button>
        </div>

        <form id="editExpenseForm" method="POST" style="margin: 0; padding: 1.35rem 1.5rem;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr; gap: 0.9rem;">
                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                        Maelezo ya Matumizi <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" id="edit_title" name="title" required class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.88rem; padding: 0.65rem 0.85rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Kiasi cha Pesa (Tsh) <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="number" step="0.01" min="1" id="edit_amount" name="amount" required class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.88rem; padding: 0.65rem 0.85rem; font-family: monospace; font-weight: 700;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Tarehe ya Matumizi <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="date" id="edit_expense_date" name="expense_date" required class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.88rem; padding: 0.65rem 0.85rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Kundi (Category) <span style="color: #DC2626;">*</span>
                        </label>
                        <select id="edit_category" name="category" required class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;">
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Njia ya Malipo <span style="color: #DC2626;">*</span>
                        </label>
                        <select id="edit_payment_method" name="payment_method" required class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;">
                            @foreach($paymentMethods as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Namba ya Kumbukumbu / Risiti
                        </label>
                        <input type="text" id="edit_reference_no" name="reference_no" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            Site Husika (Hiari)
                        </label>
                        <select id="edit_site_id" name="site_id" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;">
                            <option value="">-- Hakuna Site Mahsusi --</option>
                            @foreach($sites as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.76rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                        Maelezo ya Ziada (Hiari)
                    </label>
                    <textarea id="edit_notes" name="notes" rows="2" class="form-control" style="width: 100%; border-radius: 10px; font-size: 0.85rem; padding: 0.65rem 0.85rem;"></textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.35rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <button type="button" onclick="closeEditModal()" class="btn-secondary" style="padding: 0.65rem 1.15rem; border-radius: 10px; font-weight: 700; font-size: 0.84rem;">
                    Ghairi
                </button>
                <button type="submit" class="btn-primary" style="padding: 0.65rem 1.45rem; border-radius: 10px; font-weight: 700; font-size: 0.84rem; display: inline-flex; align-items: center; gap: 0.45rem;">
                    <i class="fas fa-save"></i> Hifadhi Mabadiliko
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openExpenseModal() {
        document.getElementById('addExpenseModal').style.display = 'flex';
    }

    function closeExpenseModal() {
        document.getElementById('addExpenseModal').style.display = 'none';
    }

    function editExpense(exp) {
        document.getElementById('editExpenseForm').action = '/admin/expenses/' + exp.id;
        document.getElementById('edit_title').value = exp.title || '';
        document.getElementById('edit_amount').value = exp.amount || '';
        
        // Format date string YYYY-MM-DD
        let d = exp.expense_date;
        if (d && d.includes('T')) {
            d = d.split('T')[0];
        }
        document.getElementById('edit_expense_date').value = d || '';
        document.getElementById('edit_category').value = exp.category || 'Mengineyo';
        document.getElementById('edit_payment_method').value = exp.payment_method || 'Cash';
        document.getElementById('edit_reference_no').value = exp.reference_no || '';
        document.getElementById('edit_site_id').value = exp.site_id || '';
        document.getElementById('edit_notes').value = exp.notes || '';

        document.getElementById('editExpenseModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editExpenseModal').style.display = 'none';
    }

    // Close on backdrop click
    document.getElementById('addExpenseModal').addEventListener('click', function(e) {
        if (e.target === this) closeExpenseModal();
    });
    document.getElementById('editExpenseModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endsection
