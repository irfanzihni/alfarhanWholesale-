@extends('layouts.app')

@section('title', 'Forgot Password - ' . config('app.name', 'AlfarhanWholesale'))

@section('content')
<div class="min-h-screen flex flex-col justify-between bg-[#f5fffa]">
    
    <!-- Top Header: Logo and Back Link -->
    <header class="w-full p-6 flex justify-between items-center max-w-7xl mx-auto">
        <div class="flex items-center">
            <!-- Replace with your actual logo path -->
            <img src="/images/logo.png" alt="AlfarhanWholesale" class="h-8">
        </div>
        <a href="/" class="text-[#049372] font-bold text-sm flex items-center hover:underline">
            &larr; Back to store
        </a>
    </header>

    <!-- Main Centered Form -->
    <main class="flex-grow flex items-center justify-center w-full px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                
                <!-- Green Header Section -->
                <div class="bg-[#049372] px-8 pt-10 pb-8 text-center text-white">
                    <div class="inline-flex items-center justify-center p-4 bg-white/10 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold">Forgot Password</h2>
                    <p class="text-emerald-50 text-sm mt-2 opacity-90">Enter your email and we'll send a reset link.</p>
                </div>

                <!-- Form Body -->
                <div class="px-8 py-8">
                    @if (session('status'))
                        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                   placeholder="you@example.com"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#049372] focus:border-transparent transition-all">
                        </div>

                        <button type="submit" 
                                class="w-full bg-[#049372] text-white font-bold py-3 px-4 rounded-lg hover:bg-[#037a5e] transition-colors shadow-md">
                            Send Reset Link
                        </button>
                    </form>

                    <div class="mt-8 text-center text-sm">
                        <p class="text-slate-600">
                            Remember your password? 
                            <a href="{{ route('login') }}" class="text-[#049372] font-bold hover:underline">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="p-6 text-slate-500 text-sm text-center">
        &copy; 2026 AlfarhanWholesale. All rights reserved.
    </footer>

</div>
@endsection