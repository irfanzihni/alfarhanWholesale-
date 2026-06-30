@extends('layouts.app')

@section('content')

{{-- Hero Banner --}}
<section class="bg-gradient-to-br from-emerald-800 to-emerald-950 text-white py-20 md:py-28 relative overflow-hidden">
    {{-- Decorative blobs --}}
    <div class="absolute top-0 left-0 w-72 h-72 rounded-full bg-emerald-700/30 blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full bg-emerald-900/40 blur-3xl translate-x-1/3 translate-y-1/3"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <span class="inline-block bg-emerald-700/60 border border-emerald-500/30 text-emerald-300 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
            Siapa Kami
        </span>
        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight leading-tight serif-font mb-6">
            Tentang <span class="text-emerald-400 font-serif italic">AlfarhanWholesale</span>
        </h1>
        <p class="text-emerald-100/80 text-lg leading-relaxed max-w-2xl mx-auto">
            Pemborong produk sunnah pilihan — kurma, madu, wangian, dan bakhoor — terus dari sumber terpercaya ke tangan anda dengan harga borong yang berpatutan.
        </p>
    </div>
</section>

{{-- Who We Are --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-center">
        <div class="space-y-6">
            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider">
                <span>🌿</span> Kisah Kami
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-emerald-950 serif-font leading-snug">
                Dipercayai oleh ribuan pembeli sejak bertahun-tahun
            </h2>
            <p class="text-slate-600 leading-relaxed text-sm md:text-base">
                AlfarhanWholesale bermula dengan satu matlamat mudah: menyediakan produk sunnah berkualiti tinggi kepada komuniti Muslim Malaysia dengan harga borong yang berpatutan. Kami percaya bahawa setiap Muslim berhak mendapat akses kepada produk yang baik, halal, dan berkat.
            </p>
            <p class="text-slate-600 leading-relaxed text-sm md:text-base">
                Dari kurma pilihan Al-Madinah hinggalah wangian oud eksklusif dan bakhoor terbaik, setiap produk kami dipilih dengan teliti untuk memastikan kualiti dan kesahihannya. Kami menjadi jambatan antara pembekal terpercaya dan pelanggan setia kami.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <a href="{{ route('shop.index') }}"
                   class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-6 py-3 rounded-full shadow-md hover:shadow-lg transition-all text-sm text-center">
                    Lihat Produk Kami
                </a>
                <a href="mailto:alfarhanwholesale@gmail.com"
                   class="border border-emerald-600 text-emerald-700 hover:bg-emerald-50 font-semibold px-6 py-3 rounded-full transition-colors text-sm text-center">
                    Hubungi Kami
                </a>
            </div>
        </div>

        {{-- Logo + Stats --}}
        <div class="space-y-6">
            {{-- Logo Display --}}
            <div class="flex justify-center">
                <div class="bg-white border-2 border-emerald-100 rounded-3xl p-8 shadow-lg flex flex-col items-center gap-4 w-full max-w-sm mx-auto">
                    <img src="/images/logo.png" alt="AlfarhanWholesale Logo" class="w-48 h-48 md:w-56 md:h-56 object-contain">
                    <div class="text-center">
                        <p class="text-xl font-extrabold text-emerald-900">Alfarhan<span class="text-emerald-500 font-serif">Wholesale</span></p>
                        <p class="text-xs text-slate-400 tracking-widest uppercase mt-1">Al-Farhan Trade & Wholesale</p>
                    </div>
                </div>
            </div>
            {{-- Stats Grid --}}
            <!--<div class="grid grid-cols-2 gap-4">
                <div class="bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm text-center space-y-1 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="text-2xl font-extrabold text-emerald-800">5+</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori Produk</div>
                </div>
                <div class="bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm text-center space-y-1 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="text-2xl font-extrabold text-emerald-800">500+</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggan Borong</div>
                </div>
                <div class="bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm text-center space-y-1 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="text-2xl font-extrabold text-emerald-800">1000+</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pesanan Diproses</div>
                </div>
                <div class="bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm text-center space-y-1 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="text-2xl font-extrabold text-emerald-800">4.9 ⭐</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Penilaian Pelanggan</div>
                </div>
            </div>
        </div>-->
    </div>
</section>

{{-- Our Products Section --}}
<section class="bg-emerald-50/50 border-t border-b border-emerald-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-3xl font-extrabold text-emerald-950 serif-font">Apa Yang Kami Tawarkan</h2>
            <p class="text-slate-500 mt-3 text-sm leading-relaxed">
                Semua produk kami adalah halal, dipilih dengan teliti, dan disediakan dalam pakej borong yang kompetitif.
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white rounded-2xl border border-emerald-100 p-6 text-center shadow-xs hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl group-hover:bg-emerald-700 group-hover:text-white transition-all">🌴</div>
                <h3 class="font-bold text-slate-800 text-sm mb-2">Dates</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Kurma premium dari Al-Madinah, Madinah Munawwarah</p>
            </div>
            <div class="bg-white rounded-2xl border border-emerald-100 p-6 text-center shadow-xs hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl group-hover:bg-emerald-700 group-hover:text-white transition-all">🍯</div>
                <h3 class="font-bold text-slate-800 text-sm mb-2">Honey</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Madu asli terpilih dari pelbagai jenis bunga dan hutan</p>
            </div>
            <div class="bg-white rounded-2xl border border-emerald-100 p-6 text-center shadow-xs hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl group-hover:bg-emerald-700 group-hover:text-white transition-all">🌸</div>
                <h3 class="font-bold text-slate-800 text-sm mb-2">Perfume</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Wangian oud, attar, dan minyak wangi halal berkualiti</p>
            </div>
            <div class="bg-white rounded-2xl border border-emerald-100 p-6 text-center shadow-xs hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl group-hover:bg-emerald-700 group-hover:text-white transition-all">🪔</div>
                <h3 class="font-bold text-slate-800 text-sm mb-2">Bakhoor</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Bakhoor dan dhan al oud ekslusif untuk majlis dan harian</p>
            </div>
            <div class="bg-white rounded-2xl border border-emerald-100 p-6 text-center shadow-xs hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl group-hover:bg-emerald-700 group-hover:text-white transition-all">🛍️</div>
                <h3 class="font-bold text-slate-800 text-sm mb-2">Others</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Produk sunnah lain seperti habbatus sauda & talbinah</p>
            </div>
        </div>
    </div>
</section>

{{-- Values --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center max-w-xl mx-auto mb-14">
        <h2 class="text-3xl font-extrabold text-emerald-950 serif-font">Komitmen Kami</h2>
        <p class="text-slate-500 mt-3 text-sm">Kami berkomitmen untuk memberikan pengalaman terbaik kepada setiap pelanggan kami.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="text-center space-y-4 p-6 bg-white rounded-2xl border border-emerald-50 shadow-xs hover:shadow-md transition-all">
            <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-2xl">✅</div>
            <h4 class="font-bold text-slate-800">Produk Halal & Sahih</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Setiap produk kami dipastikan halal dan diperolehi dari sumber yang dipercayai.</p>
        </div>
        <div class="text-center space-y-4 p-6 bg-white rounded-2xl border border-emerald-50 shadow-xs hover:shadow-md transition-all">
            <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-2xl">💰</div>
            <h4 class="font-bold text-slate-800">Harga Borong Terbaik</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Kami menawarkan harga borong yang kompetitif untuk memastikan nilai terbaik buat anda.</p>
        </div>
        <div class="text-center space-y-4 p-6 bg-white rounded-2xl border border-emerald-50 shadow-xs hover:shadow-md transition-all">
            <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-2xl">🚚</div>
            <h4 class="font-bold text-slate-800">Penghantaran Pantas</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Pesanan diproses dan dihantar dalam masa 24 jam bekerja ke seluruh Malaysia.</p>
        </div>
        <div class="text-center space-y-4 p-6 bg-white rounded-2xl border border-emerald-50 shadow-xs hover:shadow-md transition-all">
            <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-2xl">📞</div>
            <h4 class="font-bold text-slate-800">Khidmat Pelanggan</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Tim kami sedia membantu anda dengan sebarang pertanyaan dan keperluan.</p>
        </div>
    </div>
</section>

{{-- Contact Info --}}
<section class="bg-gradient-to-br from-emerald-900 to-emerald-950 text-white py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-extrabold serif-font mb-3">Hubungi Kami</h2>
            <p class="text-emerald-300/80 text-sm">Kami sedia membantu anda. Jangan ragu untuk menghubungi kami.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Address --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-8 text-center space-y-4 hover:bg-white/10 transition-all">
                <div class="w-14 h-14 bg-emerald-700/60 rounded-full flex items-center justify-center mx-auto text-2xl">📍</div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Alamat</h3>
                <p class="text-emerald-200/80 text-sm leading-relaxed">
                    48, Jalan Permai 2,<br>
                    Taman Puchong Permai,<br>
                    47150 Puchong,<br>
                    Selangor, Malaysia
                </p>
            </div>
            {{-- Email --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-8 text-center space-y-4 hover:bg-white/10 transition-all">
                <div class="w-14 h-14 bg-emerald-700/60 rounded-full flex items-center justify-center mx-auto text-2xl">✉️</div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">E-mel</h3>
                <a href="mailto:alfarhanwholesale@gmail.com"
                   class="text-emerald-300 hover:text-white text-sm transition-colors break-all">
                    alfarhanwholesale@gmail.com
                </a>
            </div>
            {{-- Phone --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-8 text-center space-y-4 hover:bg-white/10 transition-all">
                <div class="w-14 h-14 bg-emerald-700/60 rounded-full flex items-center justify-center mx-auto text-2xl">📱</div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Telefon</h3>
                <a href="tel:+60129632548"
                   class="text-emerald-300 hover:text-white text-sm transition-colors">
                    +012-963 2548
                </a>
            </div>
        </div>

        {{-- CTA --}}
        <div class="mt-14 text-center">
            <a href="{{ route('shop.index') }}"
               class="inline-flex items-center gap-2 bg-white text-emerald-900 font-bold px-8 py-3.5 rounded-full shadow-lg hover:bg-emerald-50 transition-all hover:scale-105 text-sm">
                🛍️ Mulakan Pembelian Anda
            </a>
        </div>
    </div>
</section>

@endsection
