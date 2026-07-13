@extends('layouts.guest')

@section('title', 'Reset Password - ' . config('app.name', 'AlfarhanWholesale'))

@section('content')
<div class="auth-card-wrap">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="auth-card-title">Reset Password</h1>
            <p class="auth-card-subtitle">Enter your email and choose a new password</p>
        </div>

        <div class="auth-card-body">

            @if ($errors->any())
                <div class="auth-alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="auth-form">
                @csrf

                {{-- Password Reset Token --}}
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="auth-field">
                    <label for="email">Email Address</label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $request->email ?? '') }}"
                               required autofocus
                               class="auth-input"
                               placeholder="you@example.com">
                    </div>
                </div>

                <div class="auth-field">
                    <label for="password">New Password</label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input type="password" name="password" id="password"
                               required
                               class="auth-input"
                               placeholder="Enter new password">
                    </div>
                </div>

                <div class="auth-field">
                    <label for="password_confirmation">Confirm New Password</label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               required
                               class="auth-input"
                               placeholder="Repeat new password">
                    </div>
                </div>

                <button type="submit" class="auth-btn-primary">Reset Password</button>
            </form>

            <p class="auth-footer-text">
                Remembered it?
                <a href="{{ route('login') }}">Back to sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
