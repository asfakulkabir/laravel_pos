<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body {
                margin: 0;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .print-padding {
                padding: 10mm !important;
            }
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: #000;
            background: #f7fafc;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            width: 120px;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-label {
            font-weight: 700;
            width: 140px;
            display: inline-block;
        }

        .table-header {
            background-color: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-cell {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 4px;
        }

        .summary-label {
            width: 150px;
            text-align: right;
            font-weight: 700;
            padding-right: 20px;
            color: #000;
        }

        .summary-value {
            width: 120px;
            text-align: right;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="invoice-container print-padding min-h-screen">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="logo-container">
                <img src="{{ asset('mamata_logo.webp') }}" alt="Mamata Fashion" class="max-w-full h-auto">
            </div>
            <h1 class="text-3xl font-extrabold uppercase tracking-tight mb-2 text-black">Mamata Fashion</h1>
            <p class="text-sm font-semibold max-w-md mx-auto leading-tight text-black">
                Mirpur New Market, VTCB Tower-3 Main Road, Shopno:101 Block-G Mirpur-1 Dhaka-1216 Mobile: 01711311170
            </p>
        </div>

        <!-- Order Information -->
        <div class="grid grid-cols-1 gap-1 text-sm mb-8 font-semibold text-black">
            <div><span class="info-label">Invoice #</span> {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div><span class="info-label">Served By: </span> {{ $order->user->name ?? 'System' }}</div>
            <div><span class="info-label">Customer Name: </span> {{ $order->customer_name ?? 'N/A' }}</div>
            <div><span class="info-label">Mobile: </span> {{ $order->customer_phone ?? 'N/A' }}</div>
            <div><span class="info-label">Date: </span> {{ $order->created_at->format('Y-m-d') }}</div>
            <div><span class="info-label">Time:</span> {{ $order->created_at->format('h:i A') }}</div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-sm mb-8 text-black">
            <thead>
                <tr class="table-header">
                    <th class="table-cell text-left font-extrabold">Product</th>
                    <th class="table-cell text-center font-extrabold">Qty</th>
                    <th class="table-cell text-right font-extrabold">Unit Price</th>
                    <th class="table-cell text-right font-extrabold">Discount</th>
                    <th class="table-cell text-right font-extrabold">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        {{-- Fallback Logic for Zero Prices --}}
                        @php
                            $displayBasePrice = $item->base_price;
                            $displayDiscount = $item->discount_amount;
                            
                            if ($displayBasePrice <= 0) {
                                // Try to get current product price
                                $currentPrice = ($item->productSize?->price > 0) 
                                    ? $item->productSize->price 
                                    : ($item->productSize?->color?->product?->selling_price ?? 0);
                                    
                                if ($currentPrice > 0) {
                                    $displayBasePrice = $currentPrice;
                                    // Make the math work: Discount = (Price * Qty) - Subtotal
                                    $displayDiscount = ($displayBasePrice * $item->quantity) - $item->subtotal;
                                }
                            }
                        @endphp
                        <td class="table-cell">
                            <div class="font-bold">{{ $item->product_name ?? $item->productSize?->color?->product?->name ?? 'Deleted Product' }}</div>
                            <div class="text-[10px] text-black uppercase">
                                {{ $item->color_name ?? $item->productSize?->color?->color?->name ?? 'N/A' }} / {{ $item->size_name ?? $item->productSize?->size?->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="table-cell text-center font-bold">{{ $item->quantity }}</td>
                        <td class="table-cell text-right font-bold">{{ number_format($displayBasePrice, 2) }}</td>
                        <td class="table-cell text-right font-bold">{{ number_format($displayDiscount, 2) }}</td>
                        <td class="table-cell text-right font-bold">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        @php
            // Calculate item-level discounts total
            $itemDiscountsTotal = $order->items->sum('discount_amount');
            
            // Use stored VAT if available, otherwise calculate it for old orders
            if (isset($order->vat_amount)) {
                $calculatedVat = $order->vat_amount;
            } else {
                // Legacy calculation: total_amount - (subtotal - item_discounts - global_discount + exchange)
                $calculatedVat = $order->total_amount - ($order->subtotal - $itemDiscountsTotal - $order->discount_amount + ($order->exchange_amount ?? 0));
                $calculatedVat = max(0, $calculatedVat);
            }
        @endphp
        <div class="text-sm border-t-2 border-gray-100 pt-6 space-y-2 text-black">
            <div class="summary-row">
                <div class="summary-label">Subtotal</div>
                <div class="summary-value">{{ number_format($order->subtotal, 2) }}</div>
            </div>
            @if($itemDiscountsTotal > 0)
            <div class="summary-row">
                <div class="summary-label text-black">Item Discounts</div>
                <div class="summary-value text-black">-{{ number_format($itemDiscountsTotal, 2) }}</div>
            </div>
            @endif
            
            <div class="summary-row">
                <div class="summary-label text-black">Order Discount</div>
                <div class="summary-value text-black">-{{ number_format($order->discount_amount, 2) }}</div>
            </div>
            
            <div class="summary-row">
                <div class="summary-label">VAT</div>
                <div class="summary-value">+{{ number_format($calculatedVat, 2) }}</div>
            </div>
            @if($order->exchange_amount != 0)
            <div class="summary-row">
                <div class="summary-label">Exchange Adjustment</div>
                <div class="summary-value">{{ $order->exchange_amount > 0 ? '+' : '' }}{{ number_format($order->exchange_amount, 2) }}</div>
            </div>
            @endif
            <div class="summary-row border-t border-gray-100 pt-2 mt-2">
                <div class="summary-label uppercase text-base">Net Amount</div>
                <div class="summary-value text-base">{{ number_format($order->total_amount, 2) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Customer Paid</div>
                <div class="summary-value">{{ number_format($order->customer_pay, 2) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label text-black">Change Return</div>
                <div class="summary-value text-black">{{ number_format($order->change_amount, 2) }}</div>
            </div>
        </div>

        <!-- Footer Policy -->
        <div class="mt-5 text-[11px] text-center text-black font-bold leading-relaxed border-t border-gray-100 pt-6">
            <p>Item may be exchanged subject to MAMATA FASHION sales policies within 7 days. An item may be exchanged only once. When you exchange your purchased products, please bring this invoice.</p>
        </div>

        <!-- Print Action -->
        <div class="mt-10 no-print flex gap-3 justify-center">
            <button onclick="window.print()" class="px-8 py-3 bg-black text-white text-xs font-black rounded-xl shadow-lg hover:scale-105 transition active:scale-95 uppercase tracking-widest">
                Print Invoice
            </button>
            <button onclick="window.close()" class="px-8 py-3 bg-gray-200 text-gray-700 text-xs font-black rounded-xl hover:bg-gray-300 transition uppercase tracking-widest">
                Close
            </button>
        </div>
    </div>

    <script>
        // Auto print on load if it's meant for quick printing
        if (window.location.search.includes('print=true')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>
