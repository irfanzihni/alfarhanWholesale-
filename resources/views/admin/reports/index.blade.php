@extends('layouts.admin')

@section('header_title')
    Analytical Reports
@endsection

@section('content')
<!-- Top Sales Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 text-white rounded-2xl p-6 shadow-xs">
        <span class="text-xs font-bold text-emerald-200 uppercase tracking-wider block">Cumulative Gross Sales</span>
        <h2 class="text-3xl font-extrabold mt-1">RM{{ number_format($totalRevenue, 2) }}</h2>
        <p class="text-[10px] text-emerald-300 mt-2 font-medium">Sum of all non-cancelled transactions</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-500 uppercase block">Online Portal Revenue</span>
            <h2 class="text-2xl font-extrabold text-slate-800 mt-1">RM{{ number_format($onlineRevenue, 2) }}</h2>
            <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded font-bold mt-2 inline-block">
                {{ $totalRevenue > 0 ? round(($onlineRevenue / $totalRevenue) * 100, 1) : 0 }}% of total
            </span>
        </div>
        <div class="text-2xl">🌐</div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-500 uppercase block">Outdoor Event Revenue</span>
            <h2 class="text-2xl font-extrabold text-slate-800 mt-1">RM{{ number_format($outdoorRevenue, 2) }}</h2>
            <span class="text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded font-bold mt-2 inline-block">
                {{ $totalRevenue > 0 ? round(($outdoorRevenue / $totalRevenue) * 100, 1) : 0 }}% of total
            </span>
        </div>
        <div class="text-2xl">🎪</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    
    <!-- 1. Daily Sales Aggregate -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="border-b border-slate-100 pb-2 flex justify-between items-center">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span>📈</span> Daily Sales Log (Last 15 days)
            </h3>
            <button onclick="window.print()" class="text-emerald-700 hover:text-emerald-950 text-xs font-bold uppercase tracking-wider">Print PDF</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs font-medium text-slate-600">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-3">Date</th>
                        <th class="p-3 text-center">Orders Count</th>
                        <th class="p-3 text-right">Daily Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($dailySales as $day)
                        <tr>
                            <td class="p-3 font-bold text-slate-900">{{ \Carbon\Carbon::parse($day->sales_date)->format('M d, Y') }}</td>
                            <td class="p-3 text-center">{{ $day->order_count }} orders</td>
                            <td class="p-3 text-right font-extrabold text-emerald-800">RM{{ number_format($day->daily_revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-slate-400 italic">No sales logs found for the given timeline.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Product Best Sellers -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
            <span>🏆</span> Best Selling Products
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs font-medium text-slate-600">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-3">Product Name</th>
                        <th class="p-3 text-center">Units Sold</th>
                        <th class="p-3 text-right">Sales Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($bestSellers as $item)
                        @if($item->product)
                            <tr>
                                <td class="p-3 font-bold text-slate-900">{{ $item->product->name }}</td>
                                <td class="p-3 text-center">{{ $item->units_sold }} units</td>
                                <td class="p-3 text-right font-extrabold text-emerald-800">RM{{ number_format($item->revenue, 2) }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-slate-400 italic">No transactions processed.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- 3. Outdoor Sales Staff Agent Performance -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
            <span>🎪</span> Outdoor Sales Staff Performance
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs font-medium text-slate-600">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-3">Sales Agent Name</th>
                        <th class="p-3 text-center">Events Logged</th>
                        <th class="p-3 text-right">Total Sales Logged</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($staffSales as $agent)
                        @if($agent->creator)
                            <tr>
                                <td class="p-3 font-bold text-slate-900">{{ $agent->creator->name }}</td>
                                <td class="p-3 text-center">{{ $agent->order_count }} events</td>
                                <td class="p-3 text-right font-extrabold text-emerald-800">RM{{ number_format($agent->total_sales, 2) }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-slate-400 italic">No outdoor event sales recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Stock Warning Report -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
            <span>⚠️</span> Low Stock & Replenishment Alarm
        </h3>

        <div class="overflow-y-auto max-h-60 space-y-2 pr-1">
            <!-- Products low stock -->
            @foreach($lowStockProducts as $lp)
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs flex justify-between items-center font-semibold text-amber-900">
                    <span>{{ $lp->name }} (Base)</span>
                    <span>Stock: {{ $lp->stock }} units</span>
                </div>
            @endforeach

            <!-- Variations low stock -->
            @foreach($lowStockVariations as $lv)
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs flex justify-between items-center font-semibold text-rose-900">
                    <span>{{ $lv->product->name }} — {{ $lv->name }}: {{ $lv->value }}</span>
                    <span>Stock: {{ $lv->stock }} units</span>
                </div>
            @endforeach

            @if($lowStockProducts->isEmpty() && $lowStockVariations->isEmpty())
                <p class="text-slate-500 text-xs text-center py-6">All stock levels are optimal. Purchaser can relax!</p>
            @endif
        </div>
    </div>

</div>
@endsection
