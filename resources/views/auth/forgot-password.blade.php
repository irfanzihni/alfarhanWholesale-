@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-16 bg-white border border-emerald-100 rounded-2xl shadow-xl overflow-hidden">
    <div class="p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-emerald-900 tracking-tight">Forgot Password</h2>
            <p class="text-sm text-slate-500 mt-2">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        @if (session('status'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-lg mb-6 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-lg mb-6">
                <ul class="list-disc pl-5 space-y-1 text-xs font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                       class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
            </div>

            <button type="submit" 
                    class="w-full bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg">
                Send Password Reset Link
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Remember your password? 
                <a href="{{ route('login') }}" class="text-emerald-700 hover:text-emerald-900 font-bold transition-colors">Sign in here</a>
            </p>
        </div>
    </div>
</div>
@endsection
