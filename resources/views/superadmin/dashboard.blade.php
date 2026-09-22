@extends('superadmin.layouts.app')

@section('title', 'Super Admin Dashboard')

@push('styles')
<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    
    .stats-grid > * {
        min-width: 0;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        z-index: 2;
    }

    .icon-blue { background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); }
    .icon-purple { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.3); }
    .icon-green { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .icon-orange { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }

    .stat-info {
        z-index: 2;
    }

    .stat-info p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-info h3 {
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--text-main);
    }
    
    .stat-trend {
        position: absolute;
        top: 1rem;
        right: 1.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .charts-grid > * {
        min-width: 0;
    }

    /* Tables Grid */
    .tables-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .tables-grid > * {
        min-width: 0;
    }

    @media(max-width: 1024px) {
        .charts-grid, .tables-grid {
            grid-template-columns: 1fr;
        }
    }

    @media(max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .charts-grid, .tables-grid {
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .welcome-banner {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1.25rem !important;
        }
        .welcome-actions {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem !important;
        }
        .welcome-actions .btn {
            width: 100%;
            justify-content: center;
            font-size: 0.85rem;
            padding: 0.6rem 0.5rem;
        }
        .glass-card {
            padding: 1rem !important;
        }
        .stat-card {
            padding: 1rem !important;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 1.25rem;
            border-radius: 12px;
        }
        .stat-info p {
            font-size: 0.7rem;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }
        .stat-info h3 {
            font-size: 1.25rem;
        }
        .stat-trend {
            top: 0.75rem;
            right: 0.75rem;
            font-size: 0.75rem;
        }
        th, td {
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
        }
        .table-header-padding {
            padding: 1.25rem !important;
        }
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        margin-top: 1rem;
    }

    /* Table Styles */
    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    th {
        padding: 1.25rem 1.5rem;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--glass-border);
    }

    td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.03);
        vertical-align: middle;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }

    .badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .badge-success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .badge-blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }

    .tenant-name {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .tenant-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.1);
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')

<!-- Welcome Banner -->
<div class="glass-card animate-fade-in" style="margin-bottom: 2.5rem; background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(139,92,246,0.1)); border: 1px solid rgba(139,92,246,0.2);">
    <div class="welcome-banner" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: clamp(1.5rem, 5vw, 2rem); font-weight: 800; margin-bottom: 0.5rem; background: linear-gradient(to right, #fff, #9ca3af); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Welcome back, {{ auth()->user()->name ?? 'Admin' }}! 👋</h1>
            <p style="color: var(--text-muted); font-size: clamp(0.9rem, 3vw, 1rem); margin: 0;">Here's what's happening with your SaaS platform today.</p>
        </div>
        <div class="welcome-actions" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #8b5cf6, #3b82f6);">
                <i class="fas fa-plus"></i> New Tenant
            </a>
            <a href="{{ route('superadmin.plans.index') }}" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--glass-border);">
                <i class="fas fa-cube"></i> Manage Plans
            </a>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <!-- Stat 1: MRR -->
    <div class="glass-card stat-card animate-fade-in delay-1" style="border-color: rgba(16,185,129,0.3); background: linear-gradient(135deg, rgba(16,185,129,0.05), transparent);">
        <div class="stat-trend" style="color: #10b981;">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="stat-icon icon-green">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <p style="color: #10b981;">Monthly Recurring Rev</p>
            <h3 style="font-size: 1.8rem; text-shadow: 0 0 20px rgba(16,185,129,0.3);">Tsh {{ number_format($mrr, 0) }}</h3>
        </div>
    </div>

    <!-- Stat 2: Total Revenue -->
    <div class="glass-card stat-card animate-fade-in delay-1">
        <div class="stat-icon icon-blue">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <p>Total Revenue (All Time)</p>
            <h3>Tsh {{ number_format($totalPlatformRevenue, 0) }}</h3>
        </div>
    </div>

    <!-- Stat 3: Total Tenants -->
    <div class="glass-card stat-card animate-fade-in delay-2">
        <div class="stat-icon icon-purple">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-info">
            <p>Total Businesses</p>
            <h3>{{ number_format($tenantsCount) }}</h3>
        </div>
    </div>

    <!-- Stat 4: Active Subs -->
    <div class="glass-card stat-card animate-fade-in delay-2">
        <div class="stat-icon icon-orange">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <p>Active Subscriptions</p>
            <h3>{{ number_format($activeSubscriptions) }}</h3>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-grid">
    <!-- Growth Chart -->
    <div class="glass-card animate-fade-in delay-3" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Tenant Growth</h3>
            <span style="font-size: 0.85rem; color: var(--text-muted);"><i class="fas fa-calendar-alt"></i> Last 6 Months</span>
        </div>
        <div class="chart-container">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    <!-- Plan Distribution Chart -->
    <div class="glass-card animate-fade-in delay-3" style="padding: 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Plan Distribution</h3>
        <div class="chart-container" style="display: flex; justify-content: center; align-items: center; padding-top: 1rem;">
            <canvas id="planChart"></canvas>
        </div>
    </div>
