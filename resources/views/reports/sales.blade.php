<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('Sales & Profit Report') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Filters -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <form action="{{ route('reports.sales') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-sm font-bold text-slate-700 mb-1">From Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-bold text-slate-700 mb-1">To Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg transition-all shadow-md active:scale-95 uppercase tracking-wide text-xs">
                    Generate Report
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Revenue -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Sales Revenue</p>
                <div class="mt-2 text-3xl font-black text-slate-800">
                    ৳{{ number_format($totalRevenue, 2) }}
                </div>
                <p class="text-xs text-slate-400 mt-1">Based on final invoice amounts</p>
            </div>

            <!-- Total Cost -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Product Cost</p>
                <div class="mt-2 text-3xl font-black text-slate-600">
                    ৳{{ number_format($totalCost, 2) }}
                </div>
                <p class="text-xs text-slate-400 mt-1">Sum of product costing prices</p>
            </div>

            <!-- Total Profit -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-4 opacity-5">
                    <svg class="w-24 h-24 text-teal-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-bold text-teal-600 uppercase tracking-wider">Net Profit</p>
                <div class="mt-2 text-3xl font-black {{ $totalProfit >= 0 ? 'text-teal-600' : 'text-red-500' }}">
                    ৳{{ number_format($totalProfit, 2) }}
                </div>
                <p class="text-xs text-teal-600/70 mt-1 font-bold">Revenue - Cost</p>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-bold text-lg text-slate-800">Detailed Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 font-bold">Date & Time</th>
                            <th class="px-6 py-4 font-bold">Order ID</th>
                            <th class="px-6 py-4 font-bold">Customer</th>
                            <th class="px-6 py-4 font-bold text-center">Items</th>
                            <th class="px-6 py-4 font-bold text-right">Selling Amount</th>
                            <th class="px-6 py-4 font-bold text-right">Product Cost</th>
                            <th class="px-6 py-4 font-bold text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($reportData as $row)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 text-slate-600 font-medium">
                                    {{ $row['date']->format('M d, Y') }} <br>
                                    <span class="text-[10px] text-slate-400">{{ $row['date']->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('orders.show', $row['order_id']) }}" class="text-teal-600 font-bold hover:underline">
                                        #{{ str_pad($row['order_id'], 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-slate-700 font-semibold">{{ $row['customer'] }}</td>
                                <td class="px-6 py-4 text-center font-bold">{{ $row['items_count'] }}</td>
                                <td class="px-6 py-4 text-right font-bold text-slate-800">৳{{ number_format($row['revenue'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-slate-600">৳{{ number_format($row['cost'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-black {{ $row['profit'] >= 0 ? 'text-teal-600' : 'text-red-500' }}">
                                    ৳{{ number_format($row['profit'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
                                    No completed sales found for the selected date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right font-black text-slate-600 uppercase">Totals</td>
                            <td class="px-6 py-4 text-right font-black text-slate-800">৳{{ number_format($totalRevenue, 2) }}</td>
                            <td class="px-6 py-4 text-right font-black text-slate-600">৳{{ number_format($totalCost, 2) }}</td>
                            <td class="px-6 py-4 text-right font-black {{ $totalProfit >= 0 ? 'text-teal-600' : 'text-red-500' }}">৳{{ number_format($totalProfit, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
