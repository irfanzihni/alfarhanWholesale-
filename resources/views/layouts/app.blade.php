<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Alfarhan Trading') }} - Makanan Sunnah & Wangian</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Icons (Lucide or fontawesome via cdn if needed, we'll use inline SVGs for premium weightless loads) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .serif-font {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Top Promo bar -->
    <div class="bg-emerald-950 text-emerald-100 text-xs py-2 px-4 text-center font-medium tracking-wide">
        ✨ Registered customers get RM10.00 Welcome Coupon on sign-in! Claim yours now. ✨
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-white border-b border-emerald-100 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('shop.home') }}" class="flex items-center gap-2">
                        <span class="text-2xl font-extrabold text-emerald-800 tracking-tight flex items-center">
                            <svg class="w-8 h-8 text-emerald-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Alfarhan<span class="text-emerald-500 font-light font-serif">Trading</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('shop.home') }}" class="text-slate-600 hover:text-emerald-700 font-medium transition-colors">Home</a>
                    <a href="{{ route('shop.index') }}" class="text-slate-600 hover:text-emerald-700 font-medium transition-colors">Shop Catalog</a>
                    <a href="{{ route('shop.about') }}" class="text-slate-600 hover:text-emerald-700 font-medium transition-colors">About Us</a>
                </nav>

                <!-- Search, Cart & Profile actions -->
                <div class="flex items-center gap-4">
                    <!-- Search Bar (Simple input) -->
                    <form action="{{ route('shop.index') }}" method="GET" class="hidden lg:block relative">
                        <input type="text" name="search" placeholder="Search sunnah foods..." 
                               class="w-60 pl-10 pr-4 py-2 border border-emerald-100 rounded-full bg-emerald-50/50 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition-all">
                        <div class="absolute left-3 top-2.5 text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </form>

                    <!-- Cart -->
                    <a href="{{ route('shop.cart') }}" class="p-2 text-slate-600 hover:text-emerald-700 relative transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @auth
                            @php
                                $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute top-0 right-0 bg-emerald-600 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center border border-white">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        @endauth
                    </a>

                    <!-- User authentication -->
                    @auth
                        <div class="relative group">
                            <button class="flex items-center gap-1 text-slate-700 hover:text-emerald-700 font-medium transition-colors py-2 focus:outline-none">
                                <span class="bg-emerald-100 text-emerald-800 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </span>
                                <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <!-- Dropdown -->
                            <div class="absolute right-0 top-full mt-1 w-48 bg-white border border-slate-100 rounded-lg shadow-lg py-2 hidden group-hover:block hover:block">
                                @if(in_array(auth()->user()->role, ['admin', 'outdoor_sales', 'purchaser', 'storekeeper']))
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">Admin Dashboard</a>
                                @endif
                                <a href="{{ route('customer.orders') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">My Orders</a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Sign Out</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-sm transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="bg-emerald-700 text-white hover:bg-emerald-800 font-medium px-4 py-2 rounded-full text-sm transition-all shadow-xs hover:shadow-md">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Success & Error Toast Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 p-4 rounded-r-lg shadow-xs flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-800 p-4 rounded-r-lg shadow-xs flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-emerald-950 text-emerald-100/80 border-t border-emerald-900 mt-16 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-4">
                <span class="text-xl font-bold text-white tracking-tight">
                    Alfarhan<span class="text-emerald-400 font-serif">Trading</span>
                </span>
                <p class="text-sm text-emerald-200/60 leading-relaxed">
                    Pemborong produk sunnah pilihan — kurma, madu, wangian, bakhoor, dan lebih lagi — dengan harga borong yang berpatutan.
                </p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm tracking-wider uppercase">Shop Categories</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.index', ['category' => 'dates']) }}" class="hover:text-white transition-colors">Dates</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'honey']) }}" class="hover:text-white transition-colors">Honey</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'perfume']) }}" class="hover:text-white transition-colors">Perfume</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'bakhoor']) }}" class="hover:text-white transition-colors">Bakhoor</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'others']) }}" class="hover:text-white transition-colors">Others</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm tracking-wider uppercase">Useful Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.index') }}" class="hover:text-white transition-colors">All Products</a></li>
                    <li><a href="{{ route('shop.cart') }}" class="hover:text-white transition-colors">Shopping Cart</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">My Profile</a></li>
                    <li><a href="{{ route('admin.login') }}" class="hover:text-white transition-colors">Staff Login</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <h4 class="text-white font-semibold text-sm tracking-wider uppercase">Contact Us</h4>
                <p class="text-sm leading-relaxed">
                    48, Jalan Permai 2, Taman Puchong Permai,<br>
                    47150 Puchong, Selangor, Malaysia<br><br>
                    alfarhanwholesale@gmail.com<br>
                    +012-963 2548
                </p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-emerald-900/60 mt-8 pt-6 text-center text-xs text-emerald-200/40">
            &copy; {{ date('Y') }} Alfarhan Trading. All rights reserved. Made with love and devotion.
        </div>
    </footer>

</body>
</html>
