@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-teal-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-100">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6">
                <div class="flex items-center justify-center">
                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="mt-4 text-center text-3xl font-extrabold text-white tracking-tight">Welcome Back</h2>
                <p class="mt-2 text-center text-sm text-emerald-100">Sign in to your account and claim your coupons</p>
            </div>

            <div class="px-8 py-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white">
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                   class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-slate-600">Remember me</label>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-3 px-4 rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Sign In
                    </button>
                </form>

                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-slate-500">Or continue with</span>
                    </div>
                </div>

                <button type="button" id="google-signin-btn"
                        class="w-full flex items-center justify-center gap-3 border border-slate-200 text-slate-700 font-semibold py-3 px-4 rounded-xl hover:bg-slate-50 transition-all shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
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

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-bold transition-colors">Sign up today</a>
                    </p>
                </div>
            </div>
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
