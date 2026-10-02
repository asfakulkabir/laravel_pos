<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Attributes Management') }}
            </h2>
            <p class="text-sm text-slate-500">Configure categories, colors, and sizes for your catalog.</p>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ activeTab: 'categories' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
            
            <!-- Modern Tabs -->
            <div class="mb-8 border-b border-slate-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'categories'" 
                        :class="activeTab === 'categories' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-all focus:outline-none">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            Product Categories
                        </span>
                    </button>
                    <button @click="activeTab = 'colors'" 
                        :class="activeTab === 'colors' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-all focus:outline-none">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.157a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                            Available Colors
                        </span>
                    </button>
                    <button @click="activeTab = 'sizes'" 
                        :class="activeTab === 'sizes' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-all focus:outline-none">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            Size Variants
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Categories Tab Content -->
            <div x-show="activeTab === 'categories'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0 text-slate-900 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Management Forms -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 overflow-hidden">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-slate-800 flex items-center">
                                    <span class="bg-teal-100 text-teal-600 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </span>
                                    Add New Category
                                </h3>
                                <form action="{{ route('attributes.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="category">
                                    <input type="file" name="file" id="import_category" class="hidden" onchange="this.form.submit()">
                                    <button type="button" onclick="document.getElementById('import_category').click()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center shadow-sm border border-slate-200">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        Import JSON
                                    </button>
                                </form>
                            </div>
                            <form action="{{ route('attributes.category.store') }}" method="POST">
                                @csrf
                                <div class="mb-5">
                                    <x-input-label for="cat_name" value="Category Name" class="text-slate-600" />
                                    <x-text-input id="cat_name" name="name" type="text" class="mt-1 block w-full border-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg" placeholder="e.g. Menswear, Footwear..." required />
                                </div>
                                <x-primary-button class="bg-teal-600 hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-800">Save Category</x-primary-button>
                            </form>
                            
                            <hr class="my-8 border-slate-100">

                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-slate-800 flex items-center">
                                    <span class="bg-teal-100 text-teal-600 p-2 rounded-lg mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    </span>
                                    Add SubCategory
                                </h3>
                                <form action="{{ route('attributes.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="subcategory">
                                    <input type="file" name="file" id="import_subcategory" class="hidden" onchange="this.form.submit()">
                                    <button type="button" onclick="document.getElementById('import_subcategory').click()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center shadow-sm border border-slate-200">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        Import JSON
                                    </button>
                                </form>
                            </div>
                            <form action="{{ route('attributes.subcategory.store') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <x-input-label for="parent_id" value="Parent Category" class="text-slate-600" />
                                    <select id="parent_id" name="category_id" class="mt-1 block w-full border-slate-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                                        <option value="">Choose parent...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <x-input-label for="sub_name" value="SubCategory Name" class="text-slate-600" />
                                    <x-text-input id="sub_name" name="name" type="text" class="mt-1 block w-full border-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg" placeholder="e.g. Formal Shirts, T-Shirts..." required />
                                </div>
                                <x-primary-button class="bg-teal-600 hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-800">Add Sub-category</x-primary-button>
                            </form>
                        </div>
                    </div>

                    <!-- Category List -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-lg font-bold text-slate-800">Catalog Tree</h3>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-6">
                                @foreach($categories as $category)
                                    <li class="p-4 rounded-xl border border-slate-100 hover:border-teal-200 transition-colors group">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-bold text-slate-800 flex items-center capitalize">
                                                <svg class="w-4 h-4 mr-2 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                                                {{ $category->name }}
                                            </span>
                                            <form action="{{ route('attributes.category.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete category? This will affect products in this category.');">
                                                @csrf @method('DELETE')
                                                <button class="text-slate-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                        @if($category->subcategories->count() > 0)
                                            <ul class="ml-6 mt-3 space-y-2 border-l-2 border-slate-100 pl-4">
                                                @foreach($category->subcategories as $sub)
                                                    <li class="flex justify-between items-center group/sub">
                                                        <span class="text-sm text-slate-600">{{ $sub->name }}</span>
                                                        <form action="{{ route('attributes.subcategory.destroy', $sub) }}" method="POST" onsubmit="return confirm('Delete subcategory?');">
                                                            @csrf @method('DELETE')
                                                            <button class="opacity-0 group-hover/sub:opacity-100 text-red-300 hover:text-red-500 transition-all">
                                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-xs text-slate-400 ml-6 italic">No subcategories</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colors Tab -->
            <div x-show="activeTab === 'colors'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-fit">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center">
                                <span class="bg-teal-100 text-teal-600 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </span>
                                Register New Color
                            </h3>
                            <form action="{{ route('attributes.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="color">
                                <input type="file" name="file" id="import_color" class="hidden" onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('import_color').click()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center shadow-sm border border-slate-200">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Import JSON
                                </button>
                            </form>
                        </div>
                        <form action="{{ route('attributes.color.store') }}" method="POST">
                            @csrf
                            <div class="mb-6">
                                <x-input-label for="color_name" value="Color Name" class="text-slate-600" />
                                <x-text-input id="color_name" name="name" type="text" class="mt-1 block w-full border-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg" placeholder="e.g. Royal Blue, Emerald Green..." required />
                                <p class="mt-2 text-xs text-slate-400">Enter a descriptive name for the product color variant.</p>
                            </div>
                            <x-primary-button class="bg-teal-600 hover:bg-teal-700 focus:bg-teal-700">Save Color</x-primary-button>
                        </form>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-lg font-bold text-slate-800">Color Palette</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($colors as $color)
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:bg-teal-50/30 transition group">
                                        <span class="text-sm font-medium text-slate-700 flex items-center">
                                            <span class="w-3 h-3 rounded-full mr-3 border border-slate-200 shadow-sm" style="background-color: {{ strtolower($color->name) }}"></span>
                                            {{ $color->name }}
                                        </span>
                                        <form action="{{ route('attributes.color.destroy', $color) }}" method="POST" onsubmit="return confirm('Delete color?');">
                                            @csrf @method('DELETE')
                                            <button class="text-slate-300 hover:text-red-500 transition-all opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sizes Tab -->
            <div x-show="activeTab === 'sizes'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-fit">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center">
                                <span class="bg-teal-100 text-teal-600 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </span>
                                Register New Size
                            </h3>
                            <form action="{{ route('attributes.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="size">
                                <input type="file" name="file" id="import_size" class="hidden" onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('import_size').click()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg font-bold transition-all flex items-center shadow-sm border border-slate-200">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Import JSON
                                </button>
                            </form>
                        </div>
                        <form action="{{ route('attributes.size.store') }}" method="POST">
                            @csrf
                            <div class="mb-6">
                                <x-input-label for="size_name" value="Size Identifier" class="text-slate-600" />
                                <x-text-input id="size_name" name="name" type="text" class="mt-1 block w-full border-slate-300 focus:border-teal-500 focus:ring-teal-500 rounded-lg" placeholder="e.g. XL, 42, 10.5..." required />
                                <p class="mt-2 text-xs text-slate-400">Enter a label that appears on invoices and product listings.</p>
                            </div>
                            <x-primary-button class="bg-teal-600 hover:bg-teal-700 focus:bg-teal-700">Save Size</x-primary-button>
                        </form>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-lg font-bold text-slate-800">Size Inventory</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-3">
                                @foreach($sizes as $size)
                                    <div class="inline-flex items-center bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 group hover:border-teal-300 transition-all">
                                        <span class="text-sm font-bold text-slate-700 mr-4">{{ $size->name }}</span>
                                        <form action="{{ route('attributes.size.destroy', $size) }}" method="POST" onsubmit="return confirm('Delete size variation?');">
                                            @csrf @method('DELETE')
                                            <button class="text-slate-300 hover:text-red-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

