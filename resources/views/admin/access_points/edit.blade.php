@extends('layouts.app')

@section('title', 'Hariri Access Point')

@section('content')
<style>
    .ap-field label { display:block; font-size:0.78rem; font-weight:700; color:var(--text-secondary); margin-bottom:0.3rem; }
    .ap-field input, .ap-field select, .ap-field textarea { width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:10px; font-size:0.88rem; color:var(--text-main); background:#fff; outline:none; }
    .ap-field input:focus, .ap-field select:focus, .ap-field textarea:focus { border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
    .ap-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:0.9rem; }
    @media (max-width:640px){ .ap-grid2{grid-template-columns:1fr;} }
</style>

<div class="page-header-compact" style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
    <div>
        <h2><i class="fas fa-pen"></i> Hariri Access Point</h2>
        <p>{{ $accessPoint->brand }} @if($accessPoint->model)· {{ $accessPoint->model }}@endif</p>
    </div>
    <a href="{{ route('admin.access_points.index') }}" class="btn-banking" style="border:1px solid var(--border-color); background:#fff; color:var(--text-secondary); white-space:nowrap;">
        <i class="fas fa-arrow-left"></i> Rudi
    </a>
</div>

@if($errors->any())
<div class="banking-card mb-3" style="background:#FEF2F2; border-color:#FECACA; color:#991B1B; padding:0.8rem 1rem; border-radius:10px; font-size:0.84rem;">
    <ul style="margin:0 0 0 1rem; padding:0;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.access_points.update', $accessPoint) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="banking-card p-3 p-md-4">
        <div class="ap-grid2" style="margin-bottom:0.9rem;">
            <div class="ap-field">
                <label for="brand">Aina ya Kifaa (Brand)</label>
                <select name="brand" id="brand" required>
                    @foreach($brands as $b)
                        <option value="{{ $b }}" {{ old('brand', $accessPoint->brand) === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ap-field">
                <label for="site_id">Site (Eneo)</label>
                <select name="site_id" id="site_id">
                    <option value="">— Hakuna / Chagua —</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ (string)old('site_id', $accessPoint->site_id) === (string)$site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="ap-grid2" style="margin-bottom:0.9rem;">
            <div class="ap-field">
                <label for="name">Jina la Kifaa</label>
                <input type="text" name="name" id="name" value="{{ old('name', $accessPoint->name) }}">
            </div>
            <div class="ap-field">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" value="{{ old('model', $accessPoint->model) }}">
            </div>
        </div>

        <div class="ap-grid2" style="margin-bottom:0.9rem;">
            <div class="ap-field">
                <label for="serial_number">Serial Number (S/N)</label>
                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $accessPoint->serial_number) }}">
            </div>
            <div class="ap-field">
                <label for="device_key">Device Key</label>
                <input type="text" name="device_key" id="device_key" value="{{ old('device_key', $accessPoint->device_key) }}">
            </div>
        </div>

        <div class="ap-grid2" style="margin-bottom:0.9rem;">
            <div class="ap-field">
                <label for="mac_address">MAC Address</label>
                <input type="text" name="mac_address" id="mac_address" value="{{ old('mac_address', $accessPoint->mac_address) }}">
            </div>
            <div class="ap-field">
                <label for="management_ip">Management IP</label>
                <input type="text" name="management_ip" id="management_ip" value="{{ old('management_ip', $accessPoint->management_ip) }}">
            </div>
        </div>

        <div class="ap-field" style="margin-bottom:1.1rem;">
            <label for="notes">Maelezo (hiari)</label>
            <textarea name="notes" id="notes" rows="2">{{ old('notes', $accessPoint->notes) }}</textarea>
        </div>

        <div style="display:flex; gap:0.6rem;">
            <button type="submit" class="btn-banking btn-primary" style="justify-content:center; flex:1;">
                <i class="fas fa-save"></i> Hifadhi Mabadiliko
            </button>
            @if($accessPoint->adoption_status !== 'adopted')
            <button type="submit" form="adopt-form" class="btn-banking" style="justify-content:center; background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0;">
                <i class="fas fa-plug"></i> Adopt Sasa
            </button>
            @endif
        </div>
    </div>
</form>

@if($accessPoint->adoption_status !== 'adopted')
<form id="adopt-form" action="{{ route('admin.access_points.adopt', $accessPoint) }}" method="POST" style="display:none;">
    @csrf
</form>
@endif
@endsection
