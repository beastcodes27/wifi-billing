@extends('layouts.app')

@section('title', 'Mteja Mpya')

@section('content')

<div style="max-width:640px;">

    {{-- Breadcrumb --}}
    <div style="margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem; font-size:0.88rem; color:var(--text-muted);">
        <a href="{{ route('admin.customers.index') }}" style="color:var(--accent-primary); text-decoration:none; font-weight:500;">Customers</a>
        <i class="fas fa-chevron-right" style="font-size:0.7rem;"></i>
        <span>Add New</span>
    </div>

    <div class="banking-card p-0" style="overflow:hidden;">

        {{-- Card Header --}}
        <div style="padding:1.25rem 1.4rem; border-bottom:1px solid var(--border-color); background:#FAFAFC; display:flex; align-items:center; gap:0.9rem;">
            <div style="width:42px; height:42px; border-radius:10px; background:rgba(37,99,235,0.1); color:var(--primary-blue); display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex-shrink:0;">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h2 style="margin:0; font-size:1.1rem; font-weight:700; color:var(--text-main);">Ongeza Mteja Mpya</h2>
                <p style="margin:0.2rem 0 0; font-size:0.8rem; color:var(--text-muted);">Sajili taarifa za mteja mpya wa mtandao</p>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.customers.store') }}" method="POST" style="padding:1.4rem;">
            @csrf

            @if($errors->any())
            <div class="banking-card mb-4" style="background:#FEF2F2; border-color:#FECACA; color:#991B1B; padding:0.9rem 1.2rem; font-size:0.88rem;">
                <div class="d-flex align-items-center gap-2 font-weight-bold">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Tafadhali rekebisha makosa yafuatayo:</strong>
                </div>
                <ul style="margin:0.5rem 0 0 1.2rem; padding:0; font-size:0.84rem;">
                    @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem;">

                {{-- Full Name --}}
                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                        Jina Kamili <span style="color:var(--danger);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Mfano: Amina Mohamed"
                           class="form-control"
                           style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
                    @error('name')<p style="color:var(--danger); font-size:0.8rem; margin:0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                {{-- Phone Number --}}
                <div>
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                        Namba ya Simu <span style="color:var(--danger);">*</span>
                    </label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                           placeholder="Mfano: 0762345678"
                           class="form-control"
                           style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
                    @error('phone_number')<p style="color:var(--danger); font-size:0.8rem; margin:0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                        Barua Pepe (Email) <span style="color:var(--text-muted); font-weight:400;">(hiari)</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="Mfano: amina@gmail.com"
                           class="form-control"
                           style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%;">
                    @error('email')<p style="color:var(--danger); font-size:0.8rem; margin:0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                {{-- Notes --}}
                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:0.4rem;">
                        Maelezo ya Ziada (Notes) <span style="color:var(--text-muted); font-weight:400;">(hiari)</span>
                    </label>
                    <textarea name="notes" rows="2"
                              placeholder="Maelezo yoyote kuhusu mteja huyu..."
                              class="form-control"
                              style="border-radius:10px; padding:0.75rem 1rem; font-size:0.9rem; border:1px solid var(--border-color); width:100%; resize:vertical;">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex; gap:0.75rem; margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid var(--border-color); flex-wrap:wrap; justify-content:flex-end;">
                <a href="{{ route('admin.customers.index') }}" class="btn-banking btn-secondary" style="text-decoration:none; flex:1; max-width:120px; justify-content:center;">
                    <i class="fas fa-times"></i> Ghairi
                </a>
                <button type="submit" class="btn-banking btn-primary" style="flex:2; justify-content:center;">
                    <i class="fas fa-user-plus"></i> Unda Mteja
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
