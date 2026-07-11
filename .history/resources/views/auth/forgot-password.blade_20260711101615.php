@extends('layouts.guest')

@section('title', 'Forgot Password - ' . config('app.name', 'AlfarhanWholesale'))

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 bg-slate-50/50">
    <!-- This container matches the width/shadow of your login card -->
    <div class="w-full max-w-[440px] bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Forgot Password</h1>
            <p class="text-slate-500 mt-2 text-sm">Enter your email and we'll send a reset link.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl text-sm font-medium">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="space-y-2">
                <label for="email" class="text-sm font-semibold text-slate-700 ml-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                       placeholder="you@example.com"
                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#049372]/20 focus:border-[#049372] transition-all">
            </div>

            <button type="submit" 
                    class="w-full bg-[#049372] text-white font-semibold py-3 px-4 rounded-xl hover:bg-[#037a5e] active:scale-[0.98] transition-all shadow-[0_4px_14px_0_rgba(4,147,114,0.39)]">
                Send Reset Link
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-slate-500">
            Remember your password? 
            <a href="{{ route('login') }}" class="text-[#049372] font-bold hover:underline ml-1">Sign in</a>
        </div>
    </div>
</div>
@endsection