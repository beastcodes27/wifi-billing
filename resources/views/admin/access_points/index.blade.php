@extends('layouts.app')

@section('title', 'Access Points')

@section('content')
<div class="page-header-compact" style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
    <div>
        <h2><i class="fas fa-tower-broadcast" style="color:var(--primary-blue);"></i> Access Points</h2>
        <p>Usajili wa vifaa vya WiFi (MikroTik, UniFi, Omada) — weka kwa mkono au kwa kupiga picha ya sticker</p>
    </div>
    <a href="{{ route('admin.access_points.create') }}" class="btn-banking btn-primary" style="white-space:nowrap;">
        <i class="fas fa-plus"></i> Ongeza Access Point
    </a>
</div>

@if(session('success'))
<div class="banking-card mb-3" style="background:#ECFDF5; border-color:#A7F3D0; color:#065F46; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.6rem; border-radius:10px;">
    <i class="fas fa-check-circle" style="color:#059669;"></i>
    <span style="font-weight:600; font-size:0.85rem;">{{ session('success') }}</span>
</div>
@endif

@php
    $brandIcon = function ($brand) {
        return match (true) {
            str_contains($brand, 'MikroTik') => 'fa-network-wired',
            str_contains($brand, 'UniFi') => 'fa-wifi',
            str_contains($brand, 'Omada') => 'fa-tower-broadcast',
            default => 'fa-microchip',
        };
    };
@endphp

<div class="banking-card p-3 p-md-4">
    @if($accessPoints->isEmpty())
        <div style="text-align:center; padding:2.5rem 1rem;">
            <div style="width:64px; height:64px; border-radius:18px; background:rgba(255,51,0,0.08); color:var(--primary-blue, #2563EB); display:inline-flex; align-items:center; justify-content:center; font-size:1.7rem; margin-bottom:1rem;">
                <i class="fas fa-tower-broadcast"></i>
            </div>
            <h3 style="font-weight:800; color:var(--text-main); margin-bottom:0.35rem;">Hakuna Access Point Bado</h3>
            <p style="color:var(--text-muted); font-size:0.88rem; max-width:440px; margin:0 auto 1.25rem;">
                Sajili kifaa chako cha kwanza. Unaweza kuandika taarifa kwa mkono, au kupiga picha ya sticker na AI ijaze Serial na Device Key kwa ajili yako.
            </p>
            <a href="{{ route('admin.access_points.create') }}" class="btn-banking btn-primary" style="display:inline-flex;">
                <i class="fas fa-camera"></i> Sajili Access Point ya Kwanza
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table style="width:100%; border-collapse:separate; border-spacing:0;">
                <thead>
                    <tr>
                        <th style="text-align:left; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); padding:0.6rem 0.75rem; border-bottom:1px solid var(--border-color);">Kifaa</th>
                        <th style="text-align:left; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); padding:0.6rem 0.75rem; border-bottom:1px solid var(--border-color);">Serial / Device Key</th>
                        <th style="text-align:left; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); padding:0.6rem 0.75rem; border-bottom:1px solid var(--border-color);">Site</th>
                        <th style="text-align:left; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); padding:0.6rem 0.75rem; border-bottom:1px solid var(--border-color);">Hali</th>
                        <th style="text-align:right; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); padding:0.6rem 0.75rem; border-bottom:1px solid var(--border-color);">Vitendo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accessPoints as $ap)
                    <tr>
                        <td style="padding:0.75rem; border-bottom:1px solid var(--border-color);">
                            <div style="display:flex; align-items:center; gap:0.65rem;">
                                <div style="width:38px; height:38px; border-radius:10px; background:rgba(37,99,235,0.1); color:#2563EB; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <i class="fas {{ $brandIcon($ap->brand) }}"></i>
                                </div>
                                <div>
                                    <div style="font-weight:800; color:var(--text-main); font-size:0.9rem;">{{ $ap->name ?: ($ap->model ?: 'Access Point') }}</div>
                                    <div style="font-size:0.76rem; color:var(--text-muted);">
                                        {{ $ap->brand }}@if($ap->model) · {{ $ap->model }}@endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:0.75rem; border-bottom:1px solid var(--border-color);">
                            <div style="font-family:monospace; font-size:0.8rem; color:var(--text-main);">{{ $ap->serial_number ?: '—' }}</div>
                            <div style="font-size:0.74rem; color:var(--text-muted);">
                                Key: {{ $ap->device_key ? '••••' . substr($ap->device_key, -4) : '—' }}
                            </div>
                        </td>
                        <td style="padding:0.75rem; border-bottom:1px solid var(--border-color); font-size:0.82rem; color:var(--text-secondary);">
                            {{ $ap->site->name ?? '—' }}
                        </td>
                        <td style="padding:0.75rem; border-bottom:1px solid var(--border-color);">
                            @if($ap->adoption_status === 'adopted')
                                <span class="badge-status badge-success">Imeadoptiwa</span>
                            @else
                                <span class="badge-status" style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;">Inasubiri</span>
                            @endif
                            @if($ap->adoption_method)
                                <div style="font-size:0.7rem; color:var(--text-muted); margin-top:3px; text-transform:capitalize;">
                                    {{ str_replace('_', ' ', $ap->adoption_method) }}
                                    @if($ap->scan_confidence) · {{ round($ap->scan_confidence) }}% @endif
                                </div>
                            @endif
                        </td>
                        <td style="padding:0.75rem; border-bottom:1px solid var(--border-color); text-align:right;">
                            <div style="display:inline-flex; gap:0.4rem; align-items:center;">
                                @if($ap->adoption_status !== 'adopted')
                                <form action="{{ route('admin.access_points.adopt', $ap) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn-banking btn-primary" style="padding:0.4rem 0.75rem; font-size:0.76rem;">
                                        <i class="fas fa-plug"></i> Adopt
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('admin.access_points.edit', $ap) }}" class="btn-banking" style="padding:0.4rem 0.6rem; font-size:0.78rem; border:1px solid var(--border-color); background:#fff; color:var(--text-secondary);">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.access_points.destroy', $ap) }}" method="POST" style="margin:0;" onsubmit="return confirm('Una uhakika unataka kufuta access point hii?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-banking" style="padding:0.4rem 0.6rem; font-size:0.78rem; border:1px solid #FECACA; background:#FFF5F5; color:#DC2626;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
