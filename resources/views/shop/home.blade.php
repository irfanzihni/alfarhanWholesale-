@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-emerald-800 to-emerald-950 text-white overflow-hidden py-12 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
        <div class="space-y-5 text-center md:text-left">
            <span class="inline-block bg-emerald-700/60 border border-emerald-500/30 text-emerald-300 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                100% Organic & Blessed Nutrition
            </span>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight leading-tight serif-font">
                Dapatkan makanan sunnah dan wangian <br class="hidden md:block">dengan <span class="text-emerald-400 font-serif italic">harga borong</span>
            </h1>
            <p class="text-base md:text-lg text-emerald-100/80 leading-relaxed max-w-lg mx-auto md:mx-0">
                Terokai pilihan produk sunnah kami — kurma premium, madu asli, wangian oud, bakhoor eksklusif, dan banyak lagi dengan harga borong.
            </p>
            <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-3 pt-2">
                <a href="{{ route('shop.index') }}" 
                   class="bg-white text-emerald-900 font-bold px-8 py-3.5 rounded-full shadow-lg hover:bg-emerald-50 transition-all hover:scale-105 text-center">
                    Browse Shop
                </a>
                <a href="#featured-products" 
                   class="border border-emerald-400/40 text-white font-semibold px-8 py-3.5 rounded-full hover:bg-white/10 transition-colors text-center">
                    Best Sellers
                </a>
            </div>
        </div>
        <div class="relative flex justify-center">
            <!-- Decorative circle -->
            <div class="absolute w-64 h-64 md:w-96 md:h-96 rounded-full bg-emerald-800/40 blur-3xl -z-10"></div>
            <!-- Elegant Sunnah Food Collage -->
            <div class="bg-white/5 border border-white/10 p-3 md:p-4 rounded-3xl backdrop-blur-md shadow-2xl w-full max-w-sm">
                <img src="/images/products/ajwa_dates.jpg" alt="Premium Ajwa Dates" class="rounded-2xl object-cover w-full h-56 md:h-80 shadow-md">
                <div class="mt-3 md:mt-4 flex justify-between items-center text-emerald-100 text-sm font-medium px-2">
                    <span>✨ Blessed Al-Madinah Ajwa Dates</span>
                    <span class="text-emerald-300 font-bold">RM25.00 / 500g</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Claim Coupon Section -->
@auth
    @php
        $welcomeCoupon = \App\Models\Coupon::where('code', 'WELCOME20')->first();
        if (!$welcomeCoupon) {
            $welcomeCoupon = \App\Models\Coupon::where('discount_type', 'percent')->first();
        }
        $isClaimed = $welcomeCoupon ? \App\Models\UserCoupon::where('user_id', auth()->id())->where('coupon_id', $welcomeCoupon->id)->exists() : false;
    @endphp
    @if($welcomeCoupon && !$isClaimed)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 md:mt-12">
            <div class="bg-emerald-50 border border-emerald-200 p-5 md:p-8 rounded-3xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-xl font-bold text-emerald-950">Tuntut Hadiah Selamat Datang! 🎁</h3>
                    <p class="text-slate-600 text-sm mt-1">Dapatkan <strong>diskaun 20%</strong> untuk pembelian pertama anda dengan minimum belanja RM30.00.</p>
                </div>
                <form action="{{ route('coupon.claim') }}" method="POST" class="w-full md:w-auto">
                    @csrf
                    <input type="hidden" name="coupon_id" value="{{ $welcomeCoupon->id }}">
                    <button type="submit" 
                            class="w-full md:w-auto bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-6 py-3 rounded-full shadow-md hover:shadow-lg transition-all text-sm">
                        Tuntut Kupon Selamat Datang
                    </button>
                </form>
            </div>
        </section>
    @endif
@endauth

<!-- Categories Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center max-w-xl mx-auto mb-12">
        <h2 class="text-3xl font-extrabold text-emerald-950 serif-font">Browse Blessed Categories</h2>
        <p class="text-slate-500 mt-2 text-sm">Nourishing selections recommended by prophetic tradition for physical and structural wellness.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
        <!-- Dates Category -->
        <a href="{{ route('shop.index', ['category' => 'dates']) }}" class="group bg-white border border-emerald-100 hover:border-emerald-500 rounded-2xl p-6 text-center shadow-xs transition-all hover:shadow-md hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 font-bold mb-4 group-hover:bg-emerald-700 group-hover:text-white transition-all">🌴</div>
            <h4 class="font-bold text-slate-800 text-sm">Dates</h4>
        </a>

        <!-- Honey Category -->
        <a href="{{ route('shop.index', ['category' => 'honey']) }}" class="group bg-white border border-emerald-100 hover:border-emerald-500 rounded-2xl p-6 text-center shadow-xs transition-all hover:shadow-md hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 font-bold mb-4 group-hover:bg-emerald-700 group-hover:text-white transition-all">🍯</div>
            <h4 class="font-bold text-slate-800 text-sm">Honey</h4>
        </a>

        <!-- Perfume Category -->
        <a href="{{ route('shop.index', ['category' => 'perfume']) }}" class="group bg-white border border-emerald-100 hover:border-emerald-500 rounded-2xl p-6 text-center shadow-xs transition-all hover:shadow-md hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 font-bold mb-4 group-hover:bg-emerald-700 group-hover:text-white transition-all">🌸</div>
            <h4 class="font-bold text-slate-800 text-sm">Perfume</h4>
        </a>

        <!-- Bakhoor Category -->
        <a href="{{ route('shop.index', ['category' => 'bakhoor']) }}" class="group bg-white border border-emerald-100 hover:border-emerald-500 rounded-2xl p-6 text-center shadow-xs transition-all hover:shadow-md hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 font-bold mb-4 group-hover:bg-emerald-700 group-hover:text-white transition-all">🪔</div>
            <h4 class="font-bold text-slate-800 text-sm">Bakhoor</h4>
        </a>

        <!-- Others Category -->
        <a href="{{ route('shop.index', ['category' => 'others']) }}" class="group bg-white border border-emerald-100 hover:border-emerald-500 rounded-2xl p-6 text-center shadow-xs transition-all hover:shadow-md hover:-translate-y-1 col-span-2 md:col-span-1">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 font-bold mb-4 group-hover:bg-emerald-700 group-hover:text-white transition-all">🛍️</div>
            <h4 class="font-bold text-slate-800 text-sm">Others</h4>
        </a>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured-products" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-emerald-100/60 bg-emerald-50/20">
    <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-emerald-950 serif-font">Our Featured Selections</h2>
            <p class="text-slate-500 mt-2 text-sm">Premium quality products sourced directly from trusted local growers.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm flex items-center gap-1 group">
            View All Catalog
            <span class="group-hover:translate-x-1 transition-transform">→</span>
        </a>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
        @foreach($featuredProducts as $product)
            <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs overflow-hidden hover:shadow-lg transition-all flex flex-col group">
                <div class="relative overflow-hidden bg-slate-100 aspect-square">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    
                    @if($product->discount_price)
                        <span class="absolute top-4 left-4 bg-red-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider">
                            Special Offer
                        </span>
                    @endif
                </div>
                
                <div class="p-4 md:p-6 flex-grow flex flex-col justify-between">
                    <div class="space-y-1.5">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">{{ ucfirst($product->category) }}</span>
                        <h3 class="font-bold text-slate-800 text-base line-clamp-1 group-hover:text-emerald-800 transition-colors">
                            <a href="{{ route('shop.show', $product->id) }}">{{ $product->name }}</a>
                        </h3>
                        <p class="text-slate-500 text-xs line-clamp-2 leading-relaxed">
                            {{ $product->description }}
                        </p>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <div>
                            @if($product->discount_price)
                                <div class="flex items-center gap-1.5">
                                    <span class="text-emerald-800 font-extrabold text-lg">RM{{ number_format($product->discount_price, 2) }}</span>
                                    <span class="text-slate-400 line-through text-xs">RM{{ number_format($product->base_price, 2) }}</span>
                                </div>
                            @else
                                <span class="text-slate-800 font-extrabold text-lg">RM{{ number_format($product->base_price, 2) }}</span>
                            @endif
                        </div>
                        <a href="{{ route('shop.show', $product->id) }}" 
                           class="bg-emerald-50 text-emerald-800 hover:bg-emerald-700 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-xs">
                            Select Options
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Values / Trust Blocks -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-emerald-100/60">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
        <div class="text-center space-y-3 p-6 rounded-2xl bg-white border border-emerald-50 shadow-xs">
            <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-emerald-700 text-xl font-bold">🌿</div>
            <h4 class="font-bold text-slate-800 text-sm">100% Organic</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Pure, raw crops harvested without synthetic chemicals or pesticides.</p>
        </div>
        <div class="text-center space-y-3 p-6 rounded-2xl bg-white border border-emerald-50 shadow-xs">
            <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-emerald-700 text-xl font-bold">📦</div>
            <h4 class="font-bold text-slate-800 text-sm">Secure Packaging</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Eco-friendly moisture-proof seals to preserve freshness and nutrition.</p>
        </div>
        <div class="text-center space-y-3 p-6 rounded-2xl bg-white border border-emerald-50 shadow-xs">
            <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-emerald-700 text-xl font-bold">🚚</div>
            <h4 class="font-bold text-slate-800 text-sm">Fast Shipping</h4>
            <p class="text-xs text-slate-500 leading-relaxed">We ship within 24 hours of ordering to maintain fruit crispness.</p>
        </div>
        <div class="text-center space-y-3 p-6 rounded-2xl bg-white border border-emerald-50 shadow-xs">
            <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-emerald-700 text-xl font-bold">📞</div>
            <h4 class="font-bold text-slate-800 text-sm">24/7 Care Support</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Dedicated wellness support team standing by to assist your queries.</p>
        </div>
    </div>
</section>
@endsection
