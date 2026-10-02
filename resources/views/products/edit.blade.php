<x-app-layout>
    @php
        $variants = old('variants', $product->colors->filter(function($pColor) {
            return $pColor->sizes->count() > 0;
        })->map(function($pColor) {
            return [
                'id' => $pColor->id,
                'color_id' => $pColor->color_id,
                'box_number' => $pColor->box_number,
                'stock' => $pColor->sizes->sum('stock'), // Current total for this color
                'sizes' => $pColor->sizes->map(function($pSize) {
                    return [
                        'id' => $pSize->id,
                        'size_id' => $pSize->size_id,
                        'stock' => $pSize->stock,
                        'sold' => $pSize->sold,
                        'box_number' => $pSize->box_number,
                        'sku' => $pSize->sku, // Product Size SKU
                    ];
                })->values()
            ];
        })->values());

        $totalStock = $product->colors->sum(function($pColor) {
            return $pColor->sizes->sum('stock');
        });
    @endphp

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-0" x-data="productForm()">
        <!-- Header Info -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
            <div class="flex flex-col">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Edit Product') }}: <span class="text-teal-600">{{ $product->name }}</span>
                </h2>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 py-1 bg-slate-100 rounded-full border border-slate-200">Total Stock: <span class="text-teal-600 ml-1">{{ $totalStock }}</span></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 py-1 bg-slate-100 rounded-full border border-slate-200">Style: <span class="text-slate-800 ml-1">{{ $product->sku }}</span></span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-50 text-slate-600 rounded-lg text-sm font-bold border border-slate-200 hover:bg-slate-100 hover:text-slate-900 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to products
                </a>
            </div>
        </div>
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 px-4 sm:px-0">
                <!-- Left Column: Basic Details -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Basic Information
                        </h3>
                        
                        <div class="space-y-5">
                            <!-- Category -->
                            <div>
                                <x-input-label for="category_id" value="Category" class="text-slate-600 font-semibold" />
                                <select id="category_id" name="category_id" x-model="selectedCategory" class="mt-1 block w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-all" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <!-- SubCategory (Dependent) -->
                            <div x-show="availableSubcategories.length > 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                <x-input-label for="subcategory_id" value="SubCategory" class="text-slate-600 font-semibold" />
                                <select id="subcategory_id" name="subcategory_id" x-model="selectedSubCategory" class="mt-1 block w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-all">
                                    <option value="">Select SubCategory</option>
                                    <template x-for="sub in availableSubcategories" :key="sub.id">
                                        <option :value="sub.id" x-text="sub.name" :selected="sub.id == selectedSubCategory"></option>
                                    </template>
                                </select>
                                <x-input-error :messages="$errors->get('subcategory_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="name" value="Product Name" class="text-slate-600 font-semibold" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg" value="{{ old('name', $product->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="type" value="Type" class="text-slate-600 font-semibold" />
                                <x-text-input id="type" name="type" type="text" class="mt-1 block w-full border-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg" value="{{ old('type', $product->type) }}" placeholder="e.g. Ready Made, Fabric" />
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="selling_price" value="Selling Price (৳)" class="text-slate-600 font-semibold" />
                                <div class="relative mt-1 rounded-md shadow-sm group">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-slate-500 sm:text-sm group-focus-within:text-teal-600">৳</span>
                                    </div>
                                    <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" class="block w-full rounded-lg border-slate-300 pl-8 focus:border-teal-500 focus:ring-teal-500 transition-all" value="{{ old('selling_price', $product->selling_price) }}" required />
                                </div>
                                <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="costing_price" value="Costing Price (৳)" class="text-slate-600 font-semibold" />
                                <div class="relative mt-1 rounded-md shadow-sm group">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-slate-500 sm:text-sm group-focus-within:text-teal-600">৳</span>
                                    </div>
                                    <x-text-input id="costing_price" name="costing_price" type="number" step="0.01" class="block w-full rounded-lg border-slate-300 pl-8 focus:border-teal-500 focus:ring-teal-500 transition-all" value="{{ old('costing_price', $product->costing_price) }}" />
                                </div>
                                <x-input-error :messages="$errors->get('costing_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="description" value="Description" class="text-slate-600 font-semibold" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-all placeholder-slate-400">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Variants -->
                <div class="lg:col-span-2">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    Product Variants
                                </h3>
                                <p class="text-sm text-slate-500">Manage colors, sizes, and stock availability.</p>
                            </div>
                            <button type="button" @click="addVariant()" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all shadow-md active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Color Variant
                            </button>
                        </div>

                        <template x-for="(variant, vIndex) in variants" :key="variant.id">
                            <div class="mb-6 p-5 border border-slate-200 rounded-xl bg-slate-50 relative group transition-all hover:border-teal-300 hover:shadow-sm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                <button type="button" @click="removeVariant(vIndex)" class="absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-all p-2 rounded-full hover:bg-red-50" title="Remove Variant">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>

                                <div class="flex flex-col md:flex-row md:items-end gap-6 mb-5 pr-10">
                                    <div class="flex-1 max-w-sm">
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Variant Color</label>
                                        <select :name="`variants[${vIndex}][color_id]`" x-model="variant.color_id" class="block w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-all" required>
                                            <option value="">Select Color</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-full md:w-48">
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Box Number</label>
                                        <input type="text" :name="`variants[${vIndex}][box_number]`" x-model="variant.box_number" class="block w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-all" placeholder="e.g. B-01">
                                    </div>
                                    <div class="pb-2">
                                        <span class="text-xs font-bold text-slate-500 bg-slate-200 px-3 py-1.5 rounded-full uppercase tracking-tighter">
                                            Color Stock: <span class="text-teal-700" x-text="calculateColorStock(vIndex)"></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-bold text-slate-700 flex items-center uppercase tracking-tight">
                                            <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                            Sizes & Stock
                                        </h4>
                                        <button type="button" @click="addSize(vIndex)" class="text-xs text-teal-600 hover:text-teal-800 font-bold flex items-center hover:bg-teal-50 px-3 py-1.5 rounded-full transition-all border border-teal-100">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Add Size
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(sizeRow, sIndex) in variant.sizes" :key="sizeRow.id">
                                            <div class="flex items-center gap-3">
                                                <div class="w-1/3">
                                                    <select :name="`variants[${vIndex}][sizes][${sIndex}][size_id]`" x-model="sizeRow.size_id" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-all" required>
                                                        <option value="">Select Size</option>
                                                        @foreach($sizes as $size)
                                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="w-1/3 flex items-center gap-2">
                                                    <div class="flex-1 relative">
                                                        <input type="number" :name="`variants[${vIndex}][sizes][${sIndex}][stock]`" x-model="sizeRow.stock" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-all" placeholder="Stock Qty" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="w-1/3 flex items-center justify-between gap-2">
                                                    <div class="w-16 relative">
                                                        <input type="text" :name="`variants[${vIndex}][sizes][${sIndex}][box_number]`" x-model="sizeRow.box_number" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-all text-xs" placeholder="Box #">
                                                    </div>
                                                    <div class="flex flex-col items-center">
                                                        <span class="text-[8px] font-black text-slate-400 uppercase leading-none mb-1">Sold</span>
                                                        <input type="number" :name="`variants[${vIndex}][sizes][${sIndex}][sold]`" x-model="sizeRow.sold" class="block w-16 rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-all text-xs text-center" placeholder="0" min="0">
                                                    </div>
                                                    <button type="button" @click="removeSize(vIndex, sIndex)" class="text-slate-300 hover:text-red-500 p-2 transition-colors rounded-full hover:bg-red-50" title="Remove Size">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="variants.length === 0" class="text-center py-16 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl">
                            <h3 class="text-base font-bold text-slate-900 uppercase tracking-wider">No variants defined</h3>
                            <button type="button" @click="addVariant()" class="mt-4 inline-flex items-center px-6 py-3 bg-teal-600 text-white font-bold rounded-xl shadow-lg hover:bg-teal-700 transition-all active:scale-95">
                                Add Your First Variant
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sticky xl:fixed lg:fixed fixed md:sticky sm:fixed w-full xl:w-7xl lg:w-7xl  bottom-0 right-0 left-0 bg-white border-t border-slate-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] px-6 py-4 flex justify-end gap-4 z-50">
                <a href="{{ route('products.index') }}" class="px-8 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold hover:bg-slate-100 transition-all shadow-sm active:scale-95">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-black uppercase tracking-widest shadow-xl shadow-teal-500/20 transition-all transform hover:-translate-y-1 active:translate-y-0 active:scale-95">
                    Update Product
                </button>
            </div>
        </form>
        <div class="h-24"></div>

    <script>
        function productForm() {
            return {
                allCategories: @json($categories),
                selectedCategory: {{ old('category_id', $product->category_id) }},
                selectedSubCategory: {{ old('subcategory_id', $product->subcategory_id ?? "''") }},
                variants: @json($variants),

                get availableSubcategories() {
                    if (!this.selectedCategory) return [];
                    const category = this.allCategories.find(c => c.id == this.selectedCategory);
                    return category ? category.subcategories : [];
                },
                calculateColorStock(vIndex) {
                    return this.variants[vIndex].sizes.reduce((sum, size) => sum + parseInt(size.stock || 0), 0);
                },
                addVariant() {
                    this.variants.push({
                        id: Date.now(),
                        color_id: '',
                        box_number: '',
                        sizes: [
                            { id: Date.now() + 1, size_id: '', stock: 0, sold: 0, box_number: '', sku: '' }
                        ]
                    });
                },
                removeVariant(index) {
                    this.variants.splice(index, 1);
                },
                addSize(vIndex) {
                    this.variants[vIndex].sizes.push({
                        id: Date.now(),
                        size_id: '',
                        stock: 0,
                        sold: 0,
                        box_number: '',
                        sku: ''
                    });
                },
                removeSize(vIndex, sIndex) {
                    this.variants[vIndex].sizes.splice(sIndex, 1);
                }
            }
        }
    </script>
</x-app-layout>
