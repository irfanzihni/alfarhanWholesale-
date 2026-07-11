@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 py-12 px-4">
    <div class="max-w-md w-full">
        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-slate-100">
            
            <!-- Header Section (Matching Login Page) -->
            <div class="bg-[#049372] px-8 pt-10 pb-8 text-center text-white">
                <div class="inline-flex items-center justify-center p-4 bg-white/10 rounded-full mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold">Verify Your Email</h2>
                <p class="text-emerald-50 text-sm mt-2 opacity-90">
                    Thanks for signing up! Please verify your email address by clicking the link we just sent you.
                </p>
            </div>

            <!-- Form Body -->
            <div class="px-8 py-8">
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200 text-sm">
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                <div class="space-y-4">
                    <form action="{{ route('verification.send') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-[#049372] text-white font-bold py-3 px-4 rounded-lg hover:bg-[#037a5e] transition-colors shadow-md">
                            Resend Verification Email
                        </button>
                    </form>

                    <form action="{{ route('logout') }}" method="POST" class="text-center pt-4">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 hover:text-slate-800 font-medium transition-colors">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection