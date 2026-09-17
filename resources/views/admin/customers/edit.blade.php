@extends('layouts.app')

@section('title', 'Hariri Mteja: ' . $customer->name)

@section('content')

<div style="max-width:640px;">

    {{-- Breadcrumb --}}
    <div style="margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem; font-size:0.88rem; color:var(--text-muted);">
        <a href="{{ route('admin.customers.index') }}" style="color:var(--primary-blue); text-decoration:none; font-weight:500;">Wateja</a>
        <i class="fas fa-chevron-right" style="font-size:0.7rem;"></i>
        <a href="{{ route('admin.customers.show', $customer) }}" style="color:var(--primary-blue); text-decoration:none; font-weight:500;">{{ $customer->name }}</a>
        <i class="fas fa-chevron-right" style="font-size:0.7rem;"></i>
        <span>Hariri</span>
    </div>

    <div class="banking-card p-0" style="overflow:hidden;">

        <div style="padding:1.25rem 1.4rem; border-bottom:1px solid var(--border-color); background:#FAFAFC; display:flex; align-items:center; gap:0.9rem;">
            <div style="width:42px; height:42px; border-radius:10px; background:rgba(245,158,11,0.1); color:var(--warning); display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex-shrink:0;">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h2 style="margin:0; font-size:1.1rem; font-weight:700; color:var(--text-main);">Hariri Mteja</h2>
                <p style="margin:0.2rem 0 0; font-size:0.8rem; color:var(--text-muted);">{{ $customer->name }}</p>
            </div>
        </div>

        <form action="{{ route('admin.customers.update', $customer) }}" method="POST" style="padding:1.4rem;">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="banking-card mb-4" style="background:#FEF2F2; border-color:#FECACA; color:#991B1B; padding:0.9rem 1.2rem; font-size:0.88rem;">
                <div class="d-flex align-items-center gap-2 font-weight-bold">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Tafadhali rekebisha makosa:</strong>
                </div>
                <ul style="margin:0.5rem 0 0 1.2rem; padding:0; font-size:0.84rem;">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem;">

                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                        Jina Kamili <span style="color:var(--danger);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="form-control"
                           style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
                    @error('name')<p style="color:var(--danger); font-size:0.8rem; margin:0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                        Namba ya Simu <span style="color:var(--danger);">*</span>
                    </label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $customer->phone_number) }}" required
                           class="form-control"
                           style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
                    @error('phone_number')<p style="color:var(--danger); font-size:0.8rem; margin:0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                        Barua Pepe (Email)
                    </label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="form-control"
                           style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
                    @error('email')<p style="color:var(--danger); font-size:0.8rem; margin:0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                        Status <span style="color:var(--danger);">*</span>
                    </label>
                    <select name="status" class="form-control" style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%; background:var(--card-bg);">
                        @foreach(['active'=>'Active','expired'=>'Expired','suspended'=>'Suspended','blocked'=>'Blocked'] as $s => $label)
                        <option value="{{ $s }}" {{ old('status', $customer->status) === $s ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p style="color:var(--danger); font-size:0.8rem; margin:0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">Maelezo (Notes)</label>
                    <textarea name="notes" rows="2"
                              class="form-control"
                              style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%; resize:vertical;">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>

            <div style="display:flex; gap:0.75rem; margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid var(--border-color); flex-wrap:wrap; justify-content:flex-end;">
                <a href="{{ route('admin.customers.show', $customer) }}"
                   class="btn-banking btn-secondary"
                   style="text-decoration:none; flex:1; max-width:120px; justify-content:center;">
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
