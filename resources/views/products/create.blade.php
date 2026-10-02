<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Add New Product') }}
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-teal-600 hover:text-teal-800">
                &larr; Back to Products
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6" x-data="productForm()">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            
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
                                        <option :value="sub.id" x-text="sub.name"></option>
                                    </template>
                                </select>
                                <x-input-error :messages="$errors->get('subcategory_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="name" value="Product Name" class="text-slate-600 font-semibold" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg" placeholder="e.g. Cotton T-Shirt" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="type" value="Type" class="text-slate-600 font-semibold" />
                                <x-text-input id="type" name="type" type="text" class="mt-1 block w-full border-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg" placeholder="e.g. Ready Made, Fabric" />
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="selling_price" value="Selling Price (৳)" class="text-slate-600 font-semibold" />
                                <div class="relative mt-1 rounded-md shadow-sm group">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-slate-500 sm:text-sm group-focus-within:text-teal-600">৳</span>
                                    </div>
                                    <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" class="block w-full rounded-lg border-slate-300 pl-8 focus:border-teal-500 focus:ring-teal-500 transition-all" placeholder="0.00" required />
                                </div>
                                <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="costing_price" value="Costing Price (৳)" class="text-slate-600 font-semibold" />
                                <div class="relative mt-1 rounded-md shadow-sm group">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-slate-500 sm:text-sm group-focus-within:text-teal-600">৳</span>
                                    </div>
                                    <x-text-input id="costing_price" name="costing_price" type="number" step="0.01" class="block w-full rounded-lg border-slate-300 pl-8 focus:border-teal-500 focus:ring-teal-500 transition-all" placeholder="0.00" />
                                </div>
                                <x-input-error :messages="$errors->get('costing_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="description" value="Description" class="text-slate-600 font-semibold" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-all placeholder-slate-400" placeholder="Product details..."></textarea>
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
                                            <div class="flex items-center gap-3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                                                <div class="w-1/3">
                                                    <select :name="`variants[${vIndex}][sizes][${sIndex}][size_id]`" x-model="sizeRow.size_id" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-all" required>
                                                        <option value="">Select Size</option>
                                                        @foreach($sizes as $size)
                                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="w-1/3 relative">
                                                    <input type="number" :name="`variants[${vIndex}][sizes][${sIndex}][stock]`" x-model="sizeRow.stock" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-all" placeholder="Stock Qty" min="0" required>
                                                </div>
                                                <div class="w-1/3 relative">
                                                    <input type="text" :name="`variants[${vIndex}][sizes][${sIndex}][box_number]`" x-model="sizeRow.box_number" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-all" placeholder="Box #">
                                                </div>
                                                <button type="button" @click="removeSize(vIndex, sIndex)" class="text-slate-300 hover:text-red-500 p-2 transition-colors rounded-full hover:bg-red-50" title="Remove Size">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="variants.length === 0" class="text-center py-16 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl transition-all">
                            <div class="bg-white w-16 h-16 rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                <svg class="h-8 w-8 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 uppercase tracking-wider">No variants defined</h3>
                            <p class="mt-1 text-sm text-slate-500">Add at least one color and size variant to save the product.</p>
                            <div class="mt-8">
                                <button type="button" @click="addVariant()" class="inline-flex items-center px-6 py-3 border border-transparent shadow-lg text-sm font-bold rounded-xl text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all active:scale-95">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Your First Variant
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex justify-end gap-4 sticky bottom-6 bg-slate-900/10 backdrop-blur-md p-6 rounded-2xl border border-white/50 shadow-2xl z-20 lg:bg-transparent lg:backdrop-blur-none lg:shadow-none lg:p-0 lg:static">
                <a href="{{ route('products.index') }}" class="px-8 py-3.5 bg-white border border-slate-200 rounded-xl text-slate-700 font-bold hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-black uppercase tracking-widest shadow-xl shadow-teal-500/20 transition-all transform hover:-translate-y-1 active:translate-y-0 active:scale-95">
                    Save Product
                </button>
            </div>
        </form>
    </div>

    <script>
        function productForm() {
            return {
                allCategories: @json($categories),
                selectedCategory: '',
                selectedSubCategory: '',
                variants: [
                    {
                        id: Date.now(),
                        color_id: '',
                        box_number: '',
                        sizes: [
                            { id: Date.now() + 1, size_id: '', stock: 0, box_number: '' }
                        ]
                    }
                ],
                get availableSubcategories() {
                    if (!this.selectedCategory) return [];
                    const category = this.allCategories.find(c => c.id == this.selectedCategory);
                    return category ? category.subcategories : [];
                },
                addVariant() {
                    this.variants.push({
                        id: Date.now(),
                        color_id: '',
                        box_number: '',
                        sizes: [
                            { id: Date.now() + 1, size_id: '', stock: 0, box_number: '' }
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
                        box_number: ''
                    });
                },
                removeSize(vIndex, sIndex) {
                    this.variants[vIndex].sizes.splice(sIndex, 1);
                }
            }
        }
    </script>
</x-app-layout>
