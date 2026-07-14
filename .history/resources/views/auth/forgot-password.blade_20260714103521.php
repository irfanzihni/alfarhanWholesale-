@extends('layouts.guest')

@section('title', 'Forgot Password - ' . config('app.name', 'AlfarhanWholesale'))

@section('content')
<div class="auth-card-wrap">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="auth-card-title">Forgot Password</h1>
            <p class="auth-card-subtitle">Enter your email and we'll send you a reset link</p>
        </div>

        <div class="auth-card-body">

            @if (session('status'))
                <div class="auth-success-alert">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="auth-form">
                @csrf

                <div class="auth-field">
                    <label for="email">Email Address</label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        <input type="email" name="email" id="email"
                               value="{{ old('email') }}"
                               required autofocus
                               class="auth-input"
                               placeholder="you@example.com">
                    </div>
                </div>

                <button type="submit" class="auth-btn-primary">Send Password Reset Link</button>
            </form>

            <p class="auth-footer-text">
                Remember your password?
                <a href="{{ route('login') }}">Sign in here</a>
            </p>
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
</style>
@endpush