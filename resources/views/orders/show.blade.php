<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('orders.index') }}" class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Order Details <span class="text-teal-600 font-mono ml-2">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                </h2>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-black text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-50 transition drop-shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Receipt
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left: Summary Info -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Order Summary</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase">Order ID</label>
                                <div class="text-sm font-black text-teal-600 font-mono">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase">Created At</label>
                                <div class="text-sm font-black text-slate-800">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase">Sold By</label>
                                <div class="text-sm font-black text-slate-800">{{ $order->user->name ?? 'System' }}</div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase">Payment Method</label>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter {{ $order->payment_method === 'cash' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                                        {{ $order->payment_method }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Customer Info</h4>
                        <div class="space-y-4">
                            @if($order->customer_name)
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase">Name</label>
                                    <div class="text-sm font-black text-slate-800">{{ $order->customer_name }}</div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase">Phone</label>
                                    <div class="text-sm font-black text-slate-800 font-mono">{{ $order->customer_phone ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase">Address</label>
                                    <div class="text-xs text-slate-600 leading-relaxed">{{ $order->customer_address ?? 'N/A' }}</div>
                                </div>
                            @else
                                <div class="text-xs italic text-slate-400">No customer information recorded for this order.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Itemized List -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Product Details</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Qty</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Price</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-black text-slate-800">{{ $item->product_name ?? $item->productSize?->color?->product?->name ?? 'Deleted Product' }}</div>
                                            <div class="text-[10px] text-slate-500">
                                                Color: {{ $item->color_name ?? $item->productSize?->color?->color?->name ?? 'N/A' }} | Size: {{ $item->size_name ?? $item->productSize?->size?->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-[10px] font-mono text-slate-400">{{ $item->product_sku ?? $item->productSize?->sku ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-slate-600 text-sm">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($item->discount_amount > 0)
                                                <div class="text-[10px] text-slate-400 line-through">৳{{ number_format($item->base_price, 2) }}</div>
                                            @endif
                                            <div class="font-bold text-slate-600 text-sm italic">
                                                ৳{{ number_format($item->unit_price, 2) }}
                                            </div>
                                            @if($item->discount_amount > 0)
                                                <div class="text-[9px] text-red-500 font-bold mt-0.5">
                                                    -{{ $item->discount_type === 'percent' ? $item->discount_value.'%' : '৳'.number_format($item->discount_value, 2) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-slate-800 text-sm">
                                            ৳{{ number_format($item->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="p-6 bg-slate-50/30 border-t border-slate-100 space-y-3">
                            @php
                                $itemDiscountsTotal = $order->items->sum('discount_amount');
                                if (isset($order->vat_amount)) {
                                    $calculatedVat = $order->vat_amount;
                                } else {
                                    $calculatedVat = $order->total_amount - ($order->subtotal - $itemDiscountsTotal - $order->discount_amount + ($order->exchange_amount ?? 0));
                                    $calculatedVat = max(0, $calculatedVat);
                                }
                            @endphp
                            
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-500 uppercase tracking-tighter">Subtotal</span>
                                <span class="font-black text-slate-700">৳{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            
                            @if($itemDiscountsTotal > 0)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold text-red-400 uppercase tracking-tighter">Item Discounts</span>
                                    <span class="font-black text-red-600">-৳{{ number_format($itemDiscountsTotal, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-red-400 uppercase tracking-tighter">Order Discount</span>
                                        <span class="px-1.5 py-0.5 bg-red-50 text-red-600 rounded text-[10px] font-black uppercase tracking-widest">
                                            {{ $order->discount_type === 'percent' ? 'Percentage' : 'Flat Rate' }}
                                        </span>
                                    </div>
                                    <span class="font-black text-red-600">-৳{{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($calculatedVat > 0)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold text-slate-500 uppercase tracking-tighter">VAT</span>
                                    <span class="font-black text-slate-700">+৳{{ number_format($calculatedVat, 2) }}</span>
                                </div>
                            @endif
                            @if($order->exchange_amount != 0)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold text-slate-500 uppercase tracking-tighter">Exchange Adjustment</span>
                                    <span class="font-black {{ $order->exchange_amount > 0 ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $order->exchange_amount > 0 ? '+' : '' }}৳{{ number_format($order->exchange_amount, 2) }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                                <span class="text-base font-black text-slate-800 uppercase">Total Amount</span>
                                <span class="font-black text-2xl text-teal-600">৳{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            header, nav, .py-12 > div > div > a, button {
                display: none !important;
            }
            .py-12 {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }
            .max-w-4xl {
                max-width: 100% !important;
            }
            .bg-white {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</x-app-layout>
