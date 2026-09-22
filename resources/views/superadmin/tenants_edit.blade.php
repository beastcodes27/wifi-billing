@extends('superadmin.layouts.app')

@section('title', 'Edit Business')

@section('content')
<div class="header animate-fade-in">
    <div class="header-title">
        <h1><a href="{{ route('superadmin.tenants.index') }}" style="color: var(--text-muted); text-decoration: none;"><i class="fas fa-arrow-left" style="font-size: 1.2rem; margin-right: 0.5rem;"></i> Tenants</a> / Edit Business</h1>
        <p>Update settings for {{ $tenant->business_name }}</p>
    </div>
</div>

<div class="glass-card animate-fade-in delay-1" style="max-width: 800px;">
    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(239, 68, 68, 0.2);">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.tenants.update', $tenant) }}" method="POST">
        @csrf
        @method('PUT')
        
        <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">Business Details</h3>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Business Name</label>
            <input type="text" name="business_name" value="{{ old('business_name', $tenant->business_name) }}" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); color: var(--text-main); font-family: 'Outfit', sans-serif;">
        </div>

        <div style="margin-bottom: 2.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Status</label>
            <select name="status" required style="width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); color: var(--text-main); font-family: 'Outfit', sans-serif;">
                <option value="active" {{ $tenant->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ $tenant->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
