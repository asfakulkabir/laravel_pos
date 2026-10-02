<x-app-layout>
    <div class="p-4 sm:p-6 max-w-5xl mx-auto" x-data="stockIntake()">
        <header class="border-b-2 border-teal-500 mb-6 pb-2 flex items-center justify-between">
            <h2 class="text-3xl font-black text-gray-900 leading-tight flex items-center">
                <span class="mr-3 text-teal-600">📦</span> Quick Stock Intake
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-900">
                ← Back to Products
            </a>
        </header>

        <template x-if="sessionSummary">
            <div class="mb-6 bg-white border border-teal-100 rounded-xl p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Last Intake Reference</p>
                        <p class="text-2xl font-black text-slate-900" x-text="sessionSummary.session.reference"></p>
                        <p class="text-sm text-slate-500">
                            Recorded:
                            <span class="font-semibold text-slate-700" x-text="formatDate(sessionSummary.session.created_at)"></span>
                            · Total Items:
                            <span class="font-semibold" x-text="sessionSummary.session.total_items"></span>
                            · Total Qty:
                            <span class="font-semibold" x-text="sessionSummary.session.total_quantity"></span>
                        </p>
                    </div>
                    <button type="button" @click="printSummary"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-bold text-teal-700 border border-teal-200 hover:bg-teal-50 transition">
                        🖨 Print Summary
                    </button>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-600">Product</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-600">Color</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-600">Size</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-600">SKU</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-600">Previous → New</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-600">Added</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" x-ref="sessionTable">
                            <template x-for="item in sessionSummary.items" :key="item.size_id">
                                <tr>
                                    <td class="px-3 py-2 font-semibold text-slate-900" x-text="item.product"></td>
                                    <td class="px-3 py-2 text-slate-700" x-text="item.color"></td>
                                    <td class="px-3 py-2 text-slate-700" x-text="item.size"></td>
                                    <td class="px-3 py-2 font-mono text-slate-600" x-text="item.sku"></td>
                                    <td class="px-3 py-2 text-slate-700">
                                        <span x-text="item.previous_stock"></span>
                                        <span class="mx-1 text-slate-400">→</span>
                                        <span x-text="item.new_stock"></span>
                                    </td>
                                    <td class="px-3 py-2 text-teal-700 font-bold" x-text="item.added"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <template x-if="alert">
            <div class="mb-6 rounded-lg px-4 py-3 text-sm font-semibold border"
                 :class="alert.type === 'success'
                        ? 'bg-green-50 border-green-200 text-green-800'
                        : 'bg-red-50 border-red-200 text-red-800'">
                <p x-text="alert.message"></p>
            </div>
        </template>

        <section class="bg-white p-6 rounded-xl border border-gray-200 mb-8">
            <h3 class="text-xl font-bold text-teal-700 mb-4">Scan Barcode</h3>
            <form @submit.prevent="addScan" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label for="barcode" class="block text-sm font-semibold text-gray-700 mb-1">Product Size Barcode</label>
                    <input type="text" id="barcode" x-model.trim="barcode"
                           x-ref="barcodeInput"
                           placeholder="e.g. P1001"
                           class="w-full border border-gray-300 rounded-lg py-2.5 px-3 focus:ring-teal-500 focus:border-teal-500"
                           autofocus
                           @keydown.enter.prevent="addScan">
                </div>
                <div class="flex items-end gap-3 pb-0.5">
                    <div class="flex items-center gap-2 mb-3 bg-teal-50 px-3 py-2 rounded-lg border border-teal-100">
                        <input type="checkbox" id="update_stock" x-model="updateStock" 
                               class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <label for="update_stock" class="text-sm font-black text-teal-700 uppercase cursor-pointer select-none">Update Stock</label>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg font-bold transition duration-200">
                        ➕ Add
                    </button>
                </div>
            </form>
            <p class="text-xs text-gray-500 mt-2">Scan or type the barcode. If <span class="text-teal-600 font-bold uppercase">Update Stock</span> is checked, main stock will be increased.</p>
        </section>

        <section class="bg-white p-6 rounded-xl border border-gray-200" x-show="entries.length > 0" x-cloak>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-teal-700">Scanned Items</h3>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-gray-500 uppercase tracking-tight">Total items: <strong class="text-slate-900" x-text="entries.length"></strong></span>
                    <span class="w-px h-4 bg-gray-200"></span>
                    <span class="text-sm font-bold text-teal-600 uppercase tracking-tight">Total Quantity: <strong class="text-teal-700" x-text="entries.reduce((acc, e) => acc + (parseInt(e.quantity) || 0), 0)"></strong></span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Product</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Color</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Size</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">SKU</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Current Stock</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Add Qty</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(entry, index) in entries" :key="entry.size_id">
                            <tr>
                                <td class="px-4 py-2 font-semibold text-gray-900" x-text="entry.product"></td>
                                <td class="px-4 py-2 text-gray-700" x-text="entry.color"></td>
                                <td class="px-4 py-2 text-gray-700" x-text="entry.size"></td>
                                <td class="px-4 py-2 text-gray-700 font-mono" x-text="entry.sku"></td>
                                <td class="px-4 py-2 text-gray-700" x-text="entry.current_stock"></td>
                                <td class="px-4 py-2">
                                    <input type="number" min="1" class="w-24 border border-gray-300 rounded-lg py-1.5 px-2"
                                           x-model.number="entry.quantity">
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <button type="button" @click="removeEntry(index)"
                                            class="text-red-600 hover:text-red-800 text-xs font-semibold">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="mt-6 flex justify-end" x-show="entries.length > 0" x-cloak>
            <button type="button" @click="openConfirmModal"
                    :disabled="saving"
                    class="inline-flex items-center bg-teal-600 hover:bg-teal-700 disabled:opacity-60 text-white px-8 py-3 rounded-lg font-black transition duration-200">
                <span x-show="!saving">💾 Save Stock</span>
                <span x-show="saving">Saving...</span>
            </button>
        </div>
        
        <!-- Confirmation Modal -->
        <div x-show="confirmModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="closeConfirmModal"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Confirm Stock Intake</p>
                        <h3 class="text-2xl font-black text-slate-900">Review scanned items</h3>
                        <p class="text-sm text-slate-500">Time: <span class="font-semibold" x-text="summaryTimestamp"></span></p>
                    </div>
                    <button type="button" class="p-2 text-slate-400 hover:text-slate-600" @click="closeConfirmModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">
                    <template x-if="entries.length === 0">
                        <p class="text-sm text-slate-500">No items have been scanned yet.</p>
                    </template>
                    <template x-if="entries.length > 0">
                        <table class="min-w-full text-sm divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Product</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Color</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Size</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">SKU</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Current Stock</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Add Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in entries" :key="item.size_id">
                                    <tr>
                                        <td class="px-3 py-2 font-semibold text-slate-900" x-text="item.product"></td>
                                        <td class="px-3 py-2 text-slate-700" x-text="item.color"></td>
                                        <td class="px-3 py-2 text-slate-700" x-text="item.size"></td>
                                        <td class="px-3 py-2 font-mono text-slate-600" x-text="item.sku"></td>
                                        <td class="px-3 py-2 text-slate-700" x-text="item.current_stock"></td>
                                        <td class="px-3 py-2 text-teal-700 font-bold" x-text="item.quantity"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </template>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-4 border-t border-slate-100">
                    <button type="button" @click="printCurrentEntries"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50">
                        🖨 Print Preview
                    </button>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <button type="button" @click="closeConfirmModal" :disabled="saving"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-60">
                            Cancel
                        </button>
                        <button type="button" @click="processStock" :disabled="saving"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-white bg-teal-600 rounded-lg hover:bg-teal-700 disabled:opacity-60">
                            <span x-show="!saving">Confirm & Update Stock</span>
                            <span x-show="saving">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function stockIntake() {
        return {
            updateStock: true,
            barcode: '',
            entries: [],
            alert: null,
            saving: false,
            confirmModalOpen: false,
            summaryTimestamp: '',
            sessionSummary: null,
            
            formatDate(value) {
                try {
                    return new Date(value).toLocaleString();
                } catch (error) {
                    return value;
                }
            },
            async addScan() {
                if (!this.barcode) {
                    this.alert = {type: 'error', message: 'Please scan a barcode first.'};
                    return;
                }
                try {
                    const response = await fetch(`{{ route('stock.intake.lookup') }}?sku=${encodeURIComponent(this.barcode)}`);
                    const data = await response.json();
                    if (!response.ok) {
                        this.alert = {type: 'error', message: data.error || 'Unable to find that barcode.'};
                        return;
                    }

                    const existing = this.entries.find(item => item.size_id === data.size_id);
                    if (existing) {
                        existing.quantity += 1;
                    } else {
                        this.entries.unshift({
                            ...data,
                            quantity: 1
                        });
                    }

                    this.alert = {type: 'success', message: `${data.sku} added to queue.`};
                    this.barcode = '';
                    this.$refs.barcodeInput.focus();
                } catch (error) {
                    console.error(error);
                    this.alert = {type: 'error', message: 'Something went wrong while scanning.'};
                }
            },
            removeEntry(index) {
                this.entries.splice(index, 1);
            },
            openConfirmModal() {
                if (this.entries.length === 0) {
                    this.alert = {type: 'error', message: 'Scan at least one product before saving.'};
                    return;
                }
                this.summaryTimestamp = new Date().toLocaleString();
                this.confirmModalOpen = true;
            },
            closeConfirmModal() {
                if (this.saving) return;
                this.confirmModalOpen = false;
            },
            async processStock() {
                if (this.entries.length === 0) {
                    this.alert = {type: 'error', message: 'No scanned items to save.'};
                    this.confirmModalOpen = false;
                    return;
                }

                this.saving = true;
                try {
                    const payload = {
                        update_stock: this.updateStock,
                        entries: this.entries.map(entry => ({
                            size_id: entry.size_id,
                            quantity: entry.quantity
                        }))
                    };

                    const response = await fetch(`{{ route('stock.intake.store') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        this.alert = {type: 'error', message: data.error || 'Unable to save stock.'};
                        return;
                    }

                    this.sessionSummary = data;
                    this.alert = {type: 'success', message: `Stock updated (Ref ${data.session.reference}).`};
                    this.entries = [];
                    this.confirmModalOpen = false;
                } catch (error) {
                    console.error(error);
                    this.alert = {type: 'error', message: 'Unexpected error while saving.'};
                } finally {
                    this.saving = false;
                }
            },
            printPayload(payload) {
                const win = window.open('', '', 'width=900,height=700');
                if (!win) return;
                const rows = payload.items.map((item, index) => {
                    const prev = item.previous_stock != null ? item.previous_stock : (item.current_stock || 0);
                    const next = item.new_stock != null ? item.new_stock : prev + (item.added || item.quantity || 0);
                    const added = item.added || item.quantity || 0;
                    return `
                    <tr>
                        <td style="padding:8px;border:1px solid #e2e8f0;">${index + 1}</td>
                        <td style="padding:8px;border:1px solid #e2e8f0;">${item.product}</td>
                        <td style="padding:8px;border:1px solid #e2e8f0;">${item.color}</td>
                        <td style="padding:8px;border:1px solid #e2e8f0;">${item.size}</td>
                        <td style="padding:8px;border:1px solid #e2e8f0;">${item.sku}</td>
                        <td style="padding:8px;border:1px solid #e2e8f0;">${prev}</td>
                        <td style="padding:8px;border:1px solid #e2e8f0;">${next}</td>
                        <td style="padding:8px;border:1px solid #e2e8f0;">${added}</td>
                    </tr>
                `;
                }).join('');

                win.document.write(`
                    <html>
                        <head>
                            <title>${payload.title}</title>
                            <style>
                                body { font-family: 'Courier New', monospace; padding: 24px; color: #0f172a; }
                                h1 { margin-bottom: 0; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 10px; }
                                table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 12px; }
                                th { background: #f8fafc; text-align: left; }
                                .footer { margin-top: 30px; font-size: 10px; border-top: 1px dashed #ccc; padding-top: 10px; }
                            </style>
                        </head>
                        <body>
                            <div>
                                <h1>${payload.title}</h1>
                                <p><strong>Reference:</strong> ${payload.reference}</p>
                                <p><strong>Date & Time:</strong> ${payload.timestamp}</p>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="padding:8px;border:1px solid #000;">#</th>
                                        <th style="padding:8px;border:1px solid #000;">Product</th>
                                        <th style="padding:8px;border:1px solid #000;">Color</th>
                                        <th style="padding:8px;border:1px solid #000;">Size</th>
                                        <th style="padding:8px;border:1px solid #000;">SKU</th>
                                        <th style="padding:8px;border:1px solid #000;">Prev</th>
                                        <th style="padding:8px;border:1px solid #000;">New</th>
                                        <th style="padding:8px;border:1px solid #000;">Add</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                            <div class="footer">
                                <p>Mamata Fashion POS - Intake Report</p>
                                <p>Generated on ${new Date().toLocaleString()}</p>
                            </div>
                        </body>
                    </html>
                `);
                win.document.close();
                win.focus();
                win.print();
            },
            printCurrentEntries() {
                if (!this.entries.length) return;
                this.printPayload({
                    title: 'Stock Intake Preview',
                    reference: 'Pending Confirmation',
                    timestamp: this.summaryTimestamp || new Date().toLocaleString(),
                    items: this.entries.map(entry => ({
                        product: entry.product,
                        color: entry.color,
                        size: entry.size,
                        sku: entry.sku,
                        current_stock: entry.current_stock,
                        added: entry.quantity
                    }))
                });
            },
            printSummary() {
                if (!this.sessionSummary) return;
                this.printPayload({
                    title: `Stock Intake ${this.sessionSummary.session.reference}`,
                    reference: this.sessionSummary.session.reference,
                    timestamp: this.formatDate(this.sessionSummary.session.created_at),
                    items: this.sessionSummary.items
                });
            }
        }
    }
    </script>
</x-app-layout>
