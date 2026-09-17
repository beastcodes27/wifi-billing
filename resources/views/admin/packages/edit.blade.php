@extends('layouts.app')

@section('title', 'Hariri Kifurushi: ' . $package->name)

@section('content')

<div style="max-width:840px; margin:0 auto;">

    {{-- Breadcrumb Navigation --}}
    <div class="d-flex align-items-center gap-2 mb-3" style="font-size:0.875rem; color:var(--text-muted);">
        <a href="{{ route('admin.packages.index') }}" style="color:#FF3300; text-decoration:none; font-weight:700;">Vifurushi</a>
        <i class="fas fa-chevron-right" style="font-size:0.65rem;"></i>
        <span>{{ $package->name }}</span>
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
                <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--text-main);">Hariri Kifurushi: {{ $package->name }}</h3>
                <p style="margin:0.2rem 0 0; font-size:0.84rem; color:var(--text-muted);">Badili bei, muda, na taarifa za ukomo wa matumizi</p>
            </div>
        </div>

        <form action="{{ route('admin.packages.update', $package) }}" method="POST" style="padding:1.4rem;">
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

            {{-- Basic Info Section --}}
            <div class="mb-4">
                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1.1rem; padding-bottom:0.5rem; border-bottom:1px solid var(--border-color);">
                    <i class="fas fa-tag" style="color:var(--primary-blue); font-size:0.9rem;"></i>
                    <h4 style="margin:0; font-size:0.95rem; font-weight:700; color:var(--text-main); text-transform:uppercase; letter-spacing:0.5px;">1. Taarifa za Msingi</h4>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:1rem;">
                    <div style="grid-column:1 / -1;">
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Jina la Kifurushi <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $package->name) }}" required class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color);">
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Bei (TSh) <span style="color:var(--danger);">*</span>
                        </label>
                        <div style="display:flex;">
                            <span style="background:#F1F5F9; border:1px solid var(--border-color); border-right:none; border-radius:10px 0 0 10px; padding:0.7rem 0.85rem; font-weight:700; font-size:0.85rem; color:var(--text-muted); display:flex; align-items:center;">TSh</span>
                            <input type="number" name="price" value="{{ old('price', $package->price) }}" min="0" step="1" required class="form-control" style="border-radius:0 10px 10px 0; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); flex:1;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Mpangilio (Display Order) <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="number" name="display_order" value="{{ old('display_order', $package->display_order) }}" min="0" required class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color);">
                        <small style="font-size:0.75rem; color:var(--text-muted);">Namba ndogo hutokea mwanzo kwenye portal</small>
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Kiasi cha Muda <span style="color:var(--danger);">*</span>
                        </label>
                        <input type="number" name="duration_value" value="{{ old('duration_value', $duration_value) }}" min="1" required class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color);">
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Kipimo cha Muda <span style="color:var(--danger);">*</span>
                        </label>
                        <select name="duration_unit" required class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); background:var(--card-bg);">
                            <option value="Minutes" {{ old('duration_unit', $duration_unit) == 'Minutes' ? 'selected' : '' }}>Dakika (Minutes)</option>
                            <option value="Hours" {{ old('duration_unit', $duration_unit) == 'Hours' ? 'selected' : '' }}>Masaa (Hours)</option>
                            <option value="Days" {{ old('duration_unit', $duration_unit) == 'Days' ? 'selected' : '' }}>Siku (Days)</option>
                            <option value="Weeks" {{ old('duration_unit', $duration_unit) == 'Weeks' ? 'selected' : '' }}>Wiki (Weeks)</option>
                            <option value="Months" {{ old('duration_unit', $duration_unit) == 'Months' ? 'selected' : '' }}>Miezi (Months)</option>
                        </select>
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Maelezo ya Ziada (Description)
                        </label>
                        <textarea name="description" rows="2" class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color);">{{ old('description', $package->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- MikroTik Network Section --}}
            <div class="mb-4">
                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1.1rem; padding-bottom:0.5rem; border-bottom:1px solid var(--border-color);">
                    <i class="fas fa-network-wired" style="color:var(--primary-blue); font-size:0.9rem;"></i>
                    <h4 style="margin:0; font-size:0.95rem; font-weight:700; color:var(--text-main); text-transform:uppercase; letter-spacing:0.5px;">2. MikroTik &amp; Ukomo wa Kasi (Rate Limit)</h4>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:1rem;">
                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            MikroTik User Profile Name
                        </label>
                        <input type="text" name="mikrotik_profile" value="{{ old('mikrotik_profile', $package->mikrotik_profile) }}" class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); font-family:monospace;">
                        <small style="font-size:0.75rem; color:var(--text-muted);">Jina la profile lililopo kwenye Router (mfn: default, 5mbps)</small>
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Ukomo wa Data (MB Quota - Hiari)
                        </label>
                        <div style="display:flex;">
                            <input type="number" name="data_quota_mb" value="{{ old('data_quota_mb', $package->data_quota_mb) }}" placeholder="1024" min="1" class="form-control" style="border-radius:10px 0 0 10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color); flex:1;">
                            <span style="background:#F1F5F9; border:1px solid var(--border-color); border-left:none; border-radius:0 10px 10px 0; padding:0.7rem 0.85rem; font-weight:700; font-size:0.85rem; color:var(--text-muted); display:flex; align-items:center;">MB</span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Download Speed Limit
                        </label>
                        <input type="text" name="download_speed" value="{{ old('download_speed', $package->download_speed) }}" placeholder="Mfano: 5M" class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color);">
                    </div>

                    <div>
                        <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                            Upload Speed Limit
                        </label>
                        <input type="text" name="upload_speed" value="{{ old('upload_speed', $package->upload_speed) }}" placeholder="Mfano: 2M" class="form-control" style="border-radius:10px; padding:0.7rem 0.95rem; font-size:0.9rem; border:1px solid var(--border-color);">
                    </div>

                    <div style="grid-column:1 / -1; margin-top:0.25rem;">
                        <div style="background:#F8FAFC; border:1px solid var(--border-color); border-radius:10px; padding:0.9rem 1.1rem; display:flex; align-items:center; gap:0.75rem;">
                            <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $package->is_active) ? 'checked' : '' }} style="width:20px; height:20px; cursor:pointer; accent-color:var(--primary-blue);">
                            <label for="is_active" style="cursor:pointer; margin:0;">
                                <strong style="color:var(--text-main); font-size:0.9rem;">Kifurushi kipo hewani (Active)</strong>
                                <div style="font-size:0.8rem; color:var(--text-muted);">Ukizima hapa, hakitaonekana kwenye ukurasa wa wateja kununua</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div style="display:flex; gap:0.75rem; padding-top:1.25rem; border-top:1px solid var(--border-color); justify-content:flex-end; flex-wrap:wrap;">
                <a href="{{ route('admin.packages.index') }}" class="btn-banking btn-secondary" style="text-decoration:none; flex:1; max-width:120px; text-align:center;">
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
