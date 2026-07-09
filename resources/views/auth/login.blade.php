@extends('layouts.guest')

@section('title', 'Sign In - ' . config('app.name', 'AlfarhanWholesale'))

@section('content')
<div class="auth-card-wrap">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h1 class="auth-card-title">Welcome Back</h1>
            <p class="auth-card-subtitle">Sign in to your account and claim your coupons</p>
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

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf

                <div class="auth-field">
                    <label for="email">Email Address</label>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="auth-input" placeholder="you@example.com">
                    </div>
                </div>

                <div class="auth-field">
                    <div class="auth-field-row">
                        <label for="password">Password</label>
                        <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
                    </div>
                    <div class="auth-input-wrap">
                        <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input type="password" name="password" id="password" required class="auth-input" placeholder="Enter your password">
                    </div>
                </div>

                <div class="auth-checkbox-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="auth-btn-primary">Sign In</button>
            </form>

            <div class="auth-divider">Or continue with</div>

            <button type="button" id="google-signin-btn" class="auth-btn-google">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.35,11.1H12v2.7h5.38c-0.24,1.28 -0.96,2.37 -2.04,3.1v2.57h3.3c1.93,-1.78 3.04,-4.4 3.04,-7.4C21.68,11.77 21.56,11.41 21.35,11.1z" fill="#4285F4" />
                    <path d="M12,20.6c2.59,0 4.77,-0.86 6.36,-2.33l-3.3,-2.57c-0.91,0.61 -2.08,0.97 -3.06,0.97 -2.35,0 -4.35,-1.59 -5.06,-3.72H3.5v2.66c1.57,3.12 4.8,5.04 8.5,5.04z" fill="#34A853" />
                    <path d="M6.94,12.96c-0.18,-0.54 -0.28,-1.12 -0.28,-1.71s0.1,-1.17 0.28,-1.71V6.88H3.5C2.89,8.1 2.54,9.47 2.54,10.92s0.35,2.82 0.96,4.04l2.94,-2.48 -0.5,-0.52z" fill="#FBBC05" />
                    <path d="M12,5.28c1.41,0 2.68,0.48 3.68,1.44l2.76,-2.76C16.77,2.44 14.59,1.56 12,1.56c-3.7,0 -6.93,1.92 -8.5,5.04l2.94,2.48c0.71,-2.13 2.71,-3.72 5.06,-3.72z" fill="#EA4335" />
                </svg>
                Google
            </button>

            <form id="google-login-form" action="{{ route('login.google') }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="id_token" id="google-id-token">
            </form>

            <p class="auth-footer-text">
                Don't have an account?
                <a href="{{ route('register') }}">Sign up today</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('google-signin-btn')?.addEventListener('click', function() {
        if (!window.firebaseAuth || !window.googleAuthProvider || !window.firebaseSignInWithPopup) {
            alert('Firebase is still loading. Please try again in a moment.');
            return;
        }
        window.firebaseSignInWithPopup(window.firebaseAuth, window.googleAuthProvider)
            .then((result) => result.user.getIdToken())
            .then((idToken) => {
                document.getElementById('google-id-token').value = idToken;
                document.getElementById('google-login-form').submit();
            })
            .catch((error) => {
                console.error('Google Sign-In Error:', error);
                alert('Sign-In failed: ' + error.message);
            });
    });
</script>
@endpush