</div>

<!-- Dual Tables Section -->
<div class="tables-grid">
    
    <!-- Recent Tenants Table -->
    <div class="glass-card animate-fade-in delay-3" style="padding: 0 !important;">
        <div class="table-header-padding" style="padding: 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700;">Recent Signups</h3>
            <a href="{{ route('superadmin.tenants.index') }}" style="color: var(--accent-primary); text-decoration: none; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Business</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTenants as $tenant)
                    <tr>
                        <td>
                            <div class="tenant-name">
                                <div class="tenant-avatar" style="width: 32px; height: 32px; font-size: 1rem; border-radius: 8px;">
                                    {{ substr($tenant->business_name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-main);">{{ $tenant->business_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($tenant->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst($tenant->status) }}</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.9rem;">
                            {{ $tenant->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No tenants registered yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Subscriptions Table -->
    <div class="glass-card animate-fade-in delay-3" style="padding: 0 !important;">
        <div class="table-header-padding" style="padding: 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700;">Recent Payments</h3>
            <a href="{{ route('superadmin.subscriptions.index') }}" style="color: var(--accent-primary); text-decoration: none; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Business</th>
                        <th>Plan</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSubscriptions as $sub)
                    <tr>
                        <td>
                            <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);">{{ $sub->tenant->business_name ?? 'N/A' }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $sub->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <span class="badge badge-blue">{{ $sub->plan->name ?? 'Custom' }}</span>
                        </td>
                        <td style="text-align: right; color: #10b981; font-weight: 700; font-size: 0.95rem;">
                            Tsh {{ number_format($sub->amount_paid, 0) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No recent payments.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Shared Chart Settings
        Chart.defaults.color = '#9ca3af';
        Chart.defaults.font.family = "'Outfit', sans-serif";

        // Growth Line Chart
        const growthCtx = document.getElementById('growthChart').getContext('2d');
        
        // Gradient for line chart
        let gradientBlue = growthCtx.createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'New Tenants',
                    data: {!! json_encode($tenantGrowth) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: gradientBlue,
                    borderWidth: 3,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleFont: { size: 13, family: "'Outfit', sans-serif" },
                        bodyFont: { size: 14, weight: 'bold', family: "'Outfit', sans-serif" },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false }
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // Plan Distribution Doughnut Chart
        const planCtx = document.getElementById('planChart').getContext('2d');
        new Chart(planCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($planNames) !!},
                datasets: [{
                    data: {!! json_encode($planCounts) !!},
                    backgroundColor: [
                        '#8b5cf6', // Purple
                        '#3b82f6', // Blue
                        '#10b981', // Green
                        '#f59e0b', // Orange
                        '#ef4444'  // Red
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12, family: "'Outfit', sans-serif" }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        bodyFont: { size: 14, weight: 'bold', family: "'Outfit', sans-serif" }
                    }
                }
            }
        });
    });
</script>
@endpush
