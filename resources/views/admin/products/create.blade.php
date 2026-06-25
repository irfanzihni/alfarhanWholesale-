@extends('layouts.admin')

@section('header_title')
    Create New Product
@endsection

@section('content')
<div class="max-w-2xl bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-xs">
    <div class="border-b border-slate-100 pb-4 mb-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Product details</h3>
            <p class="text-xs text-slate-500 mt-1">Provide product name, prices, category, stock, and thumbnail image.</p>
        </div>
        <a href="{{ route('admin.products') }}" class="text-slate-500 hover:text-slate-700 font-bold text-xs transition-colors">
            ← Back to Products List
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Product Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Product Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                   placeholder="e.g. Premium Ajwa Dates (Al-Madinah)">
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                      placeholder="Write a detailed description of the product..."></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category -->
            <div>
                <label for="category" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Category</label>
                <select name="category" id="category" required
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
                    <option value="">Select Category...</option>
                    <option value="dates" {{ old('category') == 'dates' ? 'selected' : '' }}>Dates</option>
                    <option value="honey" {{ old('category') == 'honey' ? 'selected' : '' }}>Honey</option>
                    <option value="perfume" {{ old('category') == 'perfume' ? 'selected' : '' }}>Perfume</option>
                    <option value="bakhoor" {{ old('category') == 'bakhoor' ? 'selected' : '' }}>Bakhoor</option>
                    <option value="others" {{ old('category') == 'others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>

            <!-- Initial Stock -->
            <div>
                <label for="stock" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Initial Stock (Units)</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all">
                <span class="text-[10px] text-slate-400 font-semibold mt-1 block">Leave as 0 if you plan to manage stock via variations instead.</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Base Price -->
            <div>
                <label for="base_price" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Base Price (RM)</label>
                <input type="number" name="base_price" id="base_price" step="0.01" value="{{ old('base_price') }}" required min="0"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                       placeholder="0.00">
            </div>

            <!-- Discount Price -->
            <div>
                <label for="discount_price" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Discount Price (RM - Optional)</label>
                <input type="number" name="discount_price" id="discount_price" step="0.01" value="{{ old('discount_price') }}" min="0"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all"
                       placeholder="Must be lower than base price">
            </div>
        </div>

        <!-- Image Upload -->
        <div>
            <label for="image" class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Product Thumbnail Image</label>
            <input type="file" name="image" id="image" accept="image/*"
                   class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
        </div>

        <button type="submit" 
                class="w-full bg-emerald-800 hover:bg-emerald-950 text-white font-bold py-3 px-6 rounded-lg shadow-sm transition-all text-sm uppercase tracking-wide">
            Save Product to Catalog
        </button>
    </form>
</div>
@endsection
