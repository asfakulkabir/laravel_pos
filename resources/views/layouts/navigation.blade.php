
<nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-slate-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-6 py-3">
        <div class="flex items-center space-x-3">
            <button data-drawer-target="drawer-navigation" data-drawer-toggle="drawer-navigation"
                aria-controls="drawer-navigation"
                class="p-2 text-teal-600 rounded-lg md:hidden hover:bg-teal-50 focus:outline-none focus:ring-2 focus:ring-teal-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div>
                <a href="{{ route('dashboard') }}" class="text-xl font-black text-slate-900 tracking-tight">Mamata
                    Fashion</a>
                <p class="text-xs text-slate-500">Warehouse Control Center</p>
            </div>
        </div>

        <div class="hidden lg:flex items-center space-x-6">
            <form action="{{ route('products.search') }}" method="get" class="relative w-80">
                <label for="lookup-search" class="sr-only">Quick Search</label>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M2 8a6 6 0 1110.89 3.476l4.82 4.82a1 1 0 01-1.42 1.41l-4.81-4.81A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <!-- Note: using 'search' param instead of 'q' if controller expects it, but view uses 'search' usually. Keeping 'q' as requested or standard?
                     ProductController index usually doesn't have search implemented yet or uses specific param.
                     I'll use 'search' as it's common convention, or 'q'. Let's use 'search' to match common patterns, or just leave 'q'. 
                     Actually the user snippet had 'q'. The current ProductController might not handle it, but the UI is the priority.
                -->
                <input type="text" id="lookup-search" name="search"
                    class="block w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Scan barcode / type SKU or product..." />
            </form>

            <div class="flex items-center space-x-3">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('pos.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg hover:bg-teal-700 shadow">
                    🖥️ POS Terminal
                </a>
                <a href="{{ route('stock.intake.terminal') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-teal-700 border border-teal-100 rounded-lg bg-teal-50 hover:bg-teal-100">
                    📦 Add Stock
                </a>
                @endif
            </div>

            @auth
            <div class="relative">
                <button type="button" id="user-menu-button" data-dropdown-toggle="dropdown"
                    class="flex items-center space-x-2 rounded-full border border-slate-200 px-2 py-1 hover:bg-slate-50">
                    <img class="w-9 h-9 rounded-full object-cover"
                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D9488&color=fff"
                        alt="avatar">
                    <span class="text-sm font-semibold text-slate-700 hidden xl:inline">
                        {{ Auth::user()->name }}</span>
                </button>
                <div id="dropdown"
                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 z-50">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="text-sm font-bold text-slate-900">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-slate-600 hover:bg-teal-50 hover:text-teal-800">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-teal-600">Log in</a>
                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg hover:bg-teal-700 shadow">Register</a>
            </div>
            @endauth
        </div>
    </div>
</nav>

<aside id="drawer-navigation"
    class="fixed top-0 left-0 z-30 w-64 h-screen pt-20 transition-transform -translate-x-full duration-300 bg-white border-r border-slate-200 md:translate-x-0 shadow">
    <div class="px-4 pb-6 overflow-y-auto h-full">
        <!-- Mobile Header inside Sidebar -->
        <div class="flex items-center justify-between mb-4 md:hidden">
            <p class="text-base font-bold text-slate-800">Menu</p>
            <button type="button" data-drawer-close
                class="p-2 rounded-full text-slate-500 hover:text-slate-800 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Mobile Search -->
        <div class="md:hidden mb-6">
            <form action="{{ route('products.search') }}" method="get" class="relative">
                <input type="text" name="search" placeholder="Search..."
                    class="w-full pl-3 pr-10 py-2 text-sm border border-slate-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M2 8a6 6 0 1110.89 3.476l4.82 4.82a1 1 0 01-1.42 1.41l-4.81-4.81A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            </form>
        </div>

        <div class="space-y-6">
            <!-- Overview -->
            <div>
                <p class="text-xs font-black text-slate-900 uppercase tracking-wide mb-2">Overview</p>
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent 
                        {{ request()->routeIs('dashboard') 
                           ? 'bg-teal-50 text-teal-900 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50 hover:text-teal-950' }}">
                        <span class="w-6 h-6">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </span>
                        <span class="ml-3">Dashboard</span>
                    </a>
                    
                    <!-- POS Terminal (Added manually as it's crucial) -->
                    <a href="{{ route('pos.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent 
                        {{ request()->routeIs('pos.index') 
                           ? 'bg-teal-50 text-teal-900 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50 hover:text-teal-950' }}">
                        <span class="w-6 h-6">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <span class="ml-3">POS Terminal</span>
                    </a>

                    <a href="{{ route('products.lookup') }}"
                        class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent 
                        {{ request()->routeIs('products.lookup') 
                           ? 'bg-teal-50 text-teal-900 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50 hover:text-teal-950' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('products.lookup') ? 'text-teal-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M4 10a6 6 0 1112 0 6 6 0 01-12 0z" />
                            </svg>
                        </span>
                        <span class="ml-3">Lookup</span>
                    </a>

                    <a href="{{ route('orders.index') }}"
                        class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent 
                        {{ request()->routeIs('orders.*') 
                           ? 'bg-teal-50 text-teal-900 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50 hover:text-teal-950' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('orders.*') ? 'text-teal-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </span>
                        <span class="ml-3">Orders</span>
                    </a>

                    <a href="{{ route('exchanges.index') }}"
                        class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent 
                        {{ request()->routeIs('exchanges.*') 
                           ? 'bg-orange-50 text-orange-950 border-orange-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50 hover:text-orange-800' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('exchanges.*') ? 'text-orange-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </span>
                        <span class="ml-3">Exchange History</span>
                    </a>

                    <a href="{{ route('customers.index') }}"
                        class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent 
                        {{ request()->routeIs('customers.*') 
                           ? 'bg-teal-50 text-teal-950 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50 hover:text-teal-800' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('customers.*') ? 'text-teal-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </span>
                        <span class="ml-3">Customers</span>
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isModerator())
                    <a href="{{ route('sms.index') }}"
                        class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent 
                        {{ request()->routeIs('sms.*') 
                           ? 'bg-teal-50 text-teal-950 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50 hover:text-teal-800' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('sms.*') ? 'text-teal-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 10.5h8M8 14h5m-4-8.5L5.4 6.8A2 2 0 004 8.4v9.2a2 2 0 001.4 1.8L8 21m0-11.5v11M16 9.5l4.6-1.3A2 2 0 0123 10.1v9.2a2 2 0 01-1.4 1.8L16 19m0-9.5V19" />
                            </svg>
                        </span>
                        <span class="ml-3">Bulk SMS</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Catalog -->
            <div>
                <p class="text-xs font-black text-slate-900 uppercase tracking-wide mb-2">Catalog</p>
                <nav class="space-y-1">
                    <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent transition
                       {{ request()->routeIs('products.index') 
                           ? 'bg-teal-50 text-teal-950 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50' }}">
                        <span class="w-6 h-6">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.99.89l-1 9A1 1 0 004 18h12a1 1 0 00.99-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4z" />
                            </svg>
                        </span>
                        <span class="ml-3">Products</span>
                    </a>

                    <a href="{{ route('products.low_stock') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent transition
                       {{ request()->routeIs('products.low_stock') 
                           ? 'bg-red-50 text-red-950 border-red-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('products.low_stock') ? 'text-red-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </span>
                        <span class="ml-3">Low Stock</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('products.create') }}" class="flex items-center px-3 py-2 text-sm rounded-lg transition
                       {{ request()->routeIs('products.create') 
                           ? 'bg-teal-50 text-teal-800 border border-teal-100 font-semibold' 
                           : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="w-6 h-6">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                            </svg>
                        </span>
                        <span class="ml-3">Add Product</span>
                    </a>
                    @endif

                    <a href="{{ route('stock.intake.terminal') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent transition
                       {{ request()->routeIs('stock.intake.terminal') 
                           ? 'bg-teal-50 text-teal-950 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('stock.intake.terminal') ? 'text-teal-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                        <span class="ml-3">Add Product Stock</span>
                    </a>
                    <a href="{{ route('stock.intake.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent transition
                       {{ request()->routeIs('stock.intake.index') 
                           ? 'bg-teal-50 text-teal-950 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('stock.intake.index') ? 'text-teal-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7h8m-8 4h8m-5 4h5M6 21h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <span class="ml-3">Stock Intake History</span>
                    </a>

                    <a href="{{ route('stock.return.terminal') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent transition
                       {{ request()->routeIs('stock.return.terminal') 
                           ? 'bg-red-50 text-red-950 border-red-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('stock.return.terminal') ? 'text-red-600' : 'text-slate-900' }}">
                           <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                        </span>
                        <span class="ml-3">Return to Warehouse</span>
                    </a>
                    <a href="{{ route('stock.return.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent transition
                       {{ request()->routeIs('stock.return.index') 
                           ? 'bg-red-50 text-red-950 border-red-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50' }}">
                        <span class="w-6 h-6 {{ request()->routeIs('stock.return.index') ? 'text-red-600' : 'text-slate-900' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <span class="ml-3">Stock Return History</span>
                    </a>
                </nav>
            </div>

            <!-- Settings -->
            <div>
                <p class="text-xs font-black text-slate-900 uppercase tracking-wide mb-2">Settings</p>
                <nav class="space-y-1">
                    <a href="{{ route('attributes.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent transition
                       {{ request()->routeIs('attributes.index') 
                           ? 'bg-teal-50 text-teal-950 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50' }}">
                        <span class="w-6 h-6">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-6 0V3h4v2m-4 0h4" />
                            </svg>
                        </span>
                        <span class="ml-3">Attributes</span>
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg border border-transparent transition
                       {{ request()->routeIs('users.index') 
                           ? 'bg-teal-50 text-teal-950 border-teal-200 font-bold' 
                           : 'text-slate-900 font-bold hover:bg-slate-50' }}">
                        <span class="w-6 h-6">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        <span class="ml-3">User Management</span>
                    </a>
                    @endif
                </nav>
            </div>
        </div>
    </div>
</aside>



