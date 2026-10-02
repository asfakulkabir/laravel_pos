<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\StockIntakeItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sales' => Order::where('status', 'Completed')
                ->whereDate('created_at', Carbon::today())
                ->sum('total_amount'),
            'orders_today' => Order::whereDate('created_at', Carbon::today())->count(),
            'total_products' => Product::count(), // Total Style Numbers
            'total_stock' => ProductSize::sum('stock'), // Total individual items in warehouse
            'total_sold' => ProductSize::sum('sold'), // Total items sold
            'total_intake' => StockIntakeItem::sum('quantity'), // Total items ever received
            'low_stock_count' => ProductSize::where('stock', '<=', 5)->count(),
        ];

        $recent_orders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Weekly Sales Calculation
        $weekly_labels = [];
        $weekly_data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weekly_labels[] = $date->format('D'); // e.g., "Mon"
            $weekly_data[] = Order::where('status', 'Completed')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
        }

        $chart_data = [
            'labels' => $weekly_labels,
            'datasets' => $weekly_data
        ];

        return view('dashboard', compact('stats', 'recent_orders', 'chart_data'));
    }
}
