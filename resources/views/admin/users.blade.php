@extends('layouts.app')

@section('title', 'Miunganisho')

@section('content')

{{-- Header --}}
<div class="page-header-compact">
    <div>
        <h2><i class="fas fa-wifi" style="color:var(--primary-blue);"></i> Miunganisho</h2>
        <p>Vikao vya data na muda wa vocha</p>
    </div>
    <div class="d-flex gap-1.5 flex-wrap">
        @php
            $onlineCount  = $users->where('is_online', true)->count();
            $offlineCount = $users->where('is_online', false)->count();
        @endphp
        <span class="badge-status badge-success" style="font-size:0.75rem; padding:0.3rem 0.65rem;">
            <i class="fas fa-circle" style="font-size:0.45rem; margin-right:3px;"></i> {{ $onlineCount }} Online
        </span>
        <span class="badge-status badge-secondary" style="font-size:0.75rem; padding:0.3rem 0.65rem;">
            <i class="fas fa-circle" style="font-size:0.45rem; margin-right:3px;"></i> {{ $offlineCount }} Offline
        </span>
    </div>
</div>

@php
    if (!function_exists('formatBytesHelper')) {
        function formatBytesHelper($bytes, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow   = min($pow, count($units) - 1);
            $bytes /= pow(1024, $pow);
            return round($bytes, $precision) . ' ' . $units[$pow];
        }
    }
@endphp

