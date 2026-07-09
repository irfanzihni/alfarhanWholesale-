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

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase font-medium">
                    <span class="bg-white px-3 text-slate-500">Or access with</span>
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

            <div class="mt-8 pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('shop.home') }}" class="text-xs text-emerald-700 hover:text-emerald-900 font-bold transition-colors">
                    ← Return to Customer Storefront
                </a>
            </div>
        </div>
    </div>

    <!-- Firebase App and Auth SDK -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.15.0/firebase-app.js";
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
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        document.getElementById('google-signin-btn')?.addEventListener('click', function() {
            signInWithPopup(auth, provider)
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

</body>
</html>
