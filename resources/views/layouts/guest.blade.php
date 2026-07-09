<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'AlfarhanWholesale'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body.auth-page {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 50%, #f0fdfa 100%);
            color: #334155;
        }

        .auth-header {
            padding: 1rem 1.5rem;
        }

        .auth-header-inner {
            max-width: 28rem;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            text-decoration: none;
            color: inherit;
        }

        .auth-brand img {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            object-fit: contain;
        }

        .auth-brand-name {
            font-size: 1.125rem;
            font-weight: 800;
            color: #065f46;
            letter-spacing: -0.025em;
        }

        .auth-brand-name em {
            font-style: normal;
            color: #10b981;
            font-weight: 300;
            font-family: 'Playfair Display', serif;
        }

        .auth-back-link {
            font-size: 0.875rem;
            font-weight: 600;
            color: #047857;
            text-decoration: none;
            white-space: nowrap;
        }

        .auth-back-link:hover { color: #064e3b; }

        .auth-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .auth-card-wrap {
            width: 100%;
            max-width: 28rem;
        }

        .auth-card {
            background: #fff;
            border-radius: 1.5rem;
            border: 1px solid #d1fae5;
            box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.15);
            overflow: hidden;
        }

        .auth-card-header {
            background: linear-gradient(90deg, #059669 0%, #0d9488 100%);
            padding: 1.75rem 2rem;
            text-align: center;
            color: #fff;
        }

        .auth-card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.2);
            margin-bottom: 0.75rem;
        }

        .auth-card-icon svg {
            width: 2rem;
            height: 2rem;
        }

        .auth-card-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            margin-bottom: 0.375rem;
        }

        .auth-card-subtitle {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.4;
        }

        .auth-card-body {
            padding: 2rem;
        }

        .auth-alert {
            margin-bottom: 1.25rem;
            padding: 0.875rem 1rem;
            border-radius: 0.5rem;
            border-left: 4px solid #ef4444;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 0.875rem;
        }

        .auth-alert ul {
            margin-left: 1.125rem;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .auth-field label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .auth-field-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .auth-field-row label { margin-bottom: 0; }

        .auth-input-wrap {
            position: relative;
        }

        .auth-input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: #94a3b8;
            pointer-events: none;
        }

        .auth-input {
            display: block;
            width: 100%;
            height: 2.75rem;
            padding: 0 1rem 0 2.75rem;
            font-size: 0.9375rem;
            font-family: inherit;
            color: #1e293b;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }

        .auth-input:focus {
            background: #fff;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .auth-link {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #059669;
            text-decoration: none;
        }

        .auth-link:hover { color: #047857; }

        .auth-checkbox-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .auth-checkbox-row input {
            width: 1rem;
            height: 1rem;
            accent-color: #059669;
        }

        .auth-checkbox-row label {
            font-size: 0.875rem;
            color: #475569;
            margin: 0;
        }

        .auth-btn-primary {
            display: block;
            width: 100%;
            height: 2.75rem;
            margin-top: 0.25rem;
            border: none;
            border-radius: 0.75rem;
            background: linear-gradient(90deg, #059669 0%, #0d9488 100%);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35);
            transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
        }

        .auth-btn-primary:hover {
            filter: brightness(1.05);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
        }

        .auth-btn-primary:active { transform: translateY(1px); }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.75rem 0;
            color: #64748b;
            font-size: 0.875rem;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .auth-btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            height: 2.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            background: #fff;
            color: #334155;
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s, box-shadow 0.15s;
        }

        .auth-btn-google:hover {
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        }

        .auth-btn-google svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }

        .auth-footer-text {
            margin-top: 1.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
        }

        .auth-footer-text a {
            color: #059669;
            font-weight: 700;
            text-decoration: none;
        }

        .auth-footer-text a:hover { color: #047857; }

        .auth-page-footer {
            padding: 1.25rem;
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
        }
    </style>
</head>
<body class="auth-page">

    <header class="auth-header">
        <div class="auth-header-inner">
            <a href="{{ route('shop.home') }}" class="auth-brand">
                <img src="{{ asset('images/logo.png') }}" alt="AlfarhanWholesale Logo">
                <span class="auth-brand-name">Alfarhan<em>Wholesale</em></span>
            </a>
            <a href="{{ route('shop.home') }}" class="auth-back-link">&larr; Back to store</a>
        </div>
    </header>

    <main class="auth-main">
        @yield('content')
    </main>

    <footer class="auth-page-footer">
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
