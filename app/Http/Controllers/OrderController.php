<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $userId = $request->get('user_id');
        
        $users = \App\Models\User::all();
        
        $orders = \App\Models\Order::with('user')
            ->withCount('items')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('orders.index', compact('orders', 'startDate', 'endDate', 'users', 'userId'));
    }

    /**
     * Display the specified order.
     */
    public function show(\App\Models\Order $order)
    {
        $order->load(['user', 'items.productSize.color.product', 'items.productSize.size']);
        return view('orders.show', compact('order'));
    }

    /**
     * Display the invoice for printing.
     */
    public function invoice(\App\Models\Order $order)
    {
        $order->load(['user', 'items.productSize.color.product', 'items.productSize.size']);
        return view('orders.invoice', compact('order'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.discount_type' => 'nullable|string|in:flat,percent',
            'cart.*.discount_value' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            // Customer Tracking
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            // Discounts
            'discount_type' => 'nullable|string|in:none,flat,percent',
            'discount_value' => 'nullable|numeric|min:0',
            // VAT
            'vat_percent' => 'nullable|numeric|min:0',
            'vat_amount' => 'nullable|numeric|min:0',
            // Payment
            'customer_pay' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            $order = \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
                $rawSubtotal = 0; // Sum of (base_price * qty) before any discounts
                $subtotal = 0; // Sum after item-level discounts
                $orderItemsData = [];
                
                foreach ($validated['cart'] as $item) {
                    $productSize = \App\Models\ProductSize::lockForUpdate()->find($item['id']);
                    
                    if (!$productSize) {
                        throw new \Exception("Product variant ID {$item['id']} not found.");
                    }
                    
                    if ($productSize->stock < $item['qty']) {
                        throw new \Exception("Insufficient stock for {$productSize->color->product->name} (Size: {$productSize->size->name})");
                    }
                    
                    $productSize->stock -= $item['qty'];
                    $productSize->sold += $item['qty'];
                    $productSize->save();
                    
                    $basePrice = ($productSize->price > 0) ? $productSize->price : $productSize->color->product->selling_price;
                    
                    // Calculate Item Discount
                    $itemDiscountType = $item['discount_type'] ?? 'flat';
                    $itemDiscountValue = $item['discount_value'] ?? 0;
                    $itemDiscountAmount = 0;

                    if ($itemDiscountType === 'percent') {
                        $itemDiscountAmount = ($basePrice * $item['qty']) * ($itemDiscountValue / 100);
                    } else {
                        $itemDiscountAmount = $itemDiscountValue;
                    }

                    $unitPrice = ($basePrice * $item['qty'] - $itemDiscountAmount) / $item['qty'];
                    $lineTotal = $basePrice * $item['qty'] - $itemDiscountAmount;
                    
                    // Track both raw and discounted subtotals
                    $rawSubtotal += $basePrice * $item['qty'];
                    $subtotal += $lineTotal;
                    
                    $orderItemsData[] = [
                        'product_size_id' => $productSize->id,
                        'product_name' => $productSize->color->product->name,
                        'product_sku' => $productSize->color->product->sku,
                        'color_name' => $productSize->color->color->name,
                        'size_name' => $productSize->size->name,
                        'quantity' => $item['qty'],
                        'base_price' => $basePrice,
                        'unit_price' => $unitPrice,
                        'discount_type' => $itemDiscountType,
                        'discount_value' => $itemDiscountValue,
                        'discount_amount' => $itemDiscountAmount,
                        'subtotal' => $lineTotal,
                    ];
                }

                // Calculate Global Discount (applied to subtotal after item discounts)
                $discountAmount = 0;
                $type = $validated['discount_type'] ?? 'none';
                $value = $validated['discount_value'] ?? 0;

                if ($type === 'flat') {
                    $discountAmount = $value;
                } elseif ($type === 'percent') {
                    $discountAmount = $subtotal * ($value / 100);
                }

                // Calculate VAT (applied after all discounts)
                $vatAmount = $validated['vat_amount'] ?? 0;
                
                // Final Total: subtotal - global_discount + vat
                $totalAmount = max(0, $subtotal - $discountAmount + $vatAmount);
                
                // Customer Logic
                $customerId = null;
                if (!empty($validated['customer_phone'])) {
                    $phone = $validated['customer_phone'];
                    
                    // Normalize phone (Ensure +880)
                    if (str_starts_with($phone, '0')) {
                        $phone = '+880' . substr($phone, 1);
                    } elseif (!str_starts_with($phone, '+880')) {
                        if (strlen($phone) === 10) {
                            $phone = '+880' . $phone;
                        }
                    }

                    $customer = \App\Models\Customer::updateOrCreate(
                        ['phone' => $phone],
                        [
                            'name' => $validated['customer_name'] ?? 'Walk-in',
                            'address' => $validated['customer_address'] ?? null,
                            'total_orders' => \Illuminate\Support\Facades\DB::raw('total_orders + 1'),
                            'total_spent' => \Illuminate\Support\Facades\DB::raw('total_spent + ' . $totalAmount),
                        ]
                    );
                    $customerId = $customer->id;
                    $validated['customer_phone'] = $phone;
                }

                // Create Order
                $order = \App\Models\Order::create([
                    'user_id' => auth()->id(),
                    'customer_id' => $customerId,
                    'customer_name' => $validated['customer_name'] ?? null,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'customer_address' => $validated['customer_address'] ?? null,
                    'subtotal' => $rawSubtotal, // Store pre-discount subtotal
                    'discount_amount' => $discountAmount,
                    'discount_type' => $type,
                    'vat_amount' => $vatAmount,
                    'vat_percent' => $validated['vat_percent'] ?? 0,
                    'total_amount' => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'customer_pay' => $validated['customer_pay'] ?? 0,
                    'change_amount' => $validated['change_amount'] ?? 0,
                    'status' => 'completed'
                ]);
                
                foreach ($orderItemsData as $data) {
                    $order->items()->create($data);
                }

                return $order;
            });
            
            return response()->json([
                'success' => true, 
                'message' => 'Order processed successfully',
                'order_id' => $order->id
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Get order details for exchange.
     */
    public function getExchangeData(Request $request)
    {
        $rawId = $request->get('order_id');
        $phone = $request->get('phone');

        if ($phone) {
            $orders = \App\Models\Order::where('customer_phone', $phone)
                ->with(['items.productSize.color.product', 'items.productSize.size'])
                ->latest()
                ->limit(5)
                ->get();

            if ($orders->isEmpty()) {
                return response()->json(['success' => false, 'message' => "No orders found for phone $phone"], 404);
            }

            return response()->json([
                'success' => true,
                'orders' => $orders,
                'can_exchange' => true
            ]);
        }

        // Normalize ID
        $orderId = preg_replace('/[^0-9]/', '', $rawId);
        $orderId = ltrim($orderId, '0');

        $order = \App\Models\Order::with(['items.productSize.color.product', 'items.productSize.size'])
            ->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => "Order #$rawId not found"], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'orders' => [$order],
            'can_exchange' => true
        ]);
    }

    /**
     * Process product exchange. (Manual & Linked)
     */
    public function processExchange(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'order_item_id' => 'nullable|exists:order_items,id',
            'returned_product_name' => 'nullable|string',
            'returned_product_sku' => 'nullable|string|max:255',
            'new_product_sku' => 'nullable|string',
            'new_product_name' => 'nullable|string', // Added for manual entry
            'old_price' => 'required|numeric',
            'new_price' => 'required|numeric',
            'reason' => 'required|string',
            'customer_phone' => 'nullable|string',
            'customer_pay' => 'nullable|numeric',
            'change_amount' => 'nullable|numeric',
        ]);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
                // ... (existing order fetching code remains same, implied context)
                $order = null;
                $returnedItem = null;
                $returnedProductSize = null;

                if (!empty($validated['order_id'])) {
                    $order = \App\Models\Order::find($validated['order_id']);
                }

                if ($order && !empty($validated['order_item_id'])) {
                    $returnedItem = $order->items()->find($validated['order_item_id']);
                    if ($returnedItem) {
                        $returnedProductSize = $returnedItem->productSize;
                    }
                }

                // 1. Determine New Product (Simplified - No Stock/Link Check)
                $newProductSize = null;
                $systemNewPrice = $validated['new_price']; 

                // We trust the input name/sku from the user form.
                // If they provided a SKU via search or manual entry, we just save it as text.
                $newProductName = $validated['new_product_name'] ?? 'Exchange Product';
                $newProductSku = $validated['new_product_sku'] ?? 'MANUAL';

                // 2. Increment stock for returned item (if linked)
                if ($returnedProductSize) {
                    $returnedProductSize->stock += 1;
                    $returnedProductSize->sold -= 1;
                    $returnedProductSize->save();
                }

                // 3. Financials
                $oldPrice = $validated['old_price'];
                $newPrice = $validated['new_price'];
                $difference = $newPrice - $oldPrice;

                // 4. Create the Exchange Invoice (Order record)
                $exchangeOrder = \App\Models\Order::create([
                    'user_id' => auth()->id(),
                    'customer_id' => $order ? $order->customer_id : null,
                    'customer_name' => $order ? $order->customer_name : 'Walk-in',
                    'customer_phone' => $validated['customer_phone'] ?? ($order ? $order->customer_phone : null),
                    'subtotal' => $newPrice,
                    'discount_amount' => $oldPrice, 
                    'discount_type' => 'flat',
                    'total_amount' => $difference,
                    'exchange_amount' => $difference,
                    'payment_method' => 'Exchange',
                    'customer_pay' => $validated['customer_pay'] ?? 0,
                    'change_amount' => $validated['change_amount'] ?? 0,
                    'status' => 'completed',
                    'note' => $validated['reason']
                ]);

                // Record the Return (Negative Qty)
                $exchangeOrder->items()->create([
                    'product_size_id' => $returnedProductSize ? $returnedProductSize->id : null,
                    'product_name' => $validated['returned_product_name'] ?? ($returnedItem ? $returnedItem->product_name : 'Manual Return'),
                    'product_sku' => $returnedItem ? $returnedItem->product_sku : ($validated['returned_product_sku'] ?? 'N/A'),
                    'color_name' => $returnedItem ? $returnedItem->color_name : 'N/A',
                    'size_name' => $returnedItem ? $returnedItem->size_name : 'N/A',
                    'quantity' => -1,
                    'base_price' => $oldPrice,
                    'unit_price' => $oldPrice,
                    'discount_amount' => 0,
                    'subtotal' => -$oldPrice
                ]);

                // Record the New Item (Positive Qty - Simplified)
                $exchangeOrder->items()->create([
                    'product_size_id' => null, // Explicitly distinct from inventory
                    'product_name' => $newProductName,
                    'product_sku' => $newProductSku,
                    'color_name' => 'N/A',
                    'size_name' => 'N/A',
                    'quantity' => 1,
                    'base_price' => $systemNewPrice,
                    'unit_price' => $newPrice,
                    'discount_amount' => $systemNewPrice - $newPrice,
                    'discount_type' => 'flat',
                    'subtotal' => $newPrice
                ]);

                // 5. Record the Exchange History
                \App\Models\ProductExchange::create([
                    'order_id' => $exchangeOrder->id, // Linking to the new exchange order
                    'returned_product_size_id' => $returnedProductSize ? $returnedProductSize->id : null,
                    'new_product_size_id' => $newProductSize ? $newProductSize->id : null,
                    'old_unit_price' => $oldPrice,
                    'new_unit_price' => $newPrice,
                    'difference_amount' => $difference,
                    'reason' => $validated['reason'],
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Exchange processed successfully',
                    'exchange_order_id' => $exchangeOrder->id
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Export order details as JSON.
     */
    public function exportJson(\App\Models\Order $order)
    {
        $order->load(['user', 'items.productSize.color.product', 'items.productSize.size']);

        $data = $order->toArray();

        // Ensure product_id is explicitly in each item
        foreach ($data['items'] as $key => $item) {
            $productSize = $order->items[$key]->productSize;
            $data['items'][$key]['product_id'] = $productSize && $productSize->color ? $productSize->color->product_id : null;
        }

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="order_' . $order->id . '.json"',
        ]);
    }

    /**
     * Export bulk orders as JSON.
     */
    public function exportBulkJson(Request $request)
    {
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $orders = \App\Models\Order::with(['user', 'items.productSize.color.product', 'items.productSize.size'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->get();

        $data = $orders->map(function ($order) {
            $orderArray = $order->toArray();
            foreach ($orderArray['items'] as $key => $item) {
                $productSize = $order->items[$key]->productSize;
                $orderArray['items'][$key]['product_id'] = $productSize && $productSize->color ? $productSize->color->product_id : null;
            }
            return $orderArray;
        });

        $filename = "orders_export";
        if ($startDate) $filename .= "_from_{$startDate}";
        if ($endDate) $filename .= "_to_{$endDate}";

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '.json"',
        ]);
    }
}
