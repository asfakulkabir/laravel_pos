<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Low Stock Products
            </h2>
            <div class="flex items-center space-x-3 no-print">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg font-bold text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-200 transition-all shadow-sm active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print List
                </button>
                <span class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-bold border border-red-100">
                    Threshold: < 2 Units
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Specific Print Header -->
            <div class="hidden print-only mb-8">
                <div class="flex justify-between items-end border-b-2 border-slate-900 pb-4">
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 uppercase">Low Stock Inventory Report</h1>
                        <p class="text-slate-500 font-bold mt-1">Generated for: Mamata Fashion - Warehouse</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-600">Report Date:</p>
                        <p class="text-xl font-black text-slate-900">{{ now()->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-200 print-no-shadow print-no-border">
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100 print-bg-gray">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold">Product Information</th>
                                <th scope="col" class="px-6 py-4 font-bold">SKU</th>
                                <th scope="col" class="px-6 py-4 font-bold text-center">Current Stock</th>
                                <th scope="col" class="px-6 py-4 font-bold text-right no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($lowStockVariants as $variant)
                                <tr class="bg-white hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800 text-base">
                                                {{ $variant->color->product->name }}-{{ $variant->color->color->name }}-{{ $variant->size->name }}
                                            </span>
                                            <span class="text-[10px] text-teal-600 font-bold uppercase tracking-tight">
                                                {{ $variant->color->product->category->name ?? 'Uncategorized' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                        {{ $variant->sku }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 text-xs font-black rounded-full bg-red-100 text-red-700 border border-red-200 print-no-bg">
                                            {{ $variant->stock }} Units
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right no-print">
                                        <a href="{{ route('products.edit', $variant->color->product) }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-teal-600 hover:text-white transition-all shadow-sm active:scale-95">
                                            Manage Stock
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-10 h-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-800">No low stock items</h3>
                                        <p class="text-slate-500 mt-1">All your products have healthy inventory levels.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="hidden print-only mt-12 text-center text-slate-400 text-[10px] uppercase font-bold tracking-[0.2em]">
                End of Low Stock Report &bull; Protected by Mamata Fashion Warehouse Management
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; padding: 0 !important; }
            .py-12 { padding-top: 0 !important; padding-bottom: 0 !important; }
            .max-w-7xl { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
            .print-no-shadow { shadow: none !important; box-shadow: none !important; }
            .print-no-border { border: none !important; }
            .print-bg-gray { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
            .print-no-bg { background: transparent !important; border: 1px solid #ef4444 !important; }
            
            nav, aside, header { display: none !important; }
            main { margin: 0 !important; padding: 0 !important; }
        }
    </style>
</x-app-layout>
