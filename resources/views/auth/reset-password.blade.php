@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-16 bg-white border border-emerald-100 rounded-2xl shadow-xl overflow-hidden">
    <div class="p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-emerald-900 tracking-tight">Reset Password</h2>
            <p class="text-sm text-slate-500 mt-2">Enter your email and choose a new password.</p>
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

        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $request->email ?? '') }}" required autofocus
                       class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700">New Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
            </div>

            <button type="submit" 
                    class="w-full bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg">
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection
