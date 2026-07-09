@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-16 bg-white border border-emerald-100 rounded-2xl shadow-xl overflow-hidden">
    <div class="p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-emerald-900 tracking-tight">Verify Your Email</h2>
            <p class="text-sm text-slate-500 mt-2">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-lg mb-6 text-xs font-semibold text-center">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="space-y-4">
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="w-full bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg">
                    Resend Verification Email
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST" class="text-center">
                @csrf
                <button type="submit" class="text-xs text-slate-500 hover:text-slate-700 underline font-medium">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
