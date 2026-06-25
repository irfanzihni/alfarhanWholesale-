@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-extrabold text-slate-900 mb-8 serif-font">My Orders</h1>

    @if($orders->isEmpty())
        <div class="bg-white border border-emerald-100 rounded-3xl p-16 text-center shadow-xs space-y-4">
            <span class="text-5xl">📦</span>
            <h3 class="text-xl font-bold text-slate-800">No orders placed yet</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto font-medium">Browse our selection of dates, honey, perfume, bakhoor, and more — and place your first order!</p>
            <a href="{{ route('shop.index') }}" class="inline-block bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-8 py-3 rounded-full text-sm shadow-md transition-all">
                Start Shopping
            </a>
        </div>
    @else
        <div class="bg-white border border-emerald-100 rounded-2xl shadow-xs overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-emerald-50/60 border-b border-emerald-100 text-slate-600 font-bold text-xs uppercase tracking-wider">
                        <th class="p-6">Order ID</th>
                        <th class="p-6">Date</th>
                        <th class="p-6">Type</th>
                        <th class="p-6">Items Purchased</th>
                        <th class="p-6">Total Amount</th>
                        <th class="p-6 text-center">Status</th>
                        <th class="p-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    @foreach($orders as $order)
                        <tr>
                            <!-- Order ID -->
                            <td class="p-6 font-bold text-slate-950">
                                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                            </td>

                            <!-- Order Date -->
                            <td class="p-6">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>

                            <!-- Order Type -->
                            <td class="p-6">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $order->order_type == 'online' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $order->order_type }}
                                </span>
                            </td>

                            <!-- Item names summary -->
                            <td class="p-6 text-xs text-slate-500 max-w-xs truncate">
                                @foreach($order->items as $item)
                                    {{ $item->product->name }} (x{{ $item->quantity }}){{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </td>

                            <!-- Total Amount -->
                            <td class="p-6 font-bold text-emerald-800">
                                RM{{ number_format($order->final_amount, 2) }}
                            </td>

                            <!-- Order Status Badge -->
                            <td class="p-6 text-center">
                                @switch($order->status)
                                    @case('pending')
                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Pending</span>
                                        @break
                                    @case('processing')
                                        <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Processing</span>
                                        @break
                                    @case('completed')
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Completed</span>
                                        @break
                                    @case('cancelled')
                                        <span class="bg-rose-100 text-rose-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Cancelled</span>
                                        @break
                                    @default
                                        <span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $order->status }}</span>
                                @endswitch
                            </td>

                            <!-- View Link -->
                            <td class="p-6 text-right">
                                <a href="{{ route('checkout.success', $order->id) }}" class="text-emerald-700 hover:text-emerald-950 font-bold transition-colors">
                                    View Invoice →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
