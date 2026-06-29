<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - {{ config('app.name', 'AlfarhanWholesale') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-emerald-950 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-emerald-900/40 overflow-hidden">
        <!-- Logo Header -->
        <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 p-8 text-center text-white">
            <span class="text-3xl font-extrabold tracking-tight">
                Sunnah<span class="text-emerald-400 font-serif">Staff</span>
            </span>
            <p class="text-xs text-emerald-200/70 mt-2 uppercase tracking-widest font-semibold">Management Portal</p>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-lg mb-6 text-xs font-semibold">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Staff Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                           placeholder="staff@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                           placeholder="••••••••">
                </div>

                <button type="submit" 
                        class="w-full bg-emerald-800 text-white font-bold py-3 px-4 rounded-lg hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 transition-all shadow-md">
                    Access Console
                </button>
            </form>

            <div class="mt-8 pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('shop.home') }}" class="text-xs text-emerald-700 hover:text-emerald-900 font-bold transition-colors">
                    ← Return to Customer Storefront
                </a>
            </div>
        </div>
    </div>

</body>
</html>
