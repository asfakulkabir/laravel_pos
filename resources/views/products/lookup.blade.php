<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            Product Lookup
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-10">
                <div class="w-20 h-20 bg-teal-50 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-teal-100">
                    <svg class="w-10 h-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1l-1 1h2l-1-1V4m-5 8h10M4 7h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V9a2 2 0 012-2z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-black text-slate-900 mb-2 italic">SCAN BARCODE</h1>
                <p class="text-slate-500 font-medium">Or type SKU / Product Name to find instant details.</p>
            </div>

            <form action="{{ route('products.search') }}" method="get" class="relative group max-w-2xl mx-auto mb-12">
                <div class="absolute inset-y-0 left-0 flex items-center pl-6 pointer-events-none transition-colors group-focus-within:text-teal-600 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" autofocus value="{{ $query ?? '' }}"
                    class="block w-full pl-16 pr-6 py-6 text-xl font-bold bg-white border-2 border-slate-100 rounded-3xl shadow-xl focus:ring-0 focus:border-teal-500 transition-all placeholder:text-slate-300"
                    placeholder="Enter SKU / Name / Scan Barcode..." />
                
                <div class="absolute inset-y-0 right-4 flex items-center">
                    <button type="submit" class="px-6 py-3 bg-teal-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-teal-700 transition-all shadow-lg active:scale-95">
                        Search
                    </button>
                </div>
            </form>

            @if(isset($product))
            <div class="bg-white p-8 rounded-xl border-2 border-teal-200 mb-12 text-left shadow-xl">
                <h3 class="text-2xl font-extrabold text-gray-900 mb-6 border-b border-gray-200 pb-3 flex justify-between items-center">
                    <span class="flex items-center gap-3">
                        Product: <span class="text-teal-600">{{ $product->name }}</span>
                        @if($product->type)
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full border border-gray-200 uppercase tracking-wider">
                            {{ $product->type }}
                        </span>
                        @endif
                    </span>
                    <a href="{{ route('products.edit', $product) }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1 transition duration-200 p-2 rounded-lg bg-blue-50 hover:bg-blue-100 border border-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Go to Edit
                    </a>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-start">
                    <div class="md:col-span-2">
                        @if($product->main_image)
                            <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}" class="w-full h-56 object-cover rounded-xl border border-gray-100">
                        @else
                        <div class="w-full h-56 bg-gray-100 rounded-xl flex flex-col items-center justify-center text-gray-500 font-semibold border-2 border-dashed border-gray-300">
                            <svg class="h-10 w-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-2-4h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            No Main Image Available
                        </div>
                        @endif
                    </div>

                    <div class="md:col-span-3 space-y-4 pt-2">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <div class="flex justify-between mb-2 pb-2 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-600">Base SKU:</p>
                                <p class="text-sm font-mono font-bold text-gray-800">{{ $product->sku }}</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-sm font-medium text-gray-600">Category:</p>
                                <span class="px-3 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xs font-bold">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 border-2 border-green-300 bg-green-50 rounded-xl">
                                <p class="text-sm font-medium text-green-800">Selling Price:</p>
                                <p class="text-2xl font-extrabold text-green-900 mt-1">৳ {{ number_format($product->selling_price, 2) }}</p>
                            </div>
                            <div class="p-4 rounded-xl border-2 border-red-300 bg-red-50">
                                <p class="text-sm font-medium text-red-800">Total Inventory:</p>
                                <p class="text-2xl font-extrabold text-red-900 mt-1">{{ $product->total_stock }} units</p>
                            </div>
                        </div>

                        <!-- Color Stock Breakdown -->
                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="h-5 w-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M17 7h.01M17 11h.01M17 15h.01">
                                    </path>
                                </svg>
                                Stock by Color
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @forelse($product->colors as $pColor)
                                <div class="flex items-center gap-3 p-3 bg-white border border-gray-100 rounded-lg shadow-sm hover:border-teal-200 transition duration-200">
                                    <div class="h-10 w-10 rounded bg-gray-50 flex items-center justify-center border border-gray-100 relative overflow-hidden">
                                        <div class="absolute inset-0 opacity-20" style="background-color: {{ strtolower($pColor->color->name) }}"></div>
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-2-4h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $pColor->color->name }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs font-semibold px-2 py-0.5 bg-teal-50 text-teal-700 rounded-full border border-teal-100">
                                                {{ $pColor->total_stock }} units
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic">No variants found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($productSize))
                <!-- Exact Variant Match -->
                <hr class="my-8 border-t-2 border-blue-300">
                <h4 class="text-xl font-bold text-blue-700 mb-5 flex items-center gap-2">
                    Variant Details (Exact SKU Match)
                </h4>
                <div class="bg-blue-50 p-6 rounded-xl border-2 border-blue-300 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-6 items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Color:</p>
                        <p class="text-xl font-bold text-blue-800">{{ $productSize->color->color->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Size:</p>
                        <p class="text-xl font-bold text-blue-800">{{ $productSize->size->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Box Number:</p>
                        <p class="text-xl font-bold text-blue-800">{{ $productSize->box_number ?: 'Not Set' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Price:</p>
                        <p class="text-xl font-bold text-blue-800">৳ {{ number_format($productSize->price ?: $product->selling_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Stock:</p>
                        <p class="text-xl font-bold text-blue-800">{{ $productSize->stock }}</p>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:border-teal-100 transition-colors group">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-50">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm mb-1 uppercase tracking-tight">Search by SKU</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Exact SKU matches will redirect you straight to the product details page.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:border-teal-100 transition-colors group">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-50">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h4l3 10 4-18 3 8h4"></path></svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm mb-1 uppercase tracking-tight">Barcode Ready</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">System is optimized for handheld barcode scanners. Just point and scan.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:border-teal-100 transition-colors group">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-50">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm mb-1 uppercase tracking-tight">Stock Insight</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Instantly check real-time stock levels across all color and size variations.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
