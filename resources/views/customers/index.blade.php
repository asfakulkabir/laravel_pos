<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Customer Database
            </h2>
            <div class="px-3 py-1 bg-teal-50 text-teal-600 rounded-lg text-xs font-black uppercase tracking-widest">
                {{ $customers->total() }} Total Customers
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
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer info</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Phone Number</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Address</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Total Orders</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total Purchase</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Last Visit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($customers as $customer)
                                    <tr class="hover:bg-slate-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-black text-xs uppercase">
                                                    {{ substr($customer->name, 0, 2) }}
                                                </div>
                                                <div class="text-sm font-bold text-slate-800">{{ $customer->name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-xs font-bold text-slate-600">{{ $customer->phone }}</span>
                                        </td>
                                        <td class="px-6 py-4 space-y-1">
                                            <div class="text-xs text-slate-500 max-w-xs truncate" title="{{ $customer->address }}">
                                                {{ $customer->address ?? 'No address provided' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase">
                                                {{ $customer->total_orders }} {{ Str::plural('Order', $customer->total_orders) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-teal-600">
                                            ৳{{ number_format($customer->total_spent, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-[10px] font-bold text-slate-400 uppercase">{{ $customer->updated_at->format('M d, Y') }}</div>
                                            <div class="text-[10px] text-slate-300 italic">{{ $customer->updated_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-20 text-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </div>
                                            <div class="text-slate-500 font-bold">No customers recorded yet.</div>
                                            <p class="text-slate-400 text-xs mt-1">Customers will appear here once they make their first purchase at the POS.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($customers->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                            {{ $customers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
