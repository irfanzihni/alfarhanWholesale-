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
                               placeholder="e.g. 01X XXXXXXXX">
                    </div>
                </div>

                <div>
                    <label for="street_address" class="block text-sm font-semibold text-slate-700">Street Address</label>
                    <input type="text" name="street_address" id="street_address" value="{{ old('street_address') }}" required
                           class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                           placeholder="No, Jalan, Taman/Seksyen">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="postcode" class="block text-sm font-semibold text-slate-700">Postcode</label>
                        <input type="text" name="postcode" id="postcode" value="{{ old('postcode') }}" required
                               class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                               placeholder="e.g. 47100" maxlength="5">
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-semibold text-slate-700">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}" required
                               class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                               placeholder="e.g. Puchong">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-semibold text-slate-700">State</label>
                        <select name="state" id="state" required
                                class="w-full mt-1.5 px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
                            <option value="">Pilih Negeri</option>
                            <option value="Johor">Johor</option>
                            <option value="Kedah">Kedah</option>
                            <option value="Kelantan">Kelantan</option>
                            <option value="Melaka">Melaka</option>
                            <option value="Negeri Sembilan">Negeri Sembilan</option>
                            <option value="Pahang">Pahang</option>
                            <option value="Perak">Perak</option>
                            <option value="Perlis">Perlis</option>
                            <option value="Pulau Pinang">Pulau Pinang</option>
                            <option value="Selangor">Selangor</option>
                            <option value="Terengganu">Terengganu</option>
                            <option value="Kuala Lumpur">Kuala Lumpur</option>
                            <option value="Putra Jaya">Putra Jaya</option>
                            <option value="Sarawak">Sarawak</option>
                            <option value="Sabah">Sabah</option>
                            <option value="Labuan">Labuan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Courier Service Selection -->
            <div id="courier-card" class="bg-white border border-emerald-100 rounded-2xl shadow-xs p-6 md:p-8 space-y-4 hidden">
                <h3 class="text-lg font-bold text-emerald-950 border-b border-emerald-50 pb-2">Courier Option</h3>
                <p class="text-xs text-slate-500">Sila masukkan poskod dan negeri yang betul untuk mengira kadar kurier.</p>
                
                <input type="hidden" name="shipping_courier" id="shipping_courier">
                <input type="hidden" name="shipping_service" id="shipping_service">
                <input type="hidden" name="shipping_cost" id="shipping_cost" value="0.00">

                <div id="courier-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Dynamic couriers loaded via JS -->
                </div>
                
                <div id="courier-loading" class="hidden text-center py-6">
                    <svg class="animate-spin h-8 w-8 text-emerald-700 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs text-slate-500 mt-2 font-medium">Mencari kadar courier terbaik...</p>
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

                    <!-- Shipping Row -->
                    <div id="shipping-fee-row" class="flex justify-between text-slate-600 hidden">
                        <span>Penghantaran (<span id="shipping-courier-name">-</span>)</span>
                        <span id="shipping-fee-amount" class="text-slate-800 font-bold">RM0.00</span>
                    </div>

                    <div class="border-t border-slate-100 pt-4 flex justify-between text-base font-extrabold text-slate-900">
                        <span>Grand Total</span>
                        <span class="text-emerald-800 text-lg">RM{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" id="checkout-submit-btn"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 px-6 rounded-lg shadow-md hover:shadow-lg transition-all text-center">
                        Teruskan Pembayaran (RM{{ number_format($total, 2) }})
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const postcodeField = document.getElementById('postcode');
        const stateField = document.getElementById('state');
        const courierCard = document.getElementById('courier-card');
        const courierList = document.getElementById('courier-list');
        const courierLoading = document.getElementById('courier-loading');
        
        const shippingCourierInput = document.getElementById('shipping_courier');
        const shippingServiceInput = document.getElementById('shipping_service');
        const shippingCostInput = document.getElementById('shipping_cost');
        
        const shippingFeeRow = document.getElementById('shipping-fee-row');
        const shippingCourierNameSpan = document.getElementById('shipping-courier-name');
        const shippingFeeAmountSpan = document.getElementById('shipping-fee-amount');
        const grandTotalTextSpan = document.querySelector('.text-emerald-800.text-lg');
        const checkoutSubmitBtn = document.getElementById('checkout-submit-btn');

        const subtotal = {{ $subtotal }};
        const discount = {{ $discount }};
        const totalWeight = {{ $totalWeight }};

        function fetchRates() {
            const postcode = postcodeField.value.trim();
            const state = stateField.value;

            if (postcode.length === 5 && state) {
                // Show loading
                courierCard.classList.remove('hidden');
                courierLoading.classList.remove('hidden');
                courierList.classList.add('hidden');
                courierList.innerHTML = '';
                
                // Clear selection
                shippingCourierInput.value = '';
                shippingServiceInput.value = '';
                shippingCostInput.value = '0.00';
                shippingFeeRow.classList.add('hidden');
                updateTotals(0);

                fetch(`/checkout/shipping-rates?postcode=${postcode}&state=${state}&weight=${totalWeight}`)
                    .then(response => response.json())
                    .then(data => {
                        courierLoading.classList.add('hidden');
                        courierList.classList.remove('hidden');

                        if (data.success && data.rates.length > 0) {
                            data.rates.forEach((rate, i) => {
                                const card = document.createElement('label');
                                card.className = 'border-2 border-slate-200 rounded-xl p-4 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-all bg-white';
                                card.innerHTML = `
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="selected_courier" id="courier_option_${i}" value="${rate.service_id}" data-courier="${rate.courier_name}" data-service="${rate.service_name}" data-price="${rate.price}" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="block font-bold text-sm text-slate-800">${rate.courier_name}</span>
                                            <span class="block text-[10px] text-slate-500 mt-0.5">${rate.service_name} (${rate.delivery})</span>
                                        </div>
                                    </div>
                                    <span class="font-extrabold text-sm text-emerald-800">RM${rate.price.toFixed(2)}</span>
                                `;

                                // Listen for click/change
                                card.querySelector('input').addEventListener('change', function () {
                                    // Highlight selected
                                    document.querySelectorAll('#courier-list label').forEach(l => {
                                        l.classList.remove('border-emerald-500', 'bg-emerald-50');
                                        l.classList.add('border-slate-200', 'bg-white');
                                    });
                                    card.classList.remove('border-slate-200', 'bg-white');
                                    card.classList.add('border-emerald-500', 'bg-emerald-50');

                                    // Set hidden inputs
                                    shippingCourierInput.value = this.dataset.courier;
                                    shippingServiceInput.value = this.dataset.service;
                                    shippingCostInput.value = this.dataset.price;

                                    // Update summary UI
                                    shippingCourierNameSpan.textContent = this.dataset.courier;
                                    shippingFeeAmountSpan.textContent = 'RM' + parseFloat(this.dataset.price).toFixed(2);
                                    shippingFeeRow.classList.remove('hidden');

                                    updateTotals(parseFloat(this.dataset.price));
                                });

                                courierList.appendChild(card);
                            });
                        } else {
                            courierList.innerHTML = '<p class="text-xs text-red-600 col-span-2 text-center font-medium">Tiada kurier tersedia untuk poskod/negeri ini.</p>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        courierLoading.classList.add('hidden');
                        courierList.classList.remove('hidden');
                        courierList.innerHTML = '<p class="text-xs text-red-600 col-span-2 text-center font-medium">Gagal memuatkan kadar kurier. Sila cuba lagi.</p>';
                    });
            }
        }

        function updateTotals(shippingFee) {
            const grandTotal = Math.max(0, subtotal - discount + shippingFee);
            
            // Grand Total text
            if (grandTotalTextSpan) {
                grandTotalTextSpan.textContent = 'RM' + grandTotal.toFixed(2);
            }
            
            // Payment button text
            if (checkoutSubmitBtn) {
                checkoutSubmitBtn.textContent = `Teruskan Pembayaran (RM${grandTotal.toFixed(2)})`;
            }
        }

        postcodeField.addEventListener('input', function() {
            if (this.value.length === 5) {
                fetchRates();
            }
        });
        stateField.addEventListener('change', fetchRates);
    });
</script>
@endpush
@endsection
