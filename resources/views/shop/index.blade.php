@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="bg-emerald-950 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-2">
        <h1 class="text-3xl md:text-4xl font-extrabold serif-font">Shop Our Catalog</h1>
        <p class="text-emerald-200/70 text-sm max-w-md mx-auto">Terokai produk sunnah pilihan kami — kurma, madu, wangian, bakhoor, dan banyak lagi.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Filters Sidebar -->
        <aside class="w-full lg:w-64 shrink-0 space-y-6">
            <!-- Search Widget (Mobile/Tablet visible) -->
            <div class="bg-white p-6 rounded-2xl border border-emerald-100 shadow-xs lg:hidden">
                <h4 class="font-bold text-slate-800 text-sm mb-3">Search Products</h4>
                <form action="{{ route('shop.index') }}" method="GET" class="site-search">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search here..." 
                           class="site-search-input py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
                    <button type="submit" class="site-search-button">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </button>
                </form>
            </div>

            <!-- Categories Widget -->
            <div class="bg-white p-6 rounded-2xl border border-emerald-100 shadow-xs space-y-4">
                <h3 class="font-bold text-emerald-950 text-base border-b border-emerald-50 pb-2">Filter Categories</h3>
                <div class="space-y-2">
                    <a href="{{ route('shop.index', ['category' => 'all', 'search' => request('search'), 'sort' => request('sort')]) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all {{ !request('category') || request('category') == 'all' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' }}">
                        <span>All Categories</span>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full {{ !request('category') || request('category') == 'all' ? 'bg-emerald-800 text-emerald-200' : '' }}">
                            {{ \App\Models\Product::count() }}
                        </span>
                    </a>

                    @foreach($categories as $key => $label)
                        <a href="{{ route('shop.index', ['category' => $key, 'search' => request('search'), 'sort' => request('sort')]) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request('category') == $key ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' }}">
                            <span>{{ $label }}</span>
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full {{ request('category') == $key ? 'bg-emerald-800 text-emerald-200' : '' }}">
                                {{ \App\Models\Product::where('category', $key)->count() }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Sort Widget -->
            <div class="bg-white p-6 rounded-2xl border border-emerald-100 shadow-xs space-y-4">
                <h3 class="font-bold text-emerald-950 text-base border-b border-emerald-50 pb-2">Sort Results</h3>
                <div class="space-y-2">
                    <a href="{{ route('shop.index', ['sort' => 'newest', 'category' => request('category'), 'search' => request('search')]) }}" 
                       class="block px-3 py-2 rounded-lg text-sm font-medium {{ request('sort') == 'newest' || !request('sort') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-emerald-50/50' }}">
                        Newest First
                    </a>
                    <a href="{{ route('shop.index', ['sort' => 'price_asc', 'category' => request('category'), 'search' => request('search')]) }}" 
                       class="block px-3 py-2 rounded-lg text-sm font-medium {{ request('sort') == 'price_asc' ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-emerald-50/50' }}">
                        Price: Low to High
                    </a>
                    <a href="{{ route('shop.index', ['sort' => 'price_desc', 'category' => request('category'), 'search' => request('search')]) }}" 
                       class="block px-3 py-2 rounded-lg text-sm font-medium {{ request('sort') == 'price_desc' ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:bg-emerald-50/50' }}">
                        Price: High to Low
                    </a>
                </div>
            </div>
        </aside>

        <!-- Product Grid Area -->
        <div class="flex-grow">
            <!-- Search status & mobile filters bar -->
            <div class="bg-white border border-slate-100 rounded-2xl p-4 mb-8 flex justify-between items-center shadow-xs">
                <p class="text-sm text-slate-500 font-medium">
                    Showing <span class="text-slate-800 font-semibold">{{ $products->firstItem() ?: 0 }}–{{ $products->lastItem() ?: 0 }}</span> of <span class="text-slate-800 font-semibold">{{ $products->total() }}</span> results
                    @if(request('search'))
                        for "<span class="text-emerald-700 font-semibold">{{ request('search') }}</span>"
                    @endif
                </p>
            </div>

            <!-- Products Grid -->
            @if($products->isEmpty())
                <div class="bg-white rounded-3xl p-16 text-center border border-emerald-100 shadow-xs space-y-4">
                    <span class="text-5xl">🌾</span>
                    <h3 class="text-xl font-bold text-slate-800">No products found</h3>
                    <p class="text-slate-500 text-sm max-w-sm mx-auto">We couldn't find any products matching your selection. Try adjusting your search query or filter settings.</p>
                    <a href="{{ route('shop.index') }}" class="inline-block bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-full text-sm">
                        Clear All Filters
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5 lg:gap-6">
                    @foreach($products as $product)
                        <div class="bg-white border border-emerald-100 rounded-xl sm:rounded-2xl shadow-xs overflow-hidden hover:shadow-lg transition-all flex flex-col group min-w-0">
                            <div class="relative overflow-hidden bg-slate-100 aspect-square">
                                <img src="{{ asset($product->image_url) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     onerror="this.src='{{ asset('images/products/placeholder.jpg') }}'">
                                
                                @if($product->discount_price)
                                    <span class="absolute top-4 left-4 bg-red-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                        Special Offer
                                    </span>
                                @endif

                                @if($product->stock <= 0 && $product->variations->isEmpty())
                                    <span class="absolute inset-0 bg-slate-900/60 flex items-center justify-center text-white font-bold text-sm tracking-wide">
                                        Out of Stock
                                    </span>
                                @endif
                            </div>
                            
                            <div class="p-3 sm:p-5 lg:p-6 flex-grow flex flex-col justify-between min-w-0">
                                <div class="space-y-1.5">
                                    <span class="text-[10px] sm:text-xs font-bold text-emerald-700 uppercase tracking-wider">{{ ucfirst($product->category) }}</span>
                                    <h3 class="font-bold text-slate-800 text-sm sm:text-base line-clamp-2 group-hover:text-emerald-800 transition-colors">
                                        <a href="{{ route('shop.show', $product->id) }}">{{ $product->name }}</a>
                                    </h3>
                                    <p class="text-slate-500 text-xs line-clamp-2 leading-relaxed hidden sm:block">
                                        {{ $product->description }}
                                    </p>
                                </div>

                                <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="min-w-0">
                                        @if($product->discount_price)
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="text-emerald-800 font-extrabold text-base sm:text-lg">RM{{ number_format($product->discount_price, 2) }}</span>
                                                <span class="text-slate-400 line-through text-xs">RM{{ number_format($product->base_price, 2) }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-800 font-extrabold text-base sm:text-lg">
                                                @if($product->variations->isNotEmpty())
                                                    From RM{{ number_format($product->variations->min('price'), 2) }}
                                                @else
                                                    RM{{ number_format($product->base_price, 2) }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('shop.show', $product->id) }}" 
                                       class="bg-emerald-50 text-emerald-800 hover:bg-emerald-700 hover:text-white px-3 sm:px-4 py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-bold transition-all shadow-xs text-center leading-tight">
                                        {{ $product->variations->isNotEmpty() ? 'Select Options' : 'View Details' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
