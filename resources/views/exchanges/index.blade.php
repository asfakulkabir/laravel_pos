<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Product Exchange History
            </h2>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:border-orange-900 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150">
                + New Exchange
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Exchange ID</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-red-500">Returned Product</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-green-600">New Product</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Calculation</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Details</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Order Reference</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($exchanges as $exchange)
                                    <tr class="hover:bg-slate-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-xs font-bold text-slate-400">#EX-{{ str_pad($exchange->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs font-bold text-slate-700">{{ $exchange->created_at->format('M d, Y') }}</div>
                                            <div class="text-[9px] text-slate-400">{{ $exchange->created_at->format('h:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($exchange->returnedProductSize)
                                                <div class="text-xs font-black text-slate-800">{{ $exchange->returnedProductSize->color?->product?->name }}</div>
                                                <div class="text-[9px] text-slate-500">
                                                    {{ $exchange->returnedProductSize->color?->color?->name }} | {{ $exchange->returnedProductSize->size?->name }}
                                                </div>
                                                <div class="text-[9px] font-mono font-bold text-orange-400 uppercase">{{ $exchange->returnedProductSize->sku }}</div>
                                            @elseif($exchange->order && $exchange->order->items->where('quantity', '<', 0)->first())
                                                @php $returnedItem = $exchange->order->items->where('quantity', '<', 0)->first(); @endphp
                                                <div class="text-xs font-black text-slate-800">{{ $returnedItem->product_name }}</div>
                                                <div class="text-[9px] text-slate-500">
                                                    {{ $returnedItem->color_name }} | {{ $returnedItem->size_name }}
                                                </div>
                                                <div class="text-[9px] font-mono font-bold text-orange-400 uppercase">{{ $returnedItem->product_sku }}</div>
                                            @else
                                                <span class="text-[10px] italic text-slate-400 font-bold uppercase tracking-tighter">Product Removed</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($exchange->newProductSize)
                                                <div class="text-xs font-black text-slate-800">{{ $exchange->newProductSize->color->product->name }}</div>
                                                <div class="text-[9px] text-slate-500">
                                                    {{ $exchange->newProductSize->color?->color?->name }} | {{ $exchange->newProductSize->size?->name }}
                                                </div>
                                                <div class="text-[9px] font-mono font-bold text-green-500 uppercase">{{ $exchange->newProductSize->sku }}</div>
                                            @else
                                                <span class="text-[10px] italic text-slate-400 font-bold uppercase tracking-tighter">No New Product</span>
                                                @if($exchange->price_note)
                                                    <div class="text-[9px] text-slate-500 mt-1 max-w-[150px] truncate" title="{{ $exchange->price_note }}">
                                                        Note: {{ $exchange->price_note }}
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-0.5">
                                                <div class="flex justify-between gap-4 text-[10px]">
                                                    <span class="text-slate-400 font-bold uppercase">Old:</span>
                                                    <span class="font-black text-slate-700">৳{{ number_format($exchange->old_unit_price ?? 0, 0) }}</span>
                                                </div>
                                                <div class="flex justify-between gap-4 text-[10px]">
                                                    <span class="text-slate-400 font-bold uppercase">New:</span>
                                                    <span class="font-black text-slate-700">৳{{ number_format($exchange->new_unit_price ?? 0, 0) }}</span>
                                                </div>
                                                 <div class="flex justify-between gap-4 text-[10px] border-t border-slate-100 pt-0.5 mt-0.5">
                                                     <span class="text-slate-500 font-bold uppercase">Difference:</span>
                                                     <span class="font-black {{ ($exchange->difference_amount ?? 0) >= 0 ? 'text-orange-600' : 'text-teal-600' }}">
                                                         {{ ($exchange->difference_amount ?? 0) >= 0 ? '+' : '' }}৳{{ number_format($exchange->difference_amount ?? 0, 0) }}
                                                     </span>
                                                 </div>
                                              </div>
                                          </td>
                                         <td class="px-6 py-4">
                                             @if($exchange->reason)
                                                 <div class="text-[10px] text-slate-600 font-medium italic max-w-[150px] truncate" title="{{ $exchange->reason }}">
                                                     "{{ $exchange->reason }}"
                                                 </div>
                                             @endif
                                             @if($exchange->discount_amount > 0)
                                                 <div class="mt-1">
                                                     <span class="px-1.5 py-0.5 rounded bg-red-50 text-red-600 text-[9px] font-black uppercase tracking-tighter">
                                                         Disc: {{ $exchange->discount_type === 'percent' ? $exchange->discount_amount.'%' : '৳'.number_format($exchange->discount_amount, 0) }}
                                                     </span>
                                                 </div>
                                             @endif
                                         </td>
                                         <td class="px-6 py-4 text-right">
                                            <div class="flex flex-col items-end gap-1">
                                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-widest mb-1">
                                                    By: {{ $exchange->user->name ?? 'System' }}
                                                </span>
                                                <a href="{{ route('orders.show', $exchange->order_id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-teal-600 uppercase tracking-widest hover:border-teal-500 hover:bg-teal-50 transition-all shadow-sm">
                                                    View Order #{{ str_pad($exchange->order_id, 6, '0', STR_PAD_LEFT) }}
                                                </a>
                                            </div>
                                         </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-20 text-center">
                                            <div class="text-slate-300 mb-4 flex justify-center">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            </div>
                                            <div class="text-slate-500 font-bold">No exchanges recorded.</div>
                                            <p class="text-slate-400 text-xs mt-1">Start processing exchanges in the POS Terminal!</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($exchanges->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                            {{ $exchanges->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
