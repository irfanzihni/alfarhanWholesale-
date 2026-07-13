@extends('layouts.guest')

@section('title', 'Verify Email - ' . config('app.name', 'AlfarhanWholesale'))

@section('content')
<div class="auth-card-wrap">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="auth-card-title">Verify Your Email</h1>
            <p class="auth-card-subtitle">
                Thanks for signing up! Please verify your email address by clicking the link we sent you.
            </p>
        </div>

        <div class="auth-card-body">

            @if (session('status') == 'verification-link-sent')
                <div class="auth-success-alert">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>A new verification link has been sent to your email address.</span>
                </div>
            @endif

            {{-- Info box --}}
            <div class="verify-info-box">
                <div class="verify-info-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="verify-info-text">
                    <p class="verify-info-title">Check your inbox</p>
                    <p class="verify-info-desc">
                        We've sent a verification link to your email. Click the link in the email to activate your account.
                        If you don't see it, check your spam or junk folder.
                    </p>
                </div>
            </div>

            <div class="verify-actions">
                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button type="submit" class="auth-btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem;height:1.1rem;display:inline;vertical-align:middle;margin-right:0.35rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Resend Verification Email
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST" class="verify-logout-form">
                    @csrf
                    <button type="submit" class="verify-logout-btn">
                        Not your account? Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .auth-success-alert {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        margin-bottom: 1.25rem;
        padding: 0.875rem 1rem;
        border-radius: 0.5rem;
        border-left: 4px solid #10b981;
        background: #ecfdf5;
        color: #065f46;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .auth-success-alert svg {
        width: 1.25rem;
        height: 1.25rem;
        flex-shrink: 0;
        color: #059669;
    }
    .verify-info-box {
        display: flex;
        gap: 0.875rem;
        background: #f0fdfa;
        border: 1px solid #a7f3d0;
        border-radius: 0.75rem;
        padding: 1rem 1.125rem;
        margin-bottom: 1.5rem;
    }
    .verify-info-icon {
        flex-shrink: 0;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 0.125rem;
        color: #059669;
    }
    .verify-info-icon svg { width: 1.25rem; height: 1.25rem; }
    .verify-info-text { flex: 1; }
    .verify-info-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #065f46;
        margin-bottom: 0.25rem;
    }
    .verify-info-desc {
        font-size: 0.8125rem;
        color: #047857;
        line-height: 1.5;
    }
    .verify-actions {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
    }
    .verify-logout-form {
        text-align: center;
    }
    .verify-logout-btn {
        background: none;
        border: none;
        font-family: inherit;
        font-size: 0.875rem;
        color: #64748b;
        cursor: pointer;
        font-weight: 500;
        transition: color 0.15s;
        text-decoration: underline;
        text-underline-offset: 2px;
    }
    .verify-logout-btn:hover { color: #334155; }
</style>
@endpush