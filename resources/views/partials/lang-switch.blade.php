@php $curLocale = app()->getLocale(); @endphp
<span class="lang-switch" style="display:inline-flex; align-items:center; gap:0.35rem; font-size:0.82rem; font-weight:700; letter-spacing:0.3px;">
    <a href="{{ route('lang.switch', 'sw') }}" style="text-decoration:none; color: {{ $curLocale === 'sw' ? '#FFFFFF' : '#64748B' }};">SW</a>
    <span style="color:#334155;">/</span>
    <a href="{{ route('lang.switch', 'en') }}" style="text-decoration:none; color: {{ $curLocale === 'en' ? '#FFFFFF' : '#64748B' }};">EN</a>
</span>
