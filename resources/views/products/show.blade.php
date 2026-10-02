<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="font-black text-3xl md:text-4xl text-slate-900 leading-tight tracking-tight">
                    {{ $product->name }}
                </h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="font-mono bg-slate-100 px-3 py-1 rounded text-sm font-bold text-slate-600 border border-slate-200">{{ $product->sku }}</span>
                    <span class="text-slate-300">/</span>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Added {{ $product->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 rounded-xl font-black text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm active:scale-95 group">
                    <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to List
                </a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-6 py-2.5 bg-slate-900 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg active:scale-95 group">
                        <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Product
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 space-y-10">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Stock Summary -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <svg class="w-24 h-24 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 11v10l8 4"></path></svg>
                </div>
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.15em] mb-4">Inventory Status</h4>
                <div class="flex items-end justify-between">
                    <div>
                        <span class="text-4xl font-black text-slate-900 leading-none">{{ $product->total_stock }}</span>
                        <span class="text-slate-400 font-black ml-1 text-sm uppercase">Units Available</span>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full {{ $product->total_stock > 10 ? 'bg-teal-500' : 'bg-red-500' }} animate-pulse"></span>
                    <span class="text-sm font-black uppercase tracking-widest {{ $product->total_stock > 10 ? 'text-teal-600' : 'text-red-600' }}">
                        {{ $product->total_stock > 10 ? 'Healthy Stock' : 'Low Stock Level' }}
                    </span>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.15em] mb-4">Pricing Breakdown</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1 text-slate-400 uppercase">Selling Price</p>
                            <span class="text-3xl font-black text-slate-900 leading-none">৳{{ number_format($product->selling_price, 2) }}</span>
                        </div>
                        @if($product->costing_price)
                            <div class="text-right">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1 text-slate-400 uppercase">Net Cost</p>
                                <span class="text-lg font-black text-slate-600">৳{{ number_format($product->costing_price, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <svg class="w-24 h-24 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.15em] mb-4">Sales Performance</h4>
                <div class="flex items-end justify-between">
                    <div>
                        <span class="text-4xl font-black text-slate-900 leading-none">{{ $product->sold }}</span>
                        <span class="text-slate-400 font-black ml-1 text-sm uppercase">Total Sold</span>
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-black uppercase tracking-widest">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    @if($product->subcategory)
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-lg text-xs font-black uppercase tracking-widest">{{ $product->subcategory->name }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            <!-- Left Panel: Product Description & Info -->
            <div class="lg:col-span-1 space-y-10">
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100 sticky top-10 overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-slate-50 rounded-bl-full -mr-12 -mt-12"></div>
                    
                    <h3 class="text-lg font-black text-slate-900 mb-8 flex items-center justify-between">
                        Product Details
                        <span class="w-10 h-1 bg-slate-100 rounded-full"></span>
                    </h3>

                    <div class="space-y-8">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                Description
                            </p>
                            <div class="text-base text-slate-600 leading-relaxed font-medium">
                                {!! nl2br(e($product->description)) !!}
                                @if(empty($product->description))
                                    <p class="italic text-slate-400">No description provided.</p>
                                @endif
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-100 space-y-6">
                            <div class="flex justify-between items-center group">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Active Status</span>
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.1em] {{ $product->is_active ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                    {{ $product->is_active ? 'Live on Store' : 'Hidden from Shop' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Product Type</span>
                                <span class="text-sm font-black text-slate-800">{{ $product->type ?? 'Global' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Inventory Breakdown (Variants) -->
            <div class="lg:col-span-3 space-y-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Available Variants</h3>
                        <p class="text-xs text-slate-400 font-black uppercase tracking-widest mt-1">Detailed inventory by color and size</p>
                    </div>
                    <div class="bg-white border border-slate-200 px-4 py-2 rounded-2xl flex items-center gap-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Variations</span>
                        <span class="text-lg font-black text-slate-900 leading-none">{{ $product->colors->count() }}</span>
                    </div>
                </div>

                @forelse($product->colors as $pColor)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group hover:border-slate-200 transition-all">
                        <!-- Color Section Header -->
                        <div class="bg-slate-50/50 px-10 py-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-center gap-6">
                                <div class="relative">
                                    <span class="block w-12 h-12 rounded-[1.25rem] shadow-sm border-2 border-white ring-1 ring-slate-100" style="background-color: {{ strtolower($pColor->color->name) }}"></span>
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white border border-slate-100 rounded-full flex items-center justify-center">
                                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ strtolower($pColor->color->name) }}"></div>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-black text-2xl text-slate-900 uppercase tracking-tight">{{ $pColor->color->name }}</h4>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 flex items-center gap-2">
                                        VARIANT COLOR
                                        @if($pColor->box_number)
                                            <span class="text-slate-300">|</span>
                                            <span class="text-indigo-600 font-bold uppercase tracking-tight">BOX #{{ $pColor->box_number }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-10">
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 leading-none">Net Stock</p>
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-2xl font-black text-slate-900 leading-none">{{ $pColor->total_stock }}</span>
                                        <span class="text-[10px] font-black text-slate-400 uppercase">Qty</span>
                                    </div>
                                </div>
                                <div class="h-8 w-px bg-slate-200 hidden md:block"></div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 leading-none text-orange-500">Items Sold</p>
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-2xl font-black text-slate-900 leading-none">{{ $pColor->sold }}</span>
                                        <span class="text-[10px] font-black text-slate-400 uppercase">Qty</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sizes breakdown table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] bg-white border-b border-slate-50">
                                    <tr>
                                        <th class="px-10 py-5">Size Variant</th>
                                        <th class="px-10 py-5">Tracking (SKU)</th>
                                        <th class="px-8 py-5 text-center">Box Reference</th>
                                        <th class="px-8 py-5 text-right font-black text-slate-800">Unit Price</th>
                                        <th class="px-10 py-5 text-center">Status (Sold)</th>
                                        <th class="px-10 py-5 text-right">Available Stock</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50/80">
                                    @foreach($pColor->sizes as $pSize)
                                        <tr class="hover:bg-slate-50/30 transition-colors group/row">
                                            <td class="px-10 py-6">
                                                <span class="text-lg font-black text-slate-900 group-hover/row:text-teal-600 transition-colors">{{ $pSize->size->name }}</span>
                                            </td>
                                            <td class="px-10 py-6">
                                                <span class="text-xs font-bold font-mono text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">{{ $pSize->sku }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <span class="text-sm font-black tracking-tight {{ $pSize->box_number ? 'text-indigo-600 font-bold' : 'text-slate-300 italic' }}">
                                                    {{ $pSize->box_number ?? 'Not Set' }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                @if($pSize->price)
                                                    <span class="text-lg font-black text-slate-900 tracking-tight">৳{{ number_format($pSize->price, 2) }}</span>
                                                @else
                                                    <span class="text-xs font-black text-slate-300 uppercase tracking-widest italic font-bold">Base Price</span>
                                                @endif
                                            </td>
                                            <td class="px-10 py-6 text-center">
                                                <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-xs font-black bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ $pSize->sold }} Sold
                                                </span>
                                            </td>
                                            <td class="px-10 py-6 text-right">
                                                <span class="inline-flex items-center px-5 py-2 rounded-2xl text-base font-black {{ $pSize->stock > 5 ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                                    {{ $pSize->stock }}
                                                    <small class="text-[10px] ml-2 opacity-60 uppercase font-black">Left</small>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-50 border-2 border-dashed border-slate-200 p-16 rounded-[3rem] text-center">
                        <div class="bg-white w-20 h-20 rounded-full shadow-sm flex items-center justify-center mx-auto mb-6 border border-slate-100">
                             <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">Missing Inventory Data</h4>
                        <p class="text-slate-500 font-medium mt-2 max-w-sm mx-auto">No color or size variants have been configured for this product yet.</p>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product) }}" class="mt-8 inline-flex items-center px-8 py-3 bg-teal-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-teal-700 transition-all shadow-xl shadow-teal-500/20 active:scale-95">
                                Add Variations Now
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
