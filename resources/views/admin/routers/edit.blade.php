@extends('layouts.app')

@section('title', 'Hariri Router: ' . $router->name)

@section('content')

<div style="max-width:840px; margin:0 auto;">

    {{-- Breadcrumb Navigation --}}
    <div class="d-flex align-items-center gap-2 mb-3" style="font-size:0.875rem; color:var(--text-muted);">
        <a href="{{ route('admin.routers.index') }}" style="color:#FF3300; text-decoration:none; font-weight:700;">Routers</a>
        <i class="fas fa-chevron-right" style="font-size:0.65rem;"></i>
        <span>{{ $router->name }}</span>
        <i class="fas fa-chevron-right" style="font-size:0.65rem;"></i>
        <span style="font-weight:600; color:var(--text-main);">Hariri</span>
    </div>

    <div class="banking-card p-0" style="overflow:hidden; border-radius:16px;">

        {{-- Card Header --}}
        <div style="padding:1.4rem 1.8rem; border-bottom:1px solid var(--border-color); background:#FAFAFC; display:flex; align-items:center; gap:1rem;">
            <div style="width:44px; height:44px; border-radius:12px; background:rgba(255, 51, 0, 0.1); color:#FF3300; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--text-main);">Hariri Router: {{ $router->name }}</h3>
                <p style="margin:0.2rem 0 0; font-size:0.84rem; color:var(--text-muted);">Sasisha taarifa za msingi, eneo na IP</p>
            </div>
        </div>

        <form action="{{ route('admin.routers.update', $router) }}" method="POST" style="padding:1.8rem;">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="banking-card mb-4" style="background:#FEF2F2; border-color:#FECACA; color:#991B1B; padding:1rem 1.25rem;">
                <div class="d-flex align-items-center gap-2 font-weight-bold" style="font-size:0.9rem;">
                    <i class="fas fa-exclamation-triangle"></i> Tafadhali rekebisha makosa yafuatayo:
                </div>
                <ul style="margin:0.5rem 0 0 1.25rem; padding:0; font-size:0.85rem;">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="mb-4">
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:1rem;">
                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Site (Eneo la Router) <span style="color:var(--danger);">*</span>
                        </label>
                        <select name="site_id" required class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); background:var(--card-bg);">
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ (old('site_id', $router->site_id) == $site->id) ? 'selected' : '' }}>{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Aina ya Router <span style="color:var(--danger);">*</span>
                        </label>
                        <select name="router_type" required class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); background:var(--card-bg);">
                            <option value="MikroTik" {{ old('router_type', $router->router_type) == 'MikroTik' ? 'selected' : '' }}>MikroTik (RouterOS)</option>
                            <option value="Ubiquiti" {{ old('router_type', $router->router_type) == 'Ubiquiti' ? 'selected' : '' }}>Ubiquiti EdgeRouter</option>
                            <option value="Other" {{ old('router_type', $router->router_type) == 'Other' ? 'selected' : '' }}>Nyingine</option>
                        </select>
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Jina la Router <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $router->name) }}" required class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color);">
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Management IP (Hiari)
                        </label>
                        <input type="text" name="management_ip" value="{{ old('management_ip', $router->management_ip) }}" placeholder="192.168.88.1" class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); font-family:monospace;">
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            API Host / Port (Hiari)
                        </label>
                        <div style="display:flex;">
                            <input type="text" name="api_host" value="{{ old('api_host', $router->api_host) }}" placeholder="Host / IP" class="form-control" style="border-radius:10px 0 0 10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); font-family:monospace; flex:1;">
                            <input type="number" name="api_port" value="{{ old('api_port', $router->api_port ?? 8728) }}" placeholder="8728" class="form-control" style="max-width:90px; border-radius:0 10px 10px 0; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); font-family:monospace;">
                        </div>
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Maelezo ya Ziada (Notes)
                        </label>
                        <textarea name="notes" rows="2" class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color);">{{ old('notes', $router->notes) }}</textarea>
                    </div>

                    <div style="grid-column:1 / -1; margin-top:0.25rem;">
                        <div style="background:#F8FAFC; border:1px solid var(--border-color); border-radius:12px; padding:0.9rem 1.1rem; display:flex; align-items:center; gap:0.75rem;">
                            <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $router->status === 'active') ? 'checked' : '' }} style="width:20px; height:20px; cursor:pointer; accent-color:#FF3300;">
                            <label for="is_active" style="cursor:pointer; margin:0;">
                                <strong style="color:var(--text-main); font-size:0.9rem;">Router ipo hewani (Active)</strong>
                                <div style="font-size:0.8rem; color:var(--text-muted);">Ukiizima hapa, maombi ya usawazishaji yatasitishwa</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div style="display:flex; gap:0.75rem; padding-top:1.25rem; border-top:1px solid var(--border-color); justify-content:flex-end; flex-wrap:wrap;">
                <a href="{{ route('admin.routers.index') }}" class="btn-banking btn-secondary" style="text-decoration:none; flex:1; max-width:120px; text-align:center;">
                    <i class="fas fa-times"></i> Ghairi
                </a>
                <button type="submit" class="btn-banking btn-primary" style="flex:2; justify-content:center;">
                    <i class="fas fa-save"></i> Hifadhi Mabadiliko
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
