<x-pos-layout>
    @php
        $jsProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->selling_price,
                'image_url' => 'https://ui-avatars.com/api/?name=' . urlencode($product->name) . '&background=f3f4f6&color=4b5563&size=200',
                'variants' => $product->colors->flatMap(function($pColor) use ($product) {
                    return $pColor->sizes->map(function($pSize) use ($pColor, $product) {
                        return [
                            'id' => $pSize->id,
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

    <div class="h-full flex flex-col bg-white/50 backdrop-blur-sm font-sans p-2" x-data="posApp()">
        
        <!-- 1. TOP ENTRY BAR (One line) -->
        <div class="bg-white/80 border-b border-gray-800 px-6 py-5 shadow-sm z-30">
            <div class="max-w-[1900px] mx-auto grid grid-cols-12 gap-5 items-center">
                <!-- Product Search (3 cols) -->
                <div class="col-span-3 relative">
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">Search Product</label>
                    <input type="text" x-model="searchQuery" @keydown.enter.prevent="handleSkuScan()"
                        placeholder="Scan SKU / Search..." 
                        class="w-full text-base py-2 px-3 rounded-lg border-2 border-gray-800 focus:ring-2 focus:ring-teal-500 focus:border-gray-900 font-bold bg-white text-gray-900 placeholder-gray-500">
                    <!-- Product Suggestions -->
                    <div x-show="searchQuery.length > 1 && filteredProducts.length > 0" 
                         class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border-2 border-gray-800 rounded-lg shadow-xl max-h-64 overflow-y-auto">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <button @click="openProductModal(product)" class="w-full text-left p-3 hover:bg-teal-50 border-b border-gray-100 flex items-center justify-between transition-colors">
                                <span class="text-sm font-bold text-gray-900" x-text="product.name"></span>
                                <span class="text-xs text-gray-600 font-mono bg-gray-100 px-1 py-0.5 rounded" x-text="product.sku"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Phone (2 cols) -->
                <div class="col-span-2 relative">
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">Customer Phone</label>
                    <input type="text" x-model="customerPhone" @input.debounce.300ms="fetchCustomers" 
                        placeholder="Phone Number" 
                        class="w-full text-base py-2 px-3 rounded-lg border-2 border-gray-800 focus:ring-2 focus:ring-teal-500 focus:border-gray-900 font-bold bg-white text-gray-900 placeholder-gray-500">
                    <div x-show="suggestions.length > 0" @click.away="suggestions = []" 
                         class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border-2 border-gray-800 rounded-lg shadow-xl max-h-48 overflow-y-auto">
                        <template x-for="customer in suggestions" :key="customer.id">
                            <button @click="selectCustomer(customer)" class="w-full text-left p-3 hover:bg-teal-50 border-b border-gray-100 transition-colors">
                                <div class="text-sm font-black text-gray-900" x-text="customer.name"></div>
                                <div class="text-xs text-gray-600" x-text="customer.phone"></div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Name (2 cols) -->
                <div class="col-span-2">
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">Customer Name</label>
                    <input type="text" x-model="customerName" placeholder="Name" 
                        class="w-full text-base py-2 px-3 rounded-lg border-2 border-gray-800 focus:ring-2 focus:ring-teal-500 focus:border-gray-900 font-bold bg-white text-gray-900 placeholder-gray-500">
                </div>

                <!-- Address (3 cols) -->
                <div class="col-span-3">
                    <label class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1">Customer Address</label>
                    <input type="text" x-model="customerAddress" placeholder="Address" 
                        class="w-full text-base py-2 px-3 rounded-lg border-2 border-gray-800 focus:ring-2 focus:ring-teal-500 focus:border-gray-900 font-bold bg-white text-gray-900 placeholder-gray-500">
                </div>

                <!-- Sell By (1 col) -->
                <div class="col-span-1">
                    <label class="block text-xs font-black text-teal-800 uppercase tracking-wider mb-1">Sell By</label>
                    <input type="text" value="{{ auth()->user()->name }}" readonly 
                        class="w-full text-base py-2 px-3 rounded-lg border-2 border-teal-600 bg-teal-50 text-teal-900 font-black shadow-sm cursor-not-allowed">
                </div>

                <!-- Exchange Button (1 col) -->
                <div class="col-span-1">
                    <label class="block text-xs font-black text-orange-800 uppercase tracking-wider mb-1">Actions</label>
                    <a href="{{ route('pos.exchange') }}" 
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-3 rounded-lg font-black text-sm shadow-md transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        EXCHANGE
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN LAYOUT (2/3 & 1/3) -->
        <div class="flex-1 flex overflow-hidden">
            
            <!-- LEFT 2/3: CART LIST WITH INDIVIDUAL DISCOUNTS -->
            <div class="w-2/3 flex flex-col bg-sky-50 border-r border-gray-300">
                <div class="p-4 bg-gray-50/80 border-b border-gray-300 flex justify-between items-center">
                    <h3 class="text-sm font-black uppercase text-gray-700 tracking-widest">Added Products</h3>
                    <span class="text-xs font-bold text-gray-600" x-text="cart.length + ' Items'"></span>
                </div>
                
                <div class="flex-1 overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100/90 sticky top-0 z-10 border-b border-gray-200">
                            <tr>
                                <th class="p-4 text-xs font-black uppercase text-gray-800">Product Detail</th>
                                <th class="p-4 text-xs font-black uppercase text-gray-800 w-24">Price</th>
                                <th class="p-4 text-xs font-black uppercase text-gray-800 w-32">Quantity</th>
                                <th class="p-4 text-xs font-black uppercase text-gray-800 w-48">Discount</th>
                                <th class="p-4 text-xs font-black uppercase text-gray-800 w-32 text-right">Total</th>
                                <th class="p-4 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, index) in cart" :key="item.variation_id">
                                <tr class="hover:bg-teal-50/50 transition-colors">
                                    <td class="p-4">
                                        <p class="text-sm font-bold text-gray-900" x-text="item.name"></p>
                                        <p class="text-[11px] text-gray-600 font-mono mt-0.5" x-text="item.sku"></p>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-sm font-bold text-gray-900" x-text="'৳' + item.price"></span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center border border-gray-400 rounded-lg bg-white overflow-hidden w-24">
                                            <button @click="updateQty(index, -1)" class="px-2 py-1 hover:bg-gray-100 text-gray-700 font-bold">-</button>
                                            <input type="number" x-model="item.qty" @input="validateQty(index)" class="w-full text-center text-sm font-bold border-none p-0 focus:ring-0 text-gray-900">
                                            <button @click="updateQty(index, 1)" class="px-2 py-1 hover:bg-gray-100 text-gray-700 font-bold">+</button>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1">
                                            <input type="number" x-model="item.discountValue" 
                                                class="w-20 text-sm py-1 px-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-teal-500 font-bold text-gray-900" placeholder="0">
                                            <select x-model="item.discountType" class="text-[10px] font-black border-none bg-gray-100 rounded p-1 focus:ring-0 text-gray-900">
                                                <option value="flat">৳ Flat rate</option>
                                                <option value="percent">% Parcentage</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="p-4 text-right">
                                        <span class="text-sm font-black text-gray-900" x-text="'৳' + calculateItemTotal(item).toFixed(0)"></span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button @click="removeFromCart(index)" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="cart.length === 0">
                                <td colspan="6" class="p-20 text-center text-gray-500 italic text-sm uppercase tracking-widest font-bold">
                                    No products added to the list
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT 1/3: PRICE SUMMARY AND PAYMENT -->
            <div class="w-1/3 flex flex-col bg-white/95 shadow-xl border-l border-gray-200">
                <!-- Header -->
                <div class="p-3 border-b border-gray-200 bg-white flex-shrink-0">
                    <h3 class="text-sm font-black uppercase text-gray-600 tracking-widest">Pricing Summary</h3>
                </div>

                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-3 space-y-3">
                    <!-- Global Discount Section -->
                    <div class="bg-teal-50 p-3 rounded-xl border border-teal-200 space-y-2">
                        <p class="text-xs font-black text-teal-800 uppercase tracking-widest mb-1">Order Discount</p>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 relative">
                                <label class="absolute -top-1.5 left-2 px-1 bg-teal-50 text-[9px] font-bold text-teal-600 uppercase">Flat (৳)</label>
                                <input type="number" x-model="globalFlat" @input="globalPercent = ''"
                                    class="w-full bg-white border border-teal-200 rounded-lg py-1.5 px-2 text-base font-black text-teal-800 focus:ring-1 focus:ring-teal-500/50 focus:border-teal-500 transition-all outline-none" 
                                    placeholder="0">
                            </div>
                            <div class="flex-1 relative">
                                <label class="absolute -top-1.5 left-2 px-1 bg-teal-50 text-[9px] font-bold text-teal-600 uppercase">Percent (%)</label>
                                <input type="number" x-model="globalPercent" @input="globalFlat = ''"
                                    class="w-full bg-white border border-teal-200 rounded-lg py-1.5 px-2 text-base font-black text-teal-800 focus:ring-1 focus:ring-teal-500/50 focus:border-teal-500 transition-all outline-none" 
                                    placeholder="0">
                            </div>
                        </div>
                    </div>

                    <!-- Financial Breakdown -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs font-bold text-gray-600 uppercase">
                            <span>Product Subtotal</span>
                            <span class="text-gray-900 font-black text-sm" x-text="'৳' + cartSubtotal.toFixed(0)"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-gray-600 uppercase">
                            <span>Item Discounts</span>
                            <span class="text-red-500 font-black text-sm" x-text="'-৳' + totalItemDiscount.toFixed(0)"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-gray-600 uppercase" x-show="calculatedGlobalDiscount > 0">
                            <span>Order Discount</span>
                            <span class="text-red-500 font-black text-sm" x-text="'-৳' + calculatedGlobalDiscount.toFixed(0)"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-gray-600 uppercase">
                            <div class="flex items-center gap-1">
                                <span>VAT</span>
                                <input type="number" x-model="vatPercent" 
                                    class="w-12 h-6 text-right px-1 py-0 text-xs font-black border border-gray-300 rounded focus:ring-1 focus:ring-teal-500" 
                                    placeholder="0">%
                            </div>
                            <span class="text-gray-800 font-black text-sm" x-text="'+৳' + vatAmount.toFixed(0)"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-gray-600 uppercase" x-show="currentExchangeAmount !== 0">
                            <span>Exchange Price</span>
                            <span :class="currentExchangeAmount > 0 ? 'text-green-600' : 'text-red-500'" 
                                class="font-black text-sm" 
                                x-text="(currentExchangeAmount > 0 ? '+' : '') + '৳' + currentExchangeAmount.toFixed(0)"></span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 flex justify-between items-end">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest leading-none mb-1">Final Amount</span>
                                <span class="text-2xl font-black text-gray-900 tracking-tighter leading-none" x-text="'৳' + cartTotal.toFixed(0)"></span>
                            </div>
                        </div>

                        <!-- Customer Paid & Change -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Customer Paid</label>
                                <div class="relative">
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">৳</span>
                                    <input type="number" x-model="customerPaid" 
                                        class="w-full pl-5 pr-2 py-1.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-teal-500 focus:border-teal-500 font-black text-gray-900 text-base shadow-sm"
                                        placeholder="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-teal-700 uppercase tracking-widest mb-1">Change Return</label>
                                <div class="w-full bg-teal-50 border border-teal-200 rounded-lg py-1.5 px-2 text-right shadow-sm h-[38px] flex items-center justify-end">
                                    <span class="text-lg font-black text-teal-700" x-text="'৳' + changeAmount.toFixed(0)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="space-y-2 border-t border-gray-200 pt-2">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Payment Method</p>
                        <div class="grid grid-cols-4 gap-1.5">
                            <template x-for="method in ['Cash', 'Bkash', 'Nagad', 'Card']" :key="method">
                                <button @click="paymentMethod = method" 
                                    :class="paymentMethod === method ? 'bg-teal-700 text-white border-teal-700 shadow-md transform scale-[1.02]' : 'bg-white text-gray-600 border-gray-300 hover:border-teal-400 hover:text-teal-700'" 
                                    class="py-2 px-1 rounded-lg border font-black text-[11px] uppercase transition-all flex items-center justify-center">
                                    <span x-text="method"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Final Action (Fixed Footer) -->
                <div class="p-3 bg-white border-t border-gray-200 flex-shrink-0">
                    <button @click="submitOrder()" 
                        :disabled="cart.length === 0 || isLoading || (parseFloat(customerPaid) || 0) < cartTotal"
                        class="w-full bg-teal-700 text-white py-3 rounded-xl font-black text-lg shadow-xl hover:bg-teal-800 active:scale-[0.98] transition-all disabled:opacity-30 disabled:grayscale flex items-center justify-center gap-3">
                        <span x-show="!isLoading" class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            CONFIRM & PRINT
                        </span>
                        <span x-show="isLoading" class="flex items-center gap-2">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            CONFIRM & PRINT
                        </span>
                        <span x-show="isLoading" class="flex items-center gap-3">
                            <svg class="animate-spin h-7 w-7 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            PROCESSING...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Variation Selection Modal -->
        <div x-show="showModal" x-transition.opacity style="display: none;" class="fixed inset-0 bg-gray-900/60 backdrop-blur-md flex items-center justify-center p-4 z-[100]">
            <div @click.away="closeModal()" class="bg-white rounded-[2.5rem] shadow-2xl max-w-xl w-full overflow-hidden border border-gray-100">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <div>
                        <h3 class="font-black text-2xl text-gray-900 uppercase" x-text="selectedProduct ? selectedProduct.name : ''"></h3>
                        <p class="text-xs font-bold text-gray-500 uppercase mt-1">Select Variation</p>
                    </div>
                    <button @click="closeModal()" class="text-4xl leading-none font-light text-gray-400 hover:text-gray-900 transition">&times;</button>
                </div>
                <div class="p-8 max-h-[500px] overflow-y-auto space-y-3 scrollbar-hide">
                    <template x-for="variant in (selectedProduct ? selectedProduct.variants : [])" :key="variant.id">
                        <button @click="addToCart(selectedProduct, variant)" 
                            :disabled="variant.stock <= 0"
                            class="w-full flex items-center justify-between p-5 rounded-2xl border-2 transition-all group"
                            :class="variant.stock > 0 ? 'border-gray-100 hover:border-teal-500 hover:bg-teal-50' : 'border-gray-50 opacity-50 bg-gray-50 cursor-not-allowed'">
                            <div class="text-left">
                                <div class="font-bold text-base text-gray-900 uppercase group-hover:text-teal-700" x-text="variant.full_name.split(' - ').slice(1).join(' / ')"></div>
                                <div class="text-xs font-mono text-gray-500 mt-1" x-text="variant.sku"></div>
                                <div class="text-xs text-teal-600 font-bold mt-1" x-text="variant.stock + ' in stock'"></div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-black text-gray-900 group-hover:text-teal-700" x-text="'৳' + Number(variant.price).toFixed(0)"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        </div>
    </div>

    <script>
        function posApp() {
            return {
                products: @json($jsProducts),
                searchQuery: '',
                cart: [],
                paymentMethod: 'Cash',
                showModal: false,
                selectedProduct: null,
                isLoading: false,

                currentExchangeAmount: 0,

                // Customer 
                customerPaid: '',
                customerPhone: '',
                customerName: '',
                customerAddress: '',
                suggestions: [],

                // Global Discount
                globalFlat: '',
                globalPercent: '',

                // VAT
                vatPercent: 0,

                async fetchCustomers() {
                    if (this.customerPhone.length < 3) {
                        this.suggestions = [];
                        return;
                    }
                    try {
                        const res = await fetch(`{{ route('customers.search') }}?query=${this.customerPhone}`);
                        this.suggestions = await res.json();
                        
                        let exactMatch = this.suggestions.find(c => c.phone === this.customerPhone);
                        if (exactMatch) {
                            this.customerName = exactMatch.name;
                            this.customerAddress = exactMatch.address;
                        }
                    } catch (e) {
                        console.error('Customer fetch error', e);
                    }
                },

                selectCustomer(customer) {
                    this.customerPhone = customer.phone;
                    this.customerName = customer.name;
                    this.customerAddress = customer.address;
                    this.suggestions = [];
                },

                get filteredProducts() {
                    const q = this.searchQuery.toLowerCase();
                    if (q.length < 2) return [];
                    return this.products.filter(p => 
                        p.name.toLowerCase().includes(q) || 
                        p.sku.toLowerCase().includes(q) ||
                        p.variants.some(v => v.sku && v.sku.toLowerCase().includes(q))
                    );
                },

                handleSkuScan() {
                    let matchedVariant = null;
                    let matchedProduct = null;
                    for (let p of this.products) {
                        let v = p.variants.find(v => v.sku === this.searchQuery);
                        if (v) {
                            matchedVariant = v;
                            matchedProduct = p;
                            break;
                        }
                    }
                    if (matchedVariant) {
                        this.addToCart(matchedProduct, matchedVariant);
                        this.searchQuery = '';
                    }
                },

                openProductModal(product) {
                    this.selectedProduct = product;
                    this.showModal = true;
                    this.searchQuery = '';
                },

                closeModal() {
                    this.showModal = false;
                    this.selectedProduct = null;
                },

                addToCart(product, variant) {
                    const existing = this.cart.find(i => i.variation_id === variant.id);
                    if (existing) {
                        if (existing.qty + 1 <= variant.stock) {
                            existing.qty++;
                        } else {
                            alert('Stock limit reached');
                        }
                    } else {
                        this.cart.push({
                            variation_id: variant.id,
                            name: variant.full_name,
                            sku: variant.sku,
                            price: variant.price,
                            qty: 1,
                            max_stock: variant.stock,
                            discountType: 'flat',
                            discountValue: 0
                        });
                    }
                    this.closeModal();
                },

                updateQty(index, change) {
                    let item = this.cart[index];
                    let newQty = parseInt(item.qty) + change;
                    if (newQty > 0 && newQty <= item.max_stock) {
                        item.qty = newQty;
                    }
                },

                validateQty(index) {
                    let item = this.cart[index];
                    if (item.qty > item.max_stock) {
                        item.qty = item.max_stock;
                        alert('Max stock available: ' + item.max_stock);
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                calculateItemTotal(item) {
                    let baseTotal = item.price * item.qty;
                    let disc = parseFloat(item.discountValue) || 0;
                    if (item.discountType === 'percent') {
                        return baseTotal * (1 - disc / 100);
                    }
                    return Math.max(0, baseTotal - disc);
                },

                get cartSubtotal() {
                    return this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
                },

                get totalItemDiscount() {
                    return this.cart.reduce((sum, i) => sum + ((i.price * i.qty) - this.calculateItemTotal(i)), 0);
                },

                get calculatedGlobalDiscount() {
                    
                    let amountAfterItemDiscounts = this.cartSubtotal - this.totalItemDiscount;
                    
                    if (this.globalPercent && parseFloat(this.globalPercent) > 0) {
                        return amountAfterItemDiscounts * (parseFloat(this.globalPercent) / 100);
                    }
                    
                    if (this.globalFlat && parseFloat(this.globalFlat) > 0) {
                        return parseFloat(this.globalFlat);
                    }
                    
                    return 0;
                },
                
                get vatAmount() {
                    let taxableAmount = (this.cartSubtotal - this.totalItemDiscount) - this.calculatedGlobalDiscount;
                    let vat = parseFloat(this.vatPercent) || 0;
                    return Math.max(0, taxableAmount * (vat / 100));
                },

                get cartTotal() {
                    let amount = ((this.cartSubtotal - this.totalItemDiscount) - this.calculatedGlobalDiscount) + this.vatAmount;
                    return Math.max(0, amount);
                },

                get changeAmount() {
                    let paid = parseFloat(this.customerPaid) || 0;
                    return Math.max(0, paid - this.cartTotal);
                },

                async submitOrder() {
                    this.isLoading = true;
                    
                    let dType = 'flat';
                    let dValue = 0;
                    
                    if (this.globalPercent && parseFloat(this.globalPercent) > 0) {
                        dType = 'percent';
                        dValue = parseFloat(this.globalPercent);
                    } else if (this.globalFlat && parseFloat(this.globalFlat) > 0) {
                        dType = 'flat';
                        dValue = parseFloat(this.globalFlat);
                    }

                    try {
                        const payload = {
                            cart: this.cart.map(item => ({
                                id: item.variation_id,
                                qty: item.qty,
                                discount_type: item.discountType,
                                discount_value: item.discountValue
                            })),
                            payment_method: this.paymentMethod,
                            customer_name: this.customerName,
                            customer_phone: this.customerPhone,
                            customer_address: this.customerAddress,
                            discount_type: dType,
                            discount_value: dValue,
                            vat_percent: this.vatPercent,
                            vat_amount: this.vatAmount,
                            customer_pay: parseFloat(this.customerPaid) || 0,
                            change_amount: this.changeAmount
                        };

                        const res = await fetch("{{ route('orders.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const result = await res.json();
                        if (res.ok && result.success) {
                            window.open(`/orders/${result.order_id}/invoice`, '_blank');
                            window.location.reload();
                        } else {
                            alert('Error: ' + (result.message || 'Unknown error'));
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Submission failed');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
    </style>
</x-pos-layout>
