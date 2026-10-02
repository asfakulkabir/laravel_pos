<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Search Results
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Found {{ $products->total() }} results for "<span class="font-bold text-teal-600">{{ $query }}</span>"
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg font-bold text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                View All Products
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        @if($products->isEmpty())
            <div class="bg-white p-12 rounded-2xl shadow-sm border border-slate-200 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800">No match found</h3>
                <p class="text-slate-500 mt-2 max-w-sm mx-auto">We couldn't find any products matching your search criteria. Try different keywords or check for typos.</p>
                <div class="mt-8">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-teal-600 text-white rounded-xl font-bold shadow-lg hover:bg-teal-700 transition-all">
                        Browse all products
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <a href="{{ route('products.show', $product) }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded-full bg-slate-100 text-slate-600">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                                <span class="font-mono text-[10px] text-slate-400">{{ $product->sku }}</span>
                            </div>
                            
                            <h4 class="font-bold text-slate-800 group-hover:text-teal-600 transition-colors mb-2 truncate">
                                {{ $product->name }}
                            </h4>
                            
                            <div class="flex items-end justify-between mt-6">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Price</p>
                                    <span class="text-xl font-black text-slate-800">৳{{ number_format($product->selling_price, 2) }}</span>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Stock</p>
                                    <span class="text-sm font-bold {{ $product->total_stock > 5 ? 'text-teal-600' : 'text-red-600' }}">
                                        {{ $product->total_stock }} <small class="text-[10px] font-bold opacity-60">UNITS</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->appends(['search' => $query])->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
