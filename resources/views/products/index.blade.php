<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">Products</h2>
            <div class="flex items-center space-x-3">
                @if(auth()->user()->isAdmin())
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center">
                    @csrf
                    <input type="file" name="file" id="import_product" class="hidden" onchange="this.form.submit()">
                    <button type="button" onclick="document.getElementById('import_product').click()" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg font-bold text-xs text-slate-600 uppercase tracking-widest hover:bg-slate-200 transition-all shadow-sm active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import JSON
                    </button>
                </form>
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-teal-700 active:bg-teal-800 transition-all shadow-md active:scale-95">
                    Add Product
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 p-4 bg-teal-50 border-l-4 border-teal-500 text-teal-700 rounded-r-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-0 text-slate-900 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">SKU</th>
                        <th scope="col" class="px-6 py-4 font-bold">Product Details</th>
                        <th scope="col" class="px-6 py-4 font-bold">Type</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Selling Price</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Current Stock</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Sold</th>
                        @if(auth()->user()->isAdmin())
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="bg-white hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                {{ $product->sku }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('products.show', $product) }}" class="font-bold text-slate-800 hover:text-teal-600 transition-colors">
                                    {{ $product->name }}
                                </a>
                                <div class="text-[10px] space-x-1">
                                    <span class="text-teal-600 font-bold uppercase tracking-tight">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                    @if($product->subcategory)
                                        <span class="text-slate-300">/</span>
                                        <span class="text-slate-500 font-medium">{{ $product->subcategory->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                {{ $product->type ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-700">
                                ৳{{ number_format($product->selling_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $product->total_stock > 10 ? 'bg-teal-100 text-teal-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $product->total_stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">
                                    {{ $product->sold }}
                                </span>
                            </td>
                            @if(auth()->user()->isAdmin())
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('products.edit', $product) }}" class="p-2 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all" title="Edit Product">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No products found. @if(auth()->user()->isAdmin()) <a href="{{ route('products.create') }}" class="text-indigo-600 hover:underline">Create one</a>. @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
