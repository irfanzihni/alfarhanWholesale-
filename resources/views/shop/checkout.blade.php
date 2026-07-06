@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-extrabold text-slate-900 mb-8 serif-font">Checkout</h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        @csrf

        <!-- Shipping & Payment Form (Left Column) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Shipping Info -->
            <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs p-6 md:p-8 space-y-6">
                <h3 class="text-lg font-bold text-emerald-950 border-b border-emerald-50 pb-2">Delivery Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="customer_name" class="block text-sm font-semibold text-slate-700">Full Name</label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required
                               class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-sm font-semibold text-slate-700">Phone Number</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                               class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                               placeholder="e.g. +1 234 567 890">
                    </div>
                </div>

                <div>
                    <label for="delivery_address" class="block text-sm font-semibold text-slate-700">Delivery Address</label>
                    <textarea name="delivery_address" id="delivery_address" rows="4" required
                              class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                              placeholder="Enter your complete shipping address..."></textarea>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs p-6 md:p-8 space-y-6">
                <h3 class="text-lg font-bold text-emerald-950 border-b border-emerald-50 pb-2">Payment Method</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- COD -->
                    <label id="label-cod" class="border-2 border-emerald-200 bg-emerald-50/10 rounded-xl p-4 flex items-center gap-3 cursor-pointer hover:bg-emerald-50/30 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <input type="radio" name="payment_method" value="cod" checked 
                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="block font-bold text-sm text-slate-800">Cash on Delivery (COD)</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Bayar tunai semasa terima barang</span>
                        </div>
                    </label>

                    <!-- Online Banking / eWallet via ToyyibPay -->
                    <label id="label-online" class="border-2 border-slate-200 rounded-xl p-4 flex items-center gap-3 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <input type="radio" name="payment_method" value="online"
                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="block font-bold text-sm text-slate-800">Online Banking / eWallet</span>
                            <span class="block text-xs text-slate-500 mt-0.5">FPX, MAE, TNG, Boost &amp; kad kredit/debit</span>
                            <span class="inline-block mt-1.5 bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Dikuasakan ToyyibPay</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Order Items & Price Summary (Right Column) -->
        <div class="space-y-6">
            <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs p-6 space-y-6">
                <h3 class="text-lg font-bold text-emerald-950 border-b border-emerald-50 pb-2">Your Order</h3>

                <!-- Cart Items List Summary -->
                <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto pr-2">
                    @foreach($cartItems as $item)
                        <div class="py-3 flex justify-between items-center gap-4 text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="bg-slate-100 text-slate-700 font-bold px-1.5 py-0.5 rounded-sm">
                                    {{ $item->quantity }}x
                                </span>
                                <div>
                                    <span class="font-bold text-slate-800 block">{{ $item->product->name }}</span>
                                    @if($item->product_variation_id && $item->variation)
                                        <span class="text-[10px] text-slate-500 font-medium">Option: {{ $item->variation->value }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="font-bold text-slate-800">RM{{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Price breakdown -->
                <div class="border-t border-slate-100 pt-4 space-y-3 text-sm font-medium">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span class="text-slate-800 font-bold">RM{{ number_format($subtotal, 2) }}</span>
                    </div>

                    @if($couponCode)
                        <div class="flex justify-between text-emerald-800 bg-emerald-50 px-2.5 py-1.5 rounded-lg text-xs">
                            <span class="font-semibold">Applied Coupon ({{ $couponCode }})</span>
                            <span class="font-bold">-RM{{ number_format($discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="border-t border-slate-100 pt-4 flex justify-between text-base font-extrabold text-slate-900">
                        <span>Grand Total</span>
                        <span class="text-emerald-800 text-lg">RM{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-6 rounded-lg shadow-md hover:shadow-lg transition-all text-center">
                        Teruskan Pembayaran (RM{{ number_format($total, 2) }})
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
