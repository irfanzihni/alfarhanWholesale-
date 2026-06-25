@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-extrabold text-slate-900 mb-8 serif-font">Shopping Cart</h1>

    @if($cartItems->isEmpty())
        <div class="bg-white border border-emerald-100 rounded-3xl p-16 text-center shadow-xs space-y-4">
            <span class="text-5xl">🛒</span>
            <h3 class="text-xl font-bold text-slate-800">Your cart is empty</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto font-medium">Add some blessed Sunnah foods to get started on your journey towards natural nutrition.</p>
            <a href="{{ route('shop.index') }}" class="inline-block bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-8 py-3 rounded-full text-sm shadow-md transition-all">
                Browse Shop Catalog
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Cart Items List (Left Column) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-emerald-50/60 border-b border-emerald-100 text-slate-600 font-bold text-xs uppercase tracking-wider">
                                <th class="p-6">Product Details</th>
                                <th class="p-6 text-center">Quantity</th>
                                <th class="p-6 text-right">Price</th>
                                <th class="p-6 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($cartItems as $item)
                                <tr>
                                    <!-- Product & Variant info -->
                                    <td class="p-6 flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-lg bg-slate-50 border border-slate-100 overflow-hidden shrink-0">
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-sm hover:text-emerald-700">
                                                <a href="{{ route('shop.show', $item->product_id) }}">{{ $item->product->name }}</a>
                                            </h4>
                                            @if($item->product_variation_id && $item->variation)
                                                <span class="inline-block bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-md mt-1">
                                                    {{ $item->variation->name }}: {{ $item->variation->value }}
                                                </span>
                                            @endif
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold transition-colors">
                                                    Remove Item
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                    <!-- Quantity Adjustment Form -->
                                    <td class="p-6">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center justify-center gap-1.5 max-w-[120px] mx-auto">
                                            @csrf
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" required
                                                   class="w-16 px-2 py-1.5 border border-slate-200 rounded-lg text-center text-xs focus:outline-none focus:ring-2 focus:ring-emerald-600">
                                            <button type="submit" class="bg-emerald-50 text-emerald-800 hover:bg-emerald-700 hover:text-white p-1.5 rounded-lg transition-all" title="Update Quantity">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Unit Price -->
                                    <td class="p-6 text-right font-medium text-slate-600 text-sm">
                                        RM{{ number_format($item->unit_price, 2) }}
                                    </td>

                                    <!-- Line Subtotal -->
                                    <td class="p-6 text-right font-bold text-slate-800 text-sm">
                                        RM{{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- COUPON CLAIMING MODULE (Sunnah Bites Specific Promotion) -->
                <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs p-6 space-y-4">
                    <h3 class="text-lg font-bold text-emerald-950 border-b border-emerald-50 pb-2">Claim Sign-in Coupons 🎁</h3>
                    <p class="text-xs text-slate-500 font-medium">As a logged-in customer, you are eligible to claim these discount coupons. Claim them to activate them on your account.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        @foreach($allCoupons as $coupon)
                            @php
                                $hasClaimed = in_array($coupon->id, $claimedCouponIds);
                                $hasUsed = in_array($coupon->id, $usedCouponIds);
                            @endphp
                            <div class="border {{ $hasClaimed ? 'border-slate-200 bg-slate-50/50' : 'border-emerald-200 bg-emerald-50/20' }} rounded-xl p-4 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <span class="inline-block font-mono font-bold text-emerald-900 bg-emerald-100 px-2 py-0.5 rounded text-xs">
                                            {{ $coupon->code }}
                                        </span>
                                        <span class="text-xs font-bold {{ $hasUsed ? 'text-slate-400' : ($hasClaimed ? 'text-emerald-700' : 'text-amber-600') }}">
                                            @if($hasUsed) Used @elseif($hasClaimed) Claimed & Active @else Available @endif
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-sm mt-2">
                                        @if($coupon->discount_type == 'percent')
                                            {{ (int)$coupon->discount_amount }}% Discount
                                        @else
                                            RM{{ number_format($coupon->discount_amount, 2) }} Discount
                                        @endif
                                    </h4>
                                    <p class="text-slate-500 text-[10px] mt-1">Minimum Spend: RM{{ number_format($coupon->min_spend, 2) }}</p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    @if($hasUsed)
                                        <button disabled class="w-full bg-slate-200 text-slate-400 text-xs font-bold py-1.5 rounded-lg cursor-not-allowed">
                                            Coupon Used
                                        </button>
                                    @elseif($hasClaimed)
                                        <form action="{{ route('coupon.apply') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="coupon_code" value="{{ $coupon->code }}">
                                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-1.5 rounded-lg shadow-sm">
                                                Apply to Cart
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('coupon.claim') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="coupon_id" value="{{ $coupon->id }}">
                                            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold py-1.5 rounded-lg shadow-sm">
                                                Claim Coupon
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Cart Totals & Coupon Code Form (Right Column) -->
            <div class="space-y-6">
                <!-- Promo Code Apply Box -->
                <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs p-6 space-y-4">
                    <h3 class="text-base font-bold text-slate-800">Have a promo code?</h3>
                    <form action="{{ route('coupon.apply') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="coupon_code" placeholder="Enter coupon code" required
                               class="flex-grow px-3 py-2 border border-slate-200 rounded-lg text-sm uppercase focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-4 py-2 rounded-lg text-sm shadow-xs transition-colors">
                            Apply
                        </button>
                    </form>
                </div>

                <!-- Totals Box -->
                <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs p-6 space-y-6">
                    <h3 class="text-lg font-bold text-emerald-950 border-b border-emerald-50 pb-2">Order Summary</h3>
                    
                    <div class="space-y-3 text-sm font-medium">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span class="text-slate-800 font-bold">RM{{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if($appliedCoupon)
                            <div class="flex justify-between items-center bg-emerald-50 text-emerald-800 p-2.5 rounded-lg text-xs">
                                <div>
                                    <span class="font-bold">Coupon: {{ $appliedCoupon->code }}</span>
                                    <p class="text-[10px] text-emerald-600">Applied successfully</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold">-RM{{ number_format($discount, 2) }}</span>
                                    <a href="{{ route('coupon.remove') }}" class="text-red-500 hover:text-red-700 font-bold text-sm" title="Remove Coupon">&times;</a>
                                </div>
                            </div>
                        @endif

                        <div class="border-t border-slate-100 pt-4 flex justify-between text-base font-extrabold text-slate-900">
                            <span>Order Total</span>
                            <span class="text-emerald-800">RM{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('checkout.index') }}" 
                           class="block text-center w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-6 rounded-lg shadow-md hover:shadow-lg transition-all">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection
