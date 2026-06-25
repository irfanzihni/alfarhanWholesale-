<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Control Panel - {{ config('app.name', 'Alfarhan Trading') }}</title>
    
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
<body class="bg-slate-100 text-slate-800 min-h-screen flex">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-emerald-950 text-emerald-100 flex flex-col shrink-0">
        <!-- Sidebar Branding -->
        <div class="h-20 flex items-center justify-center border-b border-emerald-900 px-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold text-white tracking-tight">
                    Sunnah<span class="text-emerald-400 font-serif">Staff</span>
                </span>
            </a>
        </div>

        <!-- User Role Header -->
        <div class="p-4 border-b border-emerald-900/60 bg-emerald-900/30">
            <p class="text-xs text-emerald-400 font-medium uppercase tracking-wider">Authenticated as</p>
            <h4 class="font-semibold text-white truncate text-sm mt-0.5">{{ auth()->user()->name }}</h4>
            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-800 text-emerald-200 mt-1 uppercase">
                @switch(auth()->user()->role)
                    @case('admin') Administrator @break
                    @case('outdoor_sales') Outdoor Sales @break
                    @case('purchaser') Purchaser @break
                    @case('storekeeper') Storekeeper @break
                    @default Staff
                @endswitch
            </span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-900 hover:text-white transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-900 text-white' : 'text-emerald-200/80' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                Dashboard Home
            </a>

            <!-- ADMIN ONLY Menu -->
            @if(auth()->user()->role === 'admin')
                <div class="pt-4 pb-1">
                    <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-wider px-4">Inventory CRUD</p>
                </div>
                <a href="{{ route('admin.products') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-900 hover:text-white transition-colors {{ request()->routeIs('admin.products*') ? 'bg-emerald-900 text-white' : 'text-emerald-200/80' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    Manage Products
                </a>
            @endif

            <!-- PURCHASER / STOREKEEPER / ADMIN Menu -->
            @if(in_array(auth()->user()->role, ['admin', 'purchaser', 'storekeeper']))
                <div class="pt-4 pb-1">
                    <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-wider px-4">Stock Management</p>
                </div>
                <a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-900 hover:text-white transition-colors {{ request()->routeIs('admin.inventory*') ? 'bg-emerald-900 text-white' : 'text-emerald-200/80' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    Stock Levels
                </a>
            @endif

            <!-- STOREKEEPER / ADMIN Menu -->
            @if(in_array(auth()->user()->role, ['admin', 'storekeeper']))
                <div class="pt-4 pb-1">
                    <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-wider px-4">Fulfillment</p>
                </div>
                <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-900 hover:text-white transition-colors {{ request()->routeIs('admin.orders*') ? 'bg-emerald-900 text-white' : 'text-emerald-200/80' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    Customer Orders
                </a>
            @endif

            <!-- OUTDOOR SALES / ADMIN Menu -->
            @if(in_array(auth()->user()->role, ['admin', 'outdoor_sales']))
                <div class="pt-4 pb-1">
                    <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-wider px-4">Reports & Performance</p>
                </div>
                <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-900 hover:text-white transition-colors {{ request()->routeIs('admin.reports*') ? 'bg-emerald-900 text-white' : 'text-emerald-200/80' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2" /></svg>
                    Analytical Reports
                </a>
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-emerald-900">
            <a href="{{ route('shop.home') }}" class="block text-center text-xs text-emerald-400 hover:text-emerald-200 font-medium py-1 mb-2">
                ← Go to Main Website
            </a>
            <a href="{{ route('logout') }}" class="block text-center bg-red-800 text-white rounded-lg text-xs font-semibold py-2 hover:bg-red-700 transition-colors shadow-xs">
                Log Out
            </a>
        </div>
    </aside>

    <!-- Main Workspace Container -->
    <div class="flex-grow flex flex-col min-w-0">
        <!-- Top Navigation Bar -->
        <header class="bg-white h-20 border-b border-slate-200 flex items-center justify-between px-8 shadow-xs">
            <h2 class="text-xl font-bold text-slate-800">
                @yield('header_title', 'Staff Control Panel')
            </h2>

            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-500 font-medium hidden sm:inline">{{ date('l, M d Y') }}</span>
                <div class="h-6 w-px bg-slate-200"></div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs text-slate-600 font-semibold uppercase">System Online</span>
                </div>
            </div>
        </header>

        <!-- Page Notifications -->
        @if(session('success'))
            <div class="px-8 mt-6">
                <div class="bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 p-4 rounded-r-lg shadow-xs flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="px-8 mt-6">
                <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-800 p-4 rounded-r-lg shadow-xs flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="px-8 mt-6">
                <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-800 p-4 rounded-r-lg shadow-xs">
                    <ul class="list-disc pl-5 space-y-1 text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Main Content View -->
        <main class="flex-grow p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>
