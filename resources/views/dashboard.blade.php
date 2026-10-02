<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-2">
            <div>
                <h2 class="font-black text-4xl text-slate-800 leading-tight tracking-tighter">
                    POS
                </h2>
                <p class="text-xs font-bold text-teal-600 uppercase tracking-widest mt-1">Real-time Terminal Insight</p>
            </div>

            <div class="flex items-center gap-4" x-data="dashboardWidgets()">
                <!-- 3D Calendar Widget -->
                <div class="relative group cursor-default">
                    <div class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative flex items-center bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-[0_5px_15px_-3px_rgba(0,0,0,0.07),0_4px_6px_-2px_rgba(0,0,0,0.05)] active:scale-95 transition-transform">
                        <div class="flex flex-col items-center justify-center border-r border-slate-100 pr-3 mr-3">
                            <span class="text-[10px] font-black text-red-500 uppercase tracking-tighter leading-none mb-1" x-text="month"></span>
                            <span class="text-xl font-black text-slate-900 leading-none" x-text="day"></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none" x-text="weekday"></span>
                            <span class="text-sm font-black text-slate-700 mt-1" x-text="year"></span>
                        </div>
                    </div>
                </div>

                <!-- 3D Clock Widget -->
                <div class="relative group cursor-default">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative flex items-center bg-slate-900 px-5 py-2 rounded-xl border border-slate-800 shadow-[0_10px_20px_-5px_rgba(0,0,0,0.3)] active:scale-95 transition-transform">
                        <div class="flex items-center gap-1.5">
                            <div class="w-8 flex flex-col items-center">
                                <span class="text-xl font-black text-white tabular-nums leading-none" x-text="hours"></span>
                                <span class="text-[8px] font-bold text-slate-500 uppercase tracking-tighter mt-1">HRS</span>
                            </div>
                            <span class="text-xl font-black text-indigo-400 animate-pulse">:</span>
                            <div class="w-8 flex flex-col items-center">
                                <span class="text-xl font-black text-white tabular-nums leading-none" x-text="minutes"></span>
                                <span class="text-[8px] font-bold text-slate-500 uppercase tracking-tighter mt-1">MIN</span>
                            </div>
                            <span class="text-xl font-black text-indigo-400 opacity-50">:</span>
                            <div class="w-8 flex flex-col items-center">
                                <span class="text-xl font-black text-indigo-400 tabular-nums leading-none" x-text="seconds"></span>
                                <span class="text-[8px] font-bold text-slate-500 uppercase tracking-tighter mt-1">SEC</span>
                            </div>
                            <div class="ml-1 pl-2 border-l border-slate-700">
                                <span class="text-xs font-black text-white italic" x-text="ampm"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <script>
    function dashboardWidgets() {
        return {
            hours: '00',
            minutes: '00',
            seconds: '00',
            ampm: 'AM',
            day: '01',
            month: 'JAN',
            year: '2024',
            weekday: 'MONDAY',
            
            init() {
                this.update();
                setInterval(() => this.update(), 1000);
            },
            
            update() {
                const now = new Date();
                
                // Update Time
                let h = now.getHours();
                this.ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12;
                h = h ? h : 12;
                this.hours = String(h).padStart(2, '0');
                this.minutes = String(now.getMinutes()).padStart(2, '0');
                this.seconds = String(now.getSeconds()).padStart(2, '0');
                
                // Update Date
                this.day = String(now.getDate()).padStart(2, '0');
                this.month = now.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                this.year = now.getFullYear();
                this.weekday = now.toLocaleString('en-US', { weekday: 'long' }).toUpperCase();
            }
        }
    }
    </script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Grid - Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Sales -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <div class="flex items-center">
                        <div class="p-4 rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Today's Sales</h4>
                            <p class="text-2xl font-black text-slate-900">৳{{ number_format($stats['total_sales'], 0) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Orders Today -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <div class="flex items-center">
                        <div class="p-4 rounded-xl bg-green-50 text-green-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Orders Today</h4>
                            <p class="text-2xl font-black text-slate-900">{{ $stats['orders_today'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Styles -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <div class="flex items-center">
                        <div class="p-4 rounded-xl bg-blue-50 text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Styles</h4>
                            <p class="text-2xl font-black text-slate-900">{{ $stats['total_products'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alerts -->

                <!-- Outlet Stock -->
                 <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <div class="flex items-center">
                        <div class="p-4 rounded-xl bg-teal-50 text-teal-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Outlet Stock</h4>
                            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total_stock'], 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid - Row 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Sold Items -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <div class="flex items-center">
                        <div class="p-4 rounded-xl bg-orange-50 text-orange-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Items Sold</h4>
                            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total_sold'], 0) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sales Report Link -->
                <a href="{{ route('reports.sales') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-all flex items-center group">
                    <div class="p-4 rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest group-hover:text-purple-600 transition-colors">View Report</h4>
                        <p class="text-xl font-black text-slate-900">Sales Report</p>
                    </div>
                </a>


                <!-- POS Terminal Quick Link -->
                <a href="{{ route('pos.index') }}" class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg shadow-emerald-200 p-6 border border-emerald-400 hover:scale-[1.02] transition-transform group">
                    <div class="flex items-center">
                        <div class="p-4 rounded-xl bg-white/20 text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xs font-black text-emerald-50 uppercase tracking-widest">Quick Launch</h4>
                            <p class="text-xl font-black text-white">POS Terminal</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Weekly Sales Graph -->
            <div class="mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-black text-slate-800 uppercase tracking-tight">Weekly Sales Revenue</h3>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">Performance over the last 7 days</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="flex items-center gap-1.5 text-xs font-black text-teal-600 bg-teal-50 px-2.5 py-1 rounded-lg">
                                    <span class="w-2 h-2 bg-teal-500 rounded-full animate-pulse"></span>
                                    LIVE DATA
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="h-[300px] w-full">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Orders -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                        <h3 class="font-black text-slate-800 uppercase tracking-tight">Recent Orders</h3>
                        <a href="{{ route('orders.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-800 uppercase tracking-widest">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-slate-400 font-black uppercase tracking-widest bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-4">Order ID</th>
                                    <th class="px-6 py-4">Customer</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($recent_orders as $order)
                                    <tr class="hover:bg-slate-50/50 cursor-pointer transition-colors" onclick="window.location='{{ route('orders.show', $order) }}'">
                                        <td class="px-6 py-4 font-bold text-slate-900">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800">{{ $order->customer_name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $order->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full 
                                                {{ $order->status === 'Completed' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-black text-slate-900 text-right">৳{{ number_format($order->total_amount, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                                            No recent orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 h-fit">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight mb-6">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('pos.index') }}" class="group flex items-center justify-between p-4 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition shadow-lg shadow-teal-600/20">
                            <span>Open POS Terminal</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                        <a href="{{ route('stock.intake.terminal') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-100 rounded-xl font-bold transition">
                            <span>Bulk Stock Intake</span>
                            <span class="text-slate-400">📦</span>
                        </a>
                        <a href="{{ route('products.create') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-100 rounded-xl font-bold transition">
                            <span>Add New product</span>
                            <span class="text-slate-400">➕</span>
                        </a>
                        <a href="{{ route('attributes.index') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-100 rounded-xl font-bold transition">
                            <span>Manage Attributes</span>
                            <span class="text-slate-400">⚙️</span>
                        </a>
                        <a href="{{ route('reports.sales') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-100 rounded-xl font-bold transition">
                            <span>Sales Report</span>
                            <span class="text-slate-400">📊</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            // Create gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(20, 184, 166, 0.2)'); // teal-500 with opacity
            gradient.addColorStop(1, 'rgba(20, 184, 166, 0)');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chart_data['labels']) !!},
                    datasets: [{
                        label: 'Sales Revenue',
                        data: {!! json_encode($chart_data['datasets']) !!},
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: '#14b8a6', // teal-500
                        borderWidth: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#14b8a6',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.4, // smooth curves
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 14, weight: 'black' },
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return ' ৳ ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#94a3b8',
                                callback: function(value) {
                                    return '৳' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                },
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
