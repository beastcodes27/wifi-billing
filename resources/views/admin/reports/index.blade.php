@extends('layouts.app')

@section('title', 'Ripoti na Takwimu')

@section('content')
<div style="padding-bottom: 5rem;">

    {{-- Page Header --}}
    <div class="page-header-compact" style="margin-bottom: 1.25rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 51, 0, 0.1); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">Ripoti na Takwimu</h2>
                    <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Mwenendo wa mapato, mauzo ya vifurushi na faida halisi</p>
                </div>
            </div>
        </div>
        <button onclick="window.print()" class="btn-banking btn-secondary mobile-btn-compact" style="font-size: 0.82rem; padding: 0.55rem 0.95rem; border-radius: 10px; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fas fa-print" style="color: #64748B;"></i> Chapisha Ripoti
        </button>
    </div>

    {{-- Top Financial KPI Cards (2 cols mobile, 3/6 cols desktop) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-bottom: 1.25rem;">
        {{-- Jumla ya Mapato --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Jumla ya Mapato</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #059669; line-height: 1.2; letter-spacing: -0.02em;">
                TSh {{ number_format($totalRevenue) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); border-top: 1px dashed var(--border-color); padding-top: 0.35rem; margin-top: 0.45rem;">
                Vyanzo vyote vya mtandao
            </div>
        </div>

        {{-- Mtandaoni --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Mtandaoni</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255, 51, 0, 0.12); color: #FF3300; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-mobile-alt"></i>
                </div>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #FF3300; line-height: 1.2; letter-spacing: -0.02em;">
                TSh {{ number_format($onlineRevenue) }}
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; border-top: 1px dashed var(--border-color); padding-top: 0.35rem; margin-top: 0.45rem;">
                <span style="color: var(--text-muted);">Mwezi huu:</span>
                <span style="font-weight: 700; color: #FF3300;">TSh {{ number_format($thisMonthOnline) }}</span>
            </div>
        </div>

        {{-- Cash / Vocha --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Cash / Vocha</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #D97706; line-height: 1.2; letter-spacing: -0.02em;">
                TSh {{ number_format($offlineRevenue) }}
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; border-top: 1px dashed var(--border-color); padding-top: 0.35rem; margin-top: 0.45rem;">
                <span style="color: var(--text-muted);">Mwezi huu:</span>
                <span style="font-weight: 700; color: #D97706;">TSh {{ number_format($thisMonthOffline) }}</span>
            </div>
        </div>

        {{-- Matumizi Yote --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Matumizi</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(239, 68, 68, 0.12); color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #DC2626; line-height: 1.2; letter-spacing: -0.02em;">
                TSh {{ number_format($totalExpenses) }}
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; border-top: 1px dashed var(--border-color); padding-top: 0.35rem; margin-top: 0.45rem;">
                <span style="color: var(--text-muted);">Mwezi huu:</span>
                <span style="font-weight: 700; color: #DC2626;">TSh {{ number_format($thisMonthExpenses) }}</span>
            </div>
        </div>

        {{-- Faida Halisi --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Faida Halisi</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-scale-balanced"></i>
                </div>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: #059669; line-height: 1.2; letter-spacing: -0.02em;">
                TSh {{ number_format($netProfitTotal) }}
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; border-top: 1px dashed var(--border-color); padding-top: 0.35rem; margin-top: 0.45rem;">
                <span style="color: var(--text-muted);">Mwezi huu:</span>
                <span style="font-weight: 700; color: #059669;">TSh {{ number_format($netProfitThisMonth) }}</span>
            </div>
        </div>

        {{-- Wateja --}}
        <div class="banking-card" style="padding: 1rem; border-radius: 14px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.4rem;">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.3px;">Wateja</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 23, 42, 0.08); color: #0F172A; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); line-height: 1.2; letter-spacing: -0.02em;">
                {{ number_format($totalCustomers) }}
            </div>
            <div style="font-size: 0.7rem; color: var(--text-muted); border-top: 1px dashed var(--border-color); padding-top: 0.35rem; margin-top: 0.45rem;">
                Watumiaji waliojisajili
            </div>
        </div>
    </div>

    {{-- Analytics Grid: Chart + Package Popularity --}}
    <div class="reports-analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
        {{-- Revenue Line Chart --}}
        <div class="banking-card" style="padding: 1.25rem; border-radius: 16px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs); display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin: 0;">Mwenendo wa Mapato</h3>
                    <p style="margin: 0.15rem 0 0; color: var(--text-muted); font-size: 0.78rem;">Siku 30 Zilizopita (TSh)</p>
                </div>
                <span style="background: rgba(255, 51, 0, 0.08); color: #FF3300; font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 20px;">
                    <i class="fas fa-chart-area"></i> Daily Trend
                </span>
            </div>
            <div style="position: relative; min-height: 250px; height: 280px; width: 100%; flex: 1;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Package Popularity --}}
        <div class="banking-card p-0" style="border-radius: 16px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs); overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); background: #F8FAFC; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-trophy" style="color: #F59E0B;"></i> Vifurushi Vinavyopendwa
                </h3>
                <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700;">Mauzo &amp; Mapato</span>
            </div>
            <div class="table-responsive" style="overflow-y: auto; flex: 1;">
                <table class="table-banking" style="margin: 0; width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 0.75rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Kifurushi</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: center;">Mauzo</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: right;">Mapato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packageStats as $stat)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--text-main); font-size: 0.85rem;">
                                {{ $stat->package ? $stat->package->name : 'N/A' }}
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: center;">
                                <span style="background: #F1F5F9; color: #0F172A; font-weight: 700; font-size: 0.74rem; padding: 0.2rem 0.55rem; border-radius: 6px;">
                                    {{ number_format($stat->count) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 800; color: #059669; font-size: 0.85rem; font-family: monospace; white-space: nowrap;">
                                TSh {{ number_format($stat->total_revenue) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); font-size: 0.84rem;">
                                Hakuna takwimu bado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Payment Methods Breakdown --}}
    <div class="banking-card" style="padding: 1.25rem; border-radius: 16px; background: #FFFFFF; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-credit-card" style="color: #FF3300;"></i> Ugawaji wa Njia za Malipo Zilizotumika
            </h3>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">
            @forelse($paymentMethods as $pm)
            <div style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 1rem; border-radius: 12px; text-align: center;">
                <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 0.35rem;">
                    {{ $pm->payment_method ? ucfirst(str_replace('_', ' ', $pm->payment_method)) : 'Njia Nyingine' }}
                </div>
                <div style="font-size: 1.35rem; font-weight: 900; color: var(--text-main); font-family: monospace; letter-spacing: -0.02em;">
                    {{ number_format($pm->count) }}
                </div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Miamala</div>
            </div>
            @empty
            <div style="color: var(--text-muted); font-size: 0.85rem; padding: 1rem; text-align: center; grid-column: 1 / -1;">
                Hakuna taarifa za njia za malipo zilizorekodiwa bado.
            </div>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    const chartContext = ctx.getContext('2d');
    let gradient = chartContext.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(255, 51, 0, 0.22)');
    gradient.addColorStop(1, 'rgba(255, 51, 0, 0.00)');

    new Chart(chartContext, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Mapato (TSh)',
                data: {!! json_encode($chartData) !!},
                borderColor: '#FF3300',
                backgroundColor: gradient,
                borderWidth: 2.5,
                pointBackgroundColor: '#FFFFFF',
                pointBorderColor: '#FF3300',
                pointBorderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0F172A',
                    titleColor: '#FFFFFF',
                    bodyColor: '#FF3300',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'TSh ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000) + 'M';
                            if (value >= 1000) return (value / 1000) + 'k';
                            return value;
                        },
                        color: '#64748B',
                        font: { size: 11 }
                    },
                    grid: {
                        color: '#F1F5F9',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        color: '#64748B',
                        font: { size: 11 },
                        maxTicksLimit: 8
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
