<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Order History
            </h2>
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <form action="{{ route('orders.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search ID, Name..."
                               class="bg-slate-100 border-none rounded-xl py-2 pl-4 pr-10 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 transition-all w-48">
                    </div>
                    <select name="user_id" onchange="this.form.submit()" class="bg-slate-100 border-none rounded-xl py-2 px-4 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 transition-all cursor-pointer">
                        <option value="">Sold By</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-1">
                        <span class="text-[10px] font-black text-slate-400 uppercase">From</span>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               class="bg-transparent border-none p-1 text-xs font-bold text-slate-700 focus:ring-0">
                        <span class="text-[10px] font-black text-slate-400 uppercase">To</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               class="bg-transparent border-none p-1 text-xs font-bold text-slate-700 focus:ring-0">
                    </div>
                    <button type="submit" class="p-2 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    <a href="{{ route('orders.export_bulk_json', request()->all()) }}" class="whitespace-nowrap inline-flex items-center px-4 py-2 bg-slate-800 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-slate-900 shadow-lg shadow-slate-800/20 transition-all">
                        Export JSON
                    </a>
                </form>
                <a href="{{ route('pos.index') }}" class="whitespace-nowrap inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-teal-700 active:bg-teal-900 shadow-lg shadow-teal-600/20 transition-all">
                    + New Sale
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Order ID</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date & Time</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sold By</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Items</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Payment</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Amount</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($orders as $order)
                                    <tr class="hover:bg-slate-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-xs font-bold text-slate-400">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-slate-700">{{ $order->created_at->format('M d, Y') }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $order->created_at->format('h:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($order->customer_name)
                                                <div class="text-sm font-bold text-slate-800">{{ $order->customer_name }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $order->customer_phone ?? 'No phone' }}</div>
                                            @else
                                                <span class="text-xs italic text-slate-400">Walk-in Customer</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs font-bold text-slate-600 uppercase tracking-tighter">
                                                {{ $order->user->name ?? 'System' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase">
                                                {{ $order->items_count }} {{ Str::plural('Item', $order->items_count) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter {{ $order->payment_method === 'cash' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                                                {{ $order->payment_method }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-black text-slate-900">৳{{ number_format($order->total_amount, 2) }}</div>
                                            @if($order->exchange_amount != 0)
                                                <div class="text-[9px] font-bold uppercase tracking-tighter {{ $order->exchange_amount > 0 ? 'text-green-600' : 'text-red-500' }}">
                                                    Exchange: {{ $order->exchange_amount > 0 ? '+' : '' }}৳{{ number_format($order->exchange_amount, 0) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                                            <a href="{{ route('orders.export_json', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-800 border border-transparent rounded-xl text-[10px] font-black text-white uppercase tracking-widest hover:bg-slate-900 transition-all shadow-sm">
                                                JSON
                                            </a>
                                            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-600 uppercase tracking-widest hover:border-teal-500 hover:text-teal-600 transition-all shadow-sm">
                                                Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-20 text-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            </div>
                                            <div class="text-slate-500 font-bold">No orders found.</div>
                                            <p class="text-slate-400 text-xs mt-1">Start making sales in the POS Terminal!</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($orders->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
