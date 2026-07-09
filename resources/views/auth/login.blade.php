@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-16 bg-white border border-emerald-100 rounded-2xl shadow-xl overflow-hidden">
    <div class="p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-emerald-900 tracking-tight">Welcome Back</h2>
            <p class="text-sm text-slate-500 mt-2">Sign in to your account and claim your coupons</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-lg mb-6">
                <ul class="list-disc pl-5 space-y-1 text-xs font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                       class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
            </div>

            <div>
                <div class="flex justify-between items-center">
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition-colors">Forgot password?</a>
                </div>
                <input type="password" name="password" id="password" required
                       class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                    <label for="remember" class="ml-2 block text-xs font-medium text-slate-600">Remember me</label>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg">
                Log In
            </button>
        </form>

        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase font-medium">
                <span class="bg-white px-3 text-slate-500">Or continue with</span>
            </div>
        </div>

        <button type="button" id="google-signin-btn"
                class="w-full flex items-center justify-center gap-2 border border-slate-200 text-slate-700 font-bold py-3 px-4 rounded-lg hover:bg-slate-50 transition-all shadow-xs hover:shadow-md">
            <svg class="w-5 h-5" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.35,11.1H12v2.7h5.38c-0.24,1.28 -0.96,2.37 -2.04,3.1v2.57h3.3c1.93,-1.78 3.04,-4.4 3.04,-7.4C21.68,11.77 21.56,11.41 21.35,11.1z" fill="#4285F4" />
                <path d="M12,20.6c2.59,0 4.77,-0.86 6.36,-2.33l-3.3,-2.57c-0.91,0.61 -2.08,0.97 -3.06,0.97 -2.35,0 -4.35,-1.59 -5.06,-3.72H3.5v2.66c1.57,3.12 4.8,5.04 8.5,5.04z" fill="#34A853" />
                <path d="M6.94,12.96c-0.18,-0.54 -0.28,-1.12 -0.28,-1.71s0.1,-1.17 0.28,-1.71V6.88H3.5C2.89,8.1 2.54,9.47 2.54,10.92s0.35,2.82 0.96,4.04l2.94,-2.48 -0.5,-0.52z" fill="#FBBC05" />
                <path d="M12,5.28c1.41,0 2.68,0.48 3.68,1.44l2.76,-2.76C16.77,2.44 14.59,1.56 12,1.56c-3.7,0 -6.93,1.92 -8.5,5.04l2.94,2.48c0.71,-2.13 2.71,-3.72 5.06,-3.72z" fill="#EA4335" />
            </svg>
            Google Account
        </button>

        <form id="google-login-form" action="{{ route('login.google') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="id_token" id="google-id-token">
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-emerald-700 hover:text-emerald-900 font-bold transition-colors">Sign up today</a>
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
            .then((result) => {
                return result.user.getIdToken();
            })
            .then((idToken) => {
                document.getElementById('google-id-token').value = idToken;
                document.getElementById('google-login-form').submit();
            })
            .catch((error) => {
                console.error("Google Sign-In Error:", error);
                alert("Sign-In failed: " + error.message);
            });
    });
</script>
@endpush
