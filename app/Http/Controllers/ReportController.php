<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        // Fetch Orders with needed relationships
        // We need product details to get costing_price
        $orders = Order::with(['items.productSize.color.product', 'user'])
            ->where('status', 'Completed') // Assuming we only count completed orders
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;

        $reportData = $orders->map(function ($order) use (&$totalRevenue, &$totalCost) {
            $orderCost = 0;

            foreach ($order->items as $item) {
                // Determine Cost Price
                // Try to navigate to Product model
                $costingPrice = 0;
                
                if ($item->productSize && $item->productSize->color && $item->productSize->color->product) {
                    $costingPrice = $item->productSize->color->product->costing_price;
                }
                
                // Backup: if product was deleted but we have some other ref? 
                // For now, if 0, profit will appear higher.
                
                $orderCost += ($costingPrice * $item->quantity);
            }

            // Order Revenue (Total Amount)
            // This already includes Item Discounts, Order Discounts, VAT, Exchange adjustments
            $orderRevenue = $order->total_amount;

            // Update Aggregates
            $totalRevenue += $orderRevenue;
            $totalCost += $orderCost;

            return [
                'order_id' => $order->id,
                'date' => $order->created_at,
                'customer' => $order->customer_name ?? 'Guest',
                'items_count' => $order->items->sum('quantity'),
                'revenue' => $orderRevenue,
                'cost' => $orderCost,
                'profit' => $orderRevenue - $orderCost,
            ];
        });

        $totalProfit = $totalRevenue - $totalCost;

        return view('reports.sales', compact('reportData', 'startDate', 'endDate', 'totalRevenue', 'totalCost', 'totalProfit'));
    }

    /**
     * Get sales report as JSON API
     */
    public function salesApi(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

            // Validate date format
            $validatedData = $request->validate([
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d',
            ]);

            // Fetch Orders with needed relationships
            $orders = Order::with(['items.productSize.color.product', 'user'])
                ->where('status', 'Completed')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->latest()
                ->get();

            $totalRevenue = 0;
            $totalCost = 0;
            $totalProfit = 0;

            $reportData = $orders->map(function ($order) use (&$totalRevenue, &$totalCost) {
                $orderCost = 0;

                foreach ($order->items as $item) {
                    $costingPrice = 0;
                    
                    if ($item->productSize && $item->productSize->color && $item->productSize->color->product) {
                        $costingPrice = $item->productSize->color->product->costing_price;
                    }
                    
                    $orderCost += ($costingPrice * $item->quantity);
                }

                // Order Revenue
                $orderRevenue = $order->total_amount;

                // Update Aggregates
                $totalRevenue += $orderRevenue;
                $totalCost += $orderCost;

                return [
                    'order_id' => $order->id,
                    'date' => $order->created_at->toIso8601String(),
                    'customer' => $order->customer_name ?? 'Guest',
                    'items_count' => $order->items->sum('quantity'),
                    'revenue' => (float) $orderRevenue,
                    'cost' => (float) $orderCost,
                    'profit' => (float) ($orderRevenue - $orderCost),
                ];
            });

            $totalProfit = $totalRevenue - $totalCost;

            return response()->json([
                'success' => true,
                'data' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'orders' => $reportData,
                    'summary' => [
                        'total_orders' => $orders->count(),
                        'total_revenue' => (float) $totalRevenue,
                        'total_cost' => (float) $totalCost,
                        'total_profit' => (float) $totalProfit,
                    ]
                ],
                'message' => 'Sales report generated successfully'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating sales report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
