@extends('layouts.app')

@section('title', 'Profaili')

@section('content')

<style>
    .app-profile-container {
        max-width: 540px;
        margin: 0 auto;
        padding-bottom: 2rem;
    }

    /* Hero Profile Card */
    .app-profile-hero {
        background: linear-gradient(145deg, #181820 0%, #111116 100%);
        border-radius: 26px;
        padding: 1.75rem 1.25rem 1.5rem 1.25rem;
        color: #ffffff;
        text-align: center;
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .app-profile-hero::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(255, 51, 0, 0.18) 0%, rgba(0,0,0,0) 70%);
        pointer-events: none;
    }

    /* Avatar styling */
    .app-avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 0.75rem;
    }
    .app-avatar-circle {
        width: 74px;
        height: 74px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FF3300, #ff6b3d);
        color: #ffffff;
        font-weight: 900;
        font-size: 1.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(255, 51, 0, 0.35);
        border: 3px solid rgba(255, 255, 255, 0.15);
        font-family: 'Outfit', sans-serif;
    }
    .app-avatar-badge {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #10B981;
        border: 2px solid #111116;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 10px;
    }

    .app-profile-name {
        font-size: 1.35rem;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 0.25rem;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }
    .app-profile-email {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-bottom: 0.65rem;
    }
    .app-meta-pills {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    .app-pill-badge {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #cbd5e1;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .app-pill-badge.primary {
        background: rgba(255, 51, 0, 0.12);
        border-color: rgba(255, 51, 0, 0.25);
        color: #ff6b3d;
    }

    /* Form Section Cards */
    .app-section-card {
        background: #FFFFFF;
        border-radius: 22px;
        padding: 1.25rem 1.15rem;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.06);
        margin-bottom: 1.15rem;
    }
    .app-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.88rem;
        font-weight: 800;
        color: var(--text-main);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .app-section-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        color: #ffffff;
    }
    .app-section-icon.blue { background: linear-gradient(135deg, #2563EB, #1D4ED8); }
    .app-section-icon.purple { background: linear-gradient(135deg, #7C3AED, #6D28D9); }

    /* Input with icons */
    .app-field-group {
        margin-bottom: 1rem;
    }
    .app-field-group:last-child {
        margin-bottom: 0;
    }
    .app-field-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.35rem;
    }
    .app-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .app-input-icon {
        position: absolute;
        left: 1rem;
        color: #94a3b8;
        font-size: 0.95rem;
        pointer-events: none;
    }
    .app-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.65rem;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-size: 0.92rem;
        font-weight: 600;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    .app-input:focus {
        background: #ffffff;
        border-color: #FF3300;
        box-shadow: 0 0 0 3px rgba(255, 51, 0, 0.12);
    }
    .app-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    /* Save Button */
    .app-save-btn {
        width: 100%;
        padding: 0.85rem 1.25rem;
        background: linear-gradient(135deg, #FF3300, #e62e00);
        color: #ffffff;
        border: none;
        border-radius: 14px;
        font-weight: 800;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(255, 51, 0, 0.35);
        transition: transform 0.15s ease, opacity 0.15s ease;
    }
    .app-save-btn:active {
        transform: scale(0.98);
        opacity: 0.9;
    }
</style>

<div class="app-profile-container">

    {{-- Feedback Alerts --}}
    @if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25); color: #065F46; padding: 0.75rem 1rem; border-radius: 14px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.6rem; font-size: 0.85rem; font-weight: 700;">
        <i class="fas fa-check-circle" style="color: #10B981; font-size: 1.05rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: #991B1B; padding: 0.85rem 1rem; border-radius: 14px; margin-bottom: 1rem; font-size: 0.85rem;">
        <div style="font-weight: 800; display: flex; align-items: center; gap: 0.45rem; margin-bottom: 0.35rem;">
            <i class="fas fa-circle-exclamation" style="color: #EF4444;"></i> Tafadhali rekebisha makosa yafuatayo:
        </div>
        <ul style="margin: 0; padding-left: 1.25rem;">
            @foreach($errors->all() as $error)
                <li style="font-weight: 600;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- 1. App Hero Card --}}
    <div class="app-profile-hero">
        <div class="app-avatar-wrapper">
            <div class="app-avatar-circle">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="app-avatar-badge" title="Akaunti Ipo Hewani">
                <i class="fas fa-check"></i>
            </div>
        </div>

        <h2 class="app-profile-name">{{ $user->name }}</h2>
        <div class="app-profile-email">{{ $user->email }}</div>

        <div class="app-meta-pills">
            <span class="app-pill-badge primary">
                <i class="fas fa-shield-alt"></i> Administrator
            </span>
            @if($user->tenant)
            <span class="app-pill-badge">
                <i class="fas fa-wifi"></i> {{ $user->tenant->business_name }}
            </span>
            @endif
        </div>
    </div>

    {{-- 2. Profile Form --}}
    <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf

        {{-- Card 1: Taarifa Binafsi --}}
        <div class="app-section-card">
            <div class="app-section-title">
                <div class="app-section-icon blue">
                    <i class="fas fa-user"></i>
                </div>
                <span>Taarifa za Mtumiaji</span>
            </div>

            <div class="app-field-group">
                <label class="app-field-label">Jina Kamili *</label>
                <div class="app-input-wrap">
                    <i class="fas fa-user-tag app-input-icon"></i>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="app-input" placeholder="Weka jina kamili">
                </div>
            </div>

            <div class="app-field-group">
                <label class="app-field-label">Barua Pepe (Email) *</label>
                <div class="app-input-wrap">
                    <i class="fas fa-envelope app-input-icon"></i>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="app-input" placeholder="Weka barua pepe">
                </div>
            </div>
        </div>

        {{-- Card 2: Usalama & Nenosiri --}}
        <div class="app-section-card">
            <div class="app-section-title">
                <div class="app-section-icon purple">
                    <i class="fas fa-lock"></i>
                </div>
                <span>Badili Nenosiri (Hiari)</span>
            </div>

            <div class="app-field-group">
                <label class="app-field-label">Nenosiri la Sasa</label>
                <div class="app-input-wrap">
                    <i class="fas fa-key app-input-icon"></i>
                    <input type="password" name="current_password" class="app-input" placeholder="Acha wazi kama hutaki kubadili">
                </div>
            </div>

            <div class="app-field-group">
                <label class="app-field-label">Nenosiri Jipya</label>
                <div class="app-input-wrap">
                    <i class="fas fa-lock app-input-icon"></i>
                    <input type="password" name="new_password" class="app-input" placeholder="Nenosiri lenye herufi 8+">
                </div>
            </div>

            <div class="app-field-group">
                <label class="app-field-label">Thibitisha Nenosiri Jipya</label>
                <div class="app-input-wrap">
                    <i class="fas fa-check-double app-input-icon"></i>
                    <input type="password" name="new_password_confirmation" class="app-input" placeholder="Rudia nenosiri jipya">
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="app-save-btn">
            <i class="fas fa-save"></i> Hifadhi Mabadiliko
        </button>
    </form>

</div>

@endsection

