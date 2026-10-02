<x-pos-layout>
    @php
        $jsProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->selling_price,
                'variants' => $product->colors->flatMap(function($pColor) use ($product) {
                    return $pColor->sizes->map(function($pSize) use ($pColor, $product) {
                        return [
                            'id' => $pSize->id,
                            'product_id' => $product->id,
                            'product_sku' => $product->sku,
                            'full_name' => $product->name . ' - ' . $pColor->color->name . ' - ' . $pSize->size->name,
                            'sku' => $pSize->sku,
                            'stock' => $pSize->stock,
                            'price' => ($pSize->price > 0) ? $pSize->price : $product->selling_price
                        ];
                    });
                })->values()
            ];
        });
    @endphp

    <div class="h-full overflow-y-auto font-sans p-4" x-data="exchangeApp()">
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Instant Exchange</h1>
                    <p class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] mt-1">Manual Entry Mode</p>
                </div>
                <a href="{{ route('pos.index') }}" class="px-6 py-3 rounded-2xl border-2 border-gray-100 font-black text-xs text-gray-500 uppercase hover:bg-gray-100 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to POS
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Form Section -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Customer & Return Info -->
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 space-y-6">
                        <div class="flex items-center gap-4 mb-2">
                            <span class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-lg shadow-inner">1</span>
                            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Return Product Details</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Customer Mobile (Optional)</label>
                                <input type="text" x-model="customerPhone" placeholder="e.g. 017xxxxxxxx" 
                                    class="w-full text-base py-4 px-5 rounded-2xl border-2 border-gray-50 focus:border-orange-500 focus:ring-0 font-bold bg-gray-50/50 transition-all placeholder:text-gray-300">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Reason for Exchange</label>
                                <select x-model="exchangeReason" class="w-full text-base py-4 px-5 rounded-2xl border-2 border-gray-50 focus:border-orange-500 focus:ring-0 font-bold bg-gray-50/50 transition-all">
                                    <option value="Size mismatch">Size mismatch</option>
                                    <option value="Product defect">Product defect</option>
                                    <option value="Change of mind">Change of mind</option>
                                    <option value="Wrong item delivered">Wrong item delivered</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-50">
                            <div class="md:col-span-2 space-y-2 relative">
                                <label class="block text-[10px] font-black text-orange-600 uppercase tracking-widest px-1">What is returning?</label>
                                <div class="relative">
                                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-orange-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                                    <input type="text" x-model="returnQuery" @input.debounce.250ms="searchReturning()" @keydown.enter.prevent="pickFirstReturning()" @keydown.escape="returnSuggestions = []"
                                        placeholder="Scan / type product ID, SKU or name (e.g. 0001)" 
                                        class="w-full text-lg py-4 pl-14 pr-5 rounded-2xl border-2 border-gray-50 focus:border-orange-500 focus:ring-0 font-black bg-gray-50/50 transition-all placeholder:text-gray-300">
                                </div>

                                <!-- Returning Product Suggestions (fetched from catalog) -->
                                <div x-show="returnSuggestions.length > 0" class="absolute z-50 left-0 right-0 top-full mt-3 bg-white border-2 border-orange-50 rounded-[2rem] shadow-2xl max-h-80 overflow-y-auto p-3 space-y-2">
                                    <template x-for="v in returnSuggestions" :key="'ret-' + v.id">
                                        <button @click="selectReturning(v)" class="w-full text-left p-4 hover:bg-orange-50 rounded-2xl transition-all flex items-center justify-between group">
                                            <div class="min-w-0 flex-1 pr-4">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5 group-hover:text-orange-500" x-text="v.sku"></p>
                                                <p class="text-sm font-black text-gray-900 uppercase truncate" x-text="v.full_name"></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-base font-black text-orange-600" x-text="'৳' + v.price"></p>
                                                <p class="text-[9px] font-bold text-gray-400 uppercase" x-text="'Stock: ' + v.stock"></p>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                <input type="hidden" x-model="returnProductName">
                                <input type="hidden" x-model="returnProductSku">

                                <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest px-1" x-show="returningProduct">
                                    Fetched from catalog: <span x-text="returningProduct?.sku"></span>
                                </p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-orange-600 uppercase tracking-widest px-1">Returning Price</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-gray-400">৳</span>
                                    <input type="number" x-model="oldPrice" placeholder="0.00"
                                        class="w-full text-lg py-4 pl-10 pr-5 rounded-2xl border-2 border-gray-50 focus:border-orange-500 focus:ring-0 font-black bg-gray-50/50 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- New Product Selection -->
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <span class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-lg shadow-inner">2</span>
                                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Select New Product</h2>
                            </div>
                            
                            <!-- Same Product Button Helper -->
                            <button @click="showSameProductVariants()" :disabled="!returnProductName"
                                :class="returnProductName ? 'bg-blue-50 text-blue-700 border-blue-100 hover:bg-blue-600 hover:text-white' : 'bg-gray-50 text-gray-300 border-gray-100 opacity-50'"
                                class="px-6 py-3 rounded-2xl border-2 font-black text-xs uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Same Product Variants
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-3 relative">
                                <input type="text" x-model="newProductQuery" @input.debounce.300ms="searchCatalog()" 
                                    placeholder="Search by SKU or Name..." 
                                    class="w-full text-lg py-5 px-6 rounded-3xl border-2 border-gray-50 focus:border-blue-500 focus:ring-0 font-black bg-gray-50/50 shadow-inner placeholder:text-gray-300">
                                
                                <!-- Search Suggestions -->
                                <div x-show="suggestions.length > 0" class="absolute z-50 left-0 right-0 top-full mt-3 bg-white border-2 border-gray-50 rounded-[2rem] shadow-2xl max-h-80 overflow-y-auto p-3 space-y-2">
                                    <template x-for="v in suggestions" :key="v.sku">
                                        <button @click="selectNewProduct(v)" class="w-full text-left p-4 hover:bg-blue-50 rounded-2xl transition-all flex items-center justify-between group">
                                            <div class="min-w-0 flex-1 pr-4">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5 group-hover:text-blue-400" x-text="v.sku"></p>
                                                <p class="text-sm font-black text-gray-900 uppercase truncate" x-text="v.full_name"></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-base font-black text-blue-600" x-text="'৳' + v.price"></p>
                                                <p class="text-[9px] font-bold text-gray-400 uppercase" x-text="'Stock: ' + v.stock"></p>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-gray-400">৳</span>
                                    <input type="number" x-model="newPriceOverride" placeholder="Price"
                                        class="w-full text-lg py-5 pl-10 pr-5 rounded-3xl border-2 border-gray-50 focus:border-blue-500 focus:ring-0 font-black bg-gray-50/50 transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Variant Picker for Same Product -->
                        <div x-show="matchingVariants.length > 0" class="bg-blue-50/50 rounded-3xl p-6 border border-blue-100/50 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-[10px] font-black text-blue-700 uppercase tracking-widest">Available Variations</h3>
                                <button @click="matchingVariants = []" class="text-[10px] font-black text-blue-400 uppercase hover:text-blue-700">Clear</button>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <template x-for="v in matchingVariants" :key="v.sku">
                                    <button @click="selectNewProduct(v)" 
                                        :class="newProduct?.sku === v.sku ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-gray-800 border-gray-100 hover:bg-blue-100'"
                                        class="p-4 rounded-2xl border-2 transition-all text-center group">
                                        <p class="text-[9px] font-black uppercase opacity-60 mb-1" x-text="v.full_name.split(' - ')[1]"></p>
                                        <p class="text-xs font-black uppercase" x-text="v.full_name.split(' - ')[2]"></p>
                                        <p class="mt-2 text-[10px] font-black" :class="newProduct?.sku === v.sku ? 'text-blue-200' : 'text-blue-600'" x-text="'৳' + v.price"></p>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Selected New Product Display -->
                        <div x-show="newProduct" class="bg-gray-900 p-6 rounded-[2rem] shadow-xl flex justify-between items-center transform transition-all">
                            <div class="min-w-0 flex-1 pr-6">
                                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Exchange For</p>
                                <p class="text-xl font-black text-white uppercase truncate" x-text="newProduct?.full_name"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black text-blue-400" x-text="'৳' + (newPriceOverride || 0)"></p>
                                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mt-1">Confirmed Price</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary & Checkout Section -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 sticky top-8 space-y-8">
                        <div class="flex items-center gap-4 pb-6 border-b border-gray-50">
                            <span class="w-10 h-10 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center font-black text-lg shadow-inner">3</span>
                            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Final Summary</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Helper Options -->
                            <label class="flex items-center gap-4 cursor-pointer group bg-gray-50 p-4 rounded-2xl border-2 border-transparent hover:border-teal-500 transition-all">
                                <input type="checkbox" x-model="keepPriceSame" @change="handleKeepPriceSame()"
                                    class="w-6 h-6 rounded-lg border-gray-200 text-teal-600 focus:ring-teal-500 transition-all">
                                <div class="flex-1">
                                    <span class="block text-sm font-black text-gray-800 uppercase tracking-tight">KEEP SAME PRICE</span>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Force Item-to-Item balance</span>
                                </div>
                            </label>

                            <!-- Balance Visualization -->
                            <div class="bg-gray-50 rounded-[2rem] overflow-hidden">
                                <div class="p-6 text-center space-y-1">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Net Difference</span>
                                    <div class="text-4xl font-black tracking-tighter" :class="balanceDiff >= 0 ? 'text-orange-500' : 'text-green-500'" 
                                        x-text="'৳' + Math.abs(balanceDiff)"></div>
                                    <p class="text-[10px] font-black uppercase tracking-widest" :class="balanceDiff >= 0 ? 'text-orange-400' : 'text-green-400'"
                                        x-text="balanceDiff >= 0 ? 'CUSTOMER PAYS' : 'REFUND DUE'"></p>
                                </div>
                                <div class="grid grid-cols-2 bg-gray-100/50 text-center py-4 border-t border-gray-100">
                                    <div class="border-r border-gray-200">
                                        <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Returning</p>
                                        <p class="text-base font-black text-gray-600" x-text="'৳' + (oldPrice || 0)"></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-gray-400 uppercase mb-1">New Item</p>
                                        <p class="text-base font-black text-gray-600" x-text="'৳' + (newPriceOverride || 0)"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Inputs -->
                            <div class="space-y-4" x-show="balanceDiff > 0">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Customer Paid Cash</label>
                                    <input type="number" x-model="customerPaid" 
                                        class="w-full text-2xl py-5 px-6 rounded-3xl border-2 border-gray-50 focus:border-teal-500 focus:ring-0 font-black text-gray-900 shadow-inner bg-gray-50/50">
                                </div>
                                <div class="space-y-2" x-show="customerPaid > balanceDiff">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1 text-right">Change Return</label>
                                    <div class="w-full py-4 px-6 bg-teal-50 border-2 border-teal-100 rounded-2xl font-black text-2xl text-teal-700 flex items-center justify-end shadow-sm">
                                        <span x-text="'৳' + (customerPaid - balanceDiff).toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>

                            <button @click="submitExchange()" :disabled="!isExchangeValid || isLoading"
                                class="w-full bg-teal-600 text-white py-6 rounded-3xl font-black text-xl uppercase shadow-2xl shadow-teal-200 hover:bg-teal-700 hover:-translate-y-1 active:scale-95 transition-all disabled:opacity-30 disabled:translate-y-0 flex items-center justify-center gap-4">
                                <span x-show="!isLoading">CONFIRM & PRINT</span>
                                <span x-show="isLoading" class="flex items-center gap-2">
                                     <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exchangeApp() {
            return {
                products: @json($jsProducts),
                customerPhone: '',
                returnQuery: '',
                returnProductName: '',
                returnProductSku: null,
                returningProduct: null,
                returningVariants: [],
                returnSuggestions: [],
                oldPrice: 0,
                newPriceOverride: 0,
                newProductQuery: '',
                suggestions: [],
                newProduct: null,
                isLoading: false,
                exchangeReason: 'Size mismatch',
                customerPaid: 0,
                keepPriceSame: false,
                matchingVariants: [],

                searchCatalog() {
                    const q = this.newProductQuery.toLowerCase();
                    // Allow manual entry logic
                    this.suggestions = [];

                    if (q.length > 0) {
                         // Always offer manual option first
                         this.suggestions.push({
                            sku: 'MANUAL',
                            full_name: `Use "${this.newProductQuery}" as Manual Product`,
                            price: 0,
                            stock: 'N/A',
                            isManual: true,
                            inputName: this.newProductQuery
                         });
                    }

                    if (q.length < 2) return;

                    this.products.forEach(p => {
                        p.variants.forEach(v => {
                            if (v.sku.toLowerCase().includes(q) || v.full_name.toLowerCase().includes(q)) {
                                this.suggestions.push(v);
                            }
                        });
                    });
                    this.suggestions = this.suggestions.slice(0, 10);
                },

                /**
                 * Search the catalog for the returning item by product ID, SKU or name.
                 * Free typing still works as a manual product name.
                 */
                searchReturning() {
                    const raw = this.returnQuery.trim();

                    this.returnProductName = raw;
                    this.returnSuggestions = [];

                    if (raw.length === 0 || raw.length < 2) return;

                    const q = raw.toLowerCase();

                    // Exact product ID or product SKU -> every variant of that product
                    const byProduct = this.products.find(p =>
                        String(p.id) === raw || String(p.sku || '').toLowerCase() === q
                    );

                    if (byProduct) {
                        this.returnSuggestions = byProduct.variants.slice(0, 10);

                        if (this.returnSuggestions.length === 1) {
                            this.selectReturning(this.returnSuggestions[0]);
                        }

                        return;
                    }

                    const hits = [];

                    this.products.forEach(p => {
                        const name = (p.name || '').toLowerCase();

                        p.variants.forEach(v => {
                            const sku = String(v.sku || '').toLowerCase();

                            if (sku === q) hits.push({ v: v, score: 0 });
                            else if (sku.startsWith(q)) hits.push({ v: v, score: 1 });
                            else if (sku.includes(q)) hits.push({ v: v, score: 2 });
                            else if (name.startsWith(q)) hits.push({ v: v, score: 3 });
                            else if (name.includes(q)) hits.push({ v: v, score: 4 });
                        });
                    });

                    hits.sort((a, b) => a.score - b.score);
                    this.returnSuggestions = hits.slice(0, 10).map(h => h.v);

                    // Auto fetch when the scanned SKU matches one variant exactly
                    const exact = this.returnSuggestions.find(v => String(v.sku).toLowerCase() === q);

                    if (exact) {
                        this.selectReturning(exact);
                    }
                },

                pickFirstReturning() {
                    if (this.returnSuggestions.length > 0) {
                        this.selectReturning(this.returnSuggestions[0]);
                    }
                },

                selectReturning(v) {
                    this.returningProduct = v;
                    this.returnQuery = v.full_name;
                    this.returnProductName = v.full_name;
                    this.returnProductSku = v.sku;
                    this.returnSuggestions = [];

                    // Fetch price + sibling variants automatically
                    this.oldPrice = v.price;

                    const product = this.products.find(p => p.id === v.product_id);
                    this.returningVariants = product ? product.variants : [];

                    if (this.keepPriceSame) {
                        this.newPriceOverride = this.oldPrice;
                    }
                },

                showSameProductVariants() {
                    if (this.returningProduct) {
                        this.matchingVariants = this.returningVariants;
                        this.scrollToVariants();

                        if (this.matchingVariants.length === 0) {
                            alert('This product has no variations in the catalog.');
                        }

                        return;
                    }

                    if (!this.returnProductName) return;

                    const query = this.returnProductName.toLowerCase();
                    this.matchingVariants = [];
                    this.products.forEach(p => {
                        if (p.name.toLowerCase().includes(query) || query.includes(p.name.toLowerCase())) {
                            this.matchingVariants.push(...p.variants);
                        }
                    });

                    if (this.matchingVariants.length === 0) {
                        alert('No matching products found in catalog for this name.');
                    } else {
                        this.scrollToVariants();
                    }
                },

                scrollToVariants() {
                    setTimeout(() => {
                        const el = document.querySelector('.bg-blue-50\\/50');
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                },

                selectNewProduct(v) {
                    if (v.isManual) {
                        this.newProduct = {
                            sku: null,
                            full_name: v.inputName, // Just the name
                            price: 0,
                            isManual: true
                        };
                        this.newPriceOverride = 0; 
                        // User enters price manually for manual items
                    } else {
                        this.newProduct = v;
                        this.newPriceOverride = v.price;
                    }
                    
                    if (this.keepPriceSame) {
                        this.newPriceOverride = this.oldPrice;
                    }
                    this.suggestions = [];
                },

                handleKeepPriceSame() {
                    if (this.keepPriceSame) {
                        this.newPriceOverride = this.oldPrice;
                    } else if (this.newProduct) {
                        this.newPriceOverride = this.newProduct.price;
                    }
                },

                get balanceDiff() {
                    return (parseFloat(this.newPriceOverride) || 0) - (parseFloat(this.oldPrice) || 0);
                },

                get isExchangeValid() {
                    // Allow 0 price, allow implicit manual product
                    const hasReturn = this.returnProductName && this.returnProductName.length > 0;
                    const hasOldPrice = this.oldPrice !== null && this.oldPrice !== '';
                    const hasNewProduct = this.newProduct || (this.newProductQuery && this.newProductQuery.length > 0);
                    
                    return hasReturn && hasOldPrice && hasNewProduct;
                },

                async submitExchange() {
                    if (!this.isExchangeValid) return;

                    // Handle implicit manual product
                    if (!this.newProduct && this.newProductQuery) {
                        this.newProduct = {
                            sku: null,
                            full_name: this.newProductQuery,
                            price: 0,
                            isManual: true
                        };
                        // Note: newPriceOverride stays as initialized (maybe 0) or user edited it? 
                        // Actually in this case user couldn't edit it because the input was hidden (x-show="newProduct").
                        // So if they implicitly submit, price is 0.
                        // We should probably confirm if they want 0 price if it's 0.
                    }

                    if (!confirm('Proceed with this exchange?')) return;
                    
                    this.isLoading = true;
                    try {
                        const payload = {
                            customer_phone: this.customerPhone,
                            returned_product_name: this.returnProductName,
                            returned_product_sku: this.returnProductSku,
                            old_price: this.oldPrice,
                            new_product_sku: this.newProduct.sku, // Null for manual
                            new_product_name: this.newProduct.full_name, // Name for manual
                            new_price: this.newPriceOverride,
                            reason: this.exchangeReason,
                            customer_pay: this.customerPaid,
                            change_amount: Math.max(0, this.customerPaid - this.balanceDiff)
                        };

                        const res = await fetch("{{ route('orders.exchange.process') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();
                        if (data.success) {
                            if (data.exchange_order_id) {
                                window.open(`/orders/${data.exchange_order_id}/invoice`, '_blank');
                            }
                            alert('Exchange Successful!');
                            window.location.href = "{{ route('pos.index') }}";
                        } else {
                            alert(data.message || 'Processing failed');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Error processing exchange');
                    } finally {
                        this.isLoading = false;
                    }
                }
            };
        }
    </script>
</x-pos-layout>
