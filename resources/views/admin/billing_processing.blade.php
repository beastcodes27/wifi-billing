@extends('layouts.app')

@section('title', 'Inachakata Malipo')

@section('content')
<div class="header">
    <div class="header-title">
        <h1>Tunasubiri Malipo Yako</h1>
        <p>Tafadhali kamilisha malipo kupitia simu yako.</p>
    </div>
</div>

<div class="glass-card animate-fade-in delay-1" style="max-width: 600px; margin: 0 auto; text-align: center; padding: 4rem 2rem;">
    
    <div style="margin-bottom: 2rem;">
        <i class="fas fa-circle-notch fa-spin" style="font-size: 4rem; color: #8b5cf6;"></i>
    </div>
    
    <h3 style="color: var(--text-main); font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">
        Ombi Limeshatumwa!
    </h3>
    
    <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">
        Tumekutumia ombi kwenye namba ya simu uliyoweka. Tafadhali weka <strong>namba yako ya siri (PIN)</strong> kwenye simu yako ili kukamilisha malipo haya.
    </p>

    <div style="background: rgba(139, 92, 246, 0.05); border: 1px solid rgba(139, 92, 246, 0.2); padding: 1.5rem; border-radius: 12px; display: flex; align-items: flex-start; gap: 1rem; text-align: left; margin-bottom: 2rem;">
        <i class="fas fa-info-circle" style="color: #8b5cf6; font-size: 1.5rem; margin-top: 0.2rem;"></i>
        <div>
            <strong style="color: #c4b5fd; display: block; margin-bottom: 0.3rem;">Muhimu</strong>
            <span style="color: var(--text-muted); font-size: 0.95rem;">Ukurasa huu utajirudia (refresh) wenyewe pindi tu malipo yatakapokamilika. Tafadhali usifunge wala kutoka kwenye huu ukurasa.</span>
        </div>
    </div>

    <div>
        <a href="{{ route('admin.billing') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem; transition: color 0.3s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-muted)'">
            <i class="fas fa-times-circle"></i> Ghairi na urudi nyuma
        </a>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkStatus = () => {
            fetch("{{ route('admin.billing.status', $subscription->id) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'active') {
                        // Redirect to dashboard immediately
                        window.location.href = "{{ route('admin.dashboard') }}";
                    } else if (data.status === 'failed' || data.status === 'cancelled') {
                        window.location.href = "{{ route('admin.billing') }}";
                    }
                })
                .catch(error => console.error('Error checking status:', error));
        };

        // Poll every 4 seconds
        setInterval(checkStatus, 4000);
    });
</script>
@endpush
@endsection