{{-- Main Card --}}
<div class="banking-card p-0" style="overflow:hidden;">
    <div style="padding:1.1rem 1.4rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; background:#FAFAFC;">
        <span style="font-weight:700; font-size:0.95rem; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-wifi" style="color:var(--primary-blue);"></i> Orodha ya Vifaa & Vocha Zilizounganishwa
        </span>
        <span class="badge-status badge-secondary">{{ $users->total() }} rekodi</span>
    </div>

    {{-- Desktop Table View --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table-banking">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Mteja / Simu / MAC</th>
                    <th>Kifurushi</th>
                    <th style="text-align:center;">Hali</th>
                    <th>Data Iliyotumika</th>
                    <th>Muda wa Kikao</th>
                    <th>Kuunganika</th>
                    <th>Muda wa Vocha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                @php
                    $expiresAt = $user->expires_at;
                    $timeLeftSeconds = 0;
                    $timeLeftLabel = '—';
                    $timeLeftClass = 'badge-secondary';
                    $timeLeftIcon = 'fa-clock';

                    if ($expiresAt) {
                        $timeLeftSeconds = max(0, now()->diffInSeconds($expiresAt, false));

                        if ($timeLeftSeconds <= 0) {
                            $timeLeftLabel = 'Imeisha';
                            $timeLeftClass = 'badge-danger';
                            $timeLeftIcon = 'fa-times-circle';
                        } elseif ($timeLeftSeconds < 600) {
                            $mins = (int) ceil($timeLeftSeconds / 60);
                            $timeLeftLabel = "Dak. {$mins} tu!";
                            $timeLeftClass = 'badge-danger';
                            $timeLeftIcon = 'fa-exclamation-triangle';
                        } elseif ($timeLeftSeconds < 3600) {
                            $mins = (int) floor($timeLeftSeconds / 60);
                            $timeLeftLabel = "Dak. {$mins}";
                            $timeLeftClass = 'badge-warning';
                            $timeLeftIcon = 'fa-hourglass-half';
                        } elseif ($timeLeftSeconds < 86400) {
                            $hrs  = (int) floor($timeLeftSeconds / 3600);
                            $mins = (int) floor(($timeLeftSeconds % 3600) / 60);
                            $timeLeftLabel = "Saa {$hrs}h {$mins}m";
                            $timeLeftClass = 'badge-success';
                            $timeLeftIcon = 'fa-clock';
                        } else {
                            $days = (int) floor($timeLeftSeconds / 86400);
                            $timeLeftLabel = "Siku {$days}";
                            $timeLeftClass = 'badge-success';
                            $timeLeftIcon = 'fa-clock';
                        }
                    }
                @endphp
                <tr>
                    <td style="color:var(--text-muted); font-size:0.8rem; font-weight:700;">
                        {{ $users->firstItem() + $i }}
                    </td>
                    <td>
                        <div style="font-weight:700; color:var(--text-main); font-size:0.9rem;">
                            {{ $user->phone_number }}
                        </div>
                        @if($user->mikrotik_username)
                        <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace; margin-top:2px;">
                            <i class="fas fa-ticket-alt" style="color:var(--primary-blue);"></i> {{ $user->mikrotik_username }}
                        </div>
                        @endif
                        @if($user->mac_address && strpos($user->mac_address, '-address') === false)
                        <div style="font-size:0.72rem; color:var(--text-muted); font-family:monospace;">
                            <i class="fas fa-laptop" style="font-size:0.65rem;"></i> {{ $user->mac_address }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:600; color:var(--text-main); font-size:0.875rem;">
                            {{ $user->package ? $user->package->name : '—' }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if($user->is_online)
                            <span class="badge-status badge-success">
                                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--success); margin-right:4px;"></span> Online
                            </span>
                        @else
                            <span class="badge-status badge-secondary">
                                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--text-muted); margin-right:4px;"></span> Offline
                            </span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:800; color:var(--primary-blue); font-size:0.875rem;">
                            {{ formatBytesHelper($user->data_used) }}
                        </span>
                    </td>
                    <td>
                        <span style="color:var(--text-muted); font-family:monospace; font-size:0.82rem;">
                            {{ $user->session_time ?: '—' }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_online && $user->activated_at)
                            <div style="color:var(--success); font-weight:700; font-size:0.8rem;">
                                {{ $user->activated_at->format('d M, H:i') }}
                            </div>
                            <div style="font-size:0.72rem; color:var(--text-muted);">{{ $user->activated_at->diffForHumans() }}</div>
                        @elseif($user->last_logout_at)
                            <div style="color:var(--text-muted); font-size:0.8rem;">
                                {{ $user->last_logout_at->format('d M, H:i') }}
                            </div>
                            <div style="font-size:0.72rem; color:var(--text-muted);">{{ $user->last_logout_at->diffForHumans() }}</div>
                        @else
                            <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($expiresAt)
                            <span class="badge-status {{ $timeLeftClass }}" style="font-size:0.75rem;">
                                <i class="fas {{ $timeLeftIcon }}"></i> {{ $timeLeftLabel }}
                            </span>
                            <div style="font-size:0.72rem; color:var(--text-muted); margin-top:2px;">
                                Inaisha: {{ $expiresAt->format('d M, H:i') }}
                            </div>
                        @else
                            <span style="color:var(--text-muted); font-size:0.82rem;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:3.5rem 1rem;">
                        <div style="color:var(--text-muted);">
                            <i class="fas fa-users" style="font-size:2.6rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                            <div style="font-weight:700; font-size:1.05rem; color:var(--text-main); margin-bottom:0.3rem;">Hakuna Historia ya Miunganisho</div>
                            <div style="font-size:0.875rem;">Wateja wakijiunga na Wi-Fi taarifa zao za mtandao zitaonekana hapa.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Responsive Card Feed --}}
    <div class="d-md-none">
        @forelse($users as $user)
        @php
            $mExpiresAt = $user->expires_at;
            $mTimeLeftSeconds = 0;
            $mTimeLeftLabel = '—';
            $mTimeLeftClass = 'badge-secondary';
            $mTimeLeftIcon = 'fa-clock';

            if ($mExpiresAt) {
                $mTimeLeftSeconds = max(0, now()->diffInSeconds($mExpiresAt, false));

                if ($mTimeLeftSeconds <= 0) {
                    $mTimeLeftLabel = 'Imeisha';
                    $mTimeLeftClass = 'badge-danger';
                    $mTimeLeftIcon = 'fa-times-circle';
                } elseif ($mTimeLeftSeconds < 600) {
                    $mins = (int) ceil($mTimeLeftSeconds / 60);
                    $mTimeLeftLabel = "Dak. {$mins} tu!";
                    $mTimeLeftClass = 'badge-danger';
                    $mTimeLeftIcon = 'fa-exclamation-triangle';
                } elseif ($mTimeLeftSeconds < 3600) {
                    $mins = (int) floor($mTimeLeftSeconds / 60);
                    $mTimeLeftLabel = "Dak. {$mins}";
                    $mTimeLeftClass = 'badge-warning';
                    $mTimeLeftIcon = 'fa-hourglass-half';
                } elseif ($mTimeLeftSeconds < 86400) {
                    $hrs  = (int) floor($mTimeLeftSeconds / 3600);
                    $mins = (int) floor(($mTimeLeftSeconds % 3600) / 60);
                    $mTimeLeftLabel = "Saa {$hrs}h {$mins}m";
                    $mTimeLeftClass = 'badge-success';
                    $mTimeLeftIcon = 'fa-clock';
                } else {
                    $days = (int) floor($mTimeLeftSeconds / 86400);
                    $mTimeLeftLabel = "Siku {$days}";
                    $mTimeLeftClass = 'badge-success';
                    $mTimeLeftIcon = 'fa-clock';
                }
            }
        @endphp
        <div style="padding:0.75rem 0.9rem; border-bottom:1px solid var(--border-color); display:flex; flex-direction:column; gap:0.5rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div style="min-width:0;">
                    <div style="font-weight:700; font-size:0.95rem; color:var(--text-main); display:flex; align-items:center; gap:0.4rem;">
                        <i class="fas fa-mobile-alt" style="color:var(--primary-blue); font-size:0.85rem;"></i>
                        <span>{{ $user->phone_number }}</span>
                    </div>
                    @if($user->mikrotik_username)
                        <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace; margin-top:2px;">
                            <i class="fas fa-ticket-alt" style="color:var(--text-muted); font-size:0.7rem;"></i> Vocha: {{ $user->mikrotik_username }}
                        </div>
                    @endif
                </div>
                <div class="d-flex flex-column align-items-end gap-1">
                    @if($user->is_online)
                        <span class="badge-status badge-success" style="font-size:0.7rem;">
                            <span style="display:inline-block; width:5px; height:5px; border-radius:50%; background:currentColor; margin-right:3px;"></span> Online
                        </span>
                    @else
                        <span class="badge-status badge-secondary" style="font-size:0.7rem;">Offline</span>
                    @endif
                    @if($user->package)
                        <span class="badge-status badge-info" style="font-size:0.68rem; padding:0.15rem 0.45rem;">
                            {{ $user->package->name }}
                        </span>
                    @endif
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; background:#F8FAFC; border-radius:8px; padding:0.5rem 0.75rem; font-size:0.78rem;">
                <div>
                    <span style="color:var(--text-muted); display:block; font-size:0.7rem;">Data Iliyotumika</span>
                    <strong style="color:var(--primary-blue);">{{ formatBytesHelper($user->data_used) }}</strong>
                </div>
                <div>
                    <span style="color:var(--text-muted); display:block; font-size:0.7rem;">Muda wa Kikao</span>
                    <strong style="color:var(--text-main); font-family:monospace;">{{ $user->session_time ?: '—' }}</strong>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-1" style="font-size:0.78rem;">
                <div style="color:var(--text-muted); font-size:0.74rem;">
                    @if($user->is_online && $user->activated_at)
                        <i class="fas fa-play" style="color:var(--success); font-size:0.65rem;"></i> {{ $user->activated_at->diffForHumans() }}
                    @elseif($user->last_logout_at)
                        <i class="fas fa-power-off" style="font-size:0.65rem;"></i> {{ $user->last_logout_at->diffForHumans() }}
                    @else
                        <span>—</span>
                    @endif
                </div>
                @if($mExpiresAt)
                    <span class="badge-status {{ $mTimeLeftClass }}" style="font-size:0.72rem;">
                        <i class="fas {{ $mTimeLeftIcon }}" style="font-size:0.68rem;"></i> {{ $mTimeLeftLabel }}
                    </span>
                @endif
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:3rem 1rem; color:var(--text-muted);">
            <i class="fas fa-users" style="font-size:2.2rem; opacity:0.3; margin-bottom:0.75rem; display:block;"></i>
            <div style="font-weight:700; color:var(--text-main);">Hakuna Miunganisho</div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div style="padding:1rem 1.4rem; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem; background:#FAFAFC;">
        <div style="color:var(--text-muted); font-size:0.85rem;">
            Inaonyesha <strong>{{ $users->firstItem() }}</strong>–<strong>{{ $users->lastItem() }}</strong> kati ya <strong>{{ $users->total() }}</strong>
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
    @endif
</div>

@endsection
