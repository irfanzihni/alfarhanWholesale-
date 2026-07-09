<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'AlfarhanWholesale'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 via-white to-teal-50 min-h-screen flex flex-col">

    <header class="px-4 sm:px-6 lg:px-8 py-4">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <a href="{{ route('shop.home') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="AlfarhanWholesale Logo" class="h-10 w-10 object-contain rounded-full">
                <span class="text-lg font-extrabold text-emerald-800 tracking-tight">
                    Alfarhan<span class="text-emerald-500 font-light font-serif">Wholesale</span>
                </span>
            </a>
            <a href="{{ route('shop.home') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 transition-colors">
                &larr; Back to store
            </a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} AlfarhanWholesale. All rights reserved.
    </footer>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.15.0/firebase-app.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.15.0/firebase-analytics.js";
        import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/12.15.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyA-EDxariyRsE0ErsdVWlv3N2RJ5G28l00",
            authDomain: "alfarhanwholesale-31ed4.firebaseapp.com",
            projectId: "alfarhanwholesale-31ed4",
            storageBucket: "alfarhanwholesale-31ed4.firebasestorage.app",
            messagingSenderId: "550883612445",
            appId: "1:550883612445:web:07fe27e7bf53365361f3f4",
            measurementId: "G-XG3HPDKS79"
        };

        const app = initializeApp(firebaseConfig);
        getAnalytics(app);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        window.firebaseAuth = auth;
        window.googleAuthProvider = provider;
        window.firebaseSignInWithPopup = signInWithPopup;
    </script>

    @stack('scripts')
</body>
</html>
