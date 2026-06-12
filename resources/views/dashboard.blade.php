<x-app-layout>
    @push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', 'Plus Jakarta Sans', sans-serif !important; }
        .glass-card { background: rgba(255,255,255,0.9); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.7); }
        .kpi-card { border-radius: 20px; transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(79,70,229,0.15); }
        .chart-container { border-radius: 20px; }
        .status-badge { padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 600; letter-spacing: 0.02em; }
        .table-row { transition: all 0.2s; }
        .table-row:hover { background: rgba(79,70,229,0.03); }
        .progress-bar { border-radius: 100px; overflow: hidden; }
        .progress-fill { border-radius: 100px; transition: width 0.8s cubic-bezier(0.4,0,0.2,1); }
        .activity-item { transition: all 0.2s; border-left: 3px solid transparent; }
        .activity-item:hover { border-left-color: #4F46E5; background: rgba(79,70,229,0.02); }
        .sidebar-link { transition: all 0.2s; border-radius: 12px; }
        .sidebar-link:hover { background: rgba(79,70,229,0.08); color: #4F46E5; }
        .sidebar-link.active { background: #4F46E5; color: white; }
        .nav-btn { border-radius: 14px; transition: all 0.2s; }
        .nav-btn:hover { background: rgba(79,70,229,0.08); }
        .filter-btn { transition: all 0.2s; border-radius: 100px; }
        .filter-btn.active { background: #4F46E5; color: white; border-color: #4F46E5; }
        .filter-btn:hover:not(.active) { background: rgba(79,70,229,0.06); }
        .avatar-ring { border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .notification-dot { width: 8px; height: 8px; border-radius: 50%; position: absolute; top: -2px; right: -2px; border: 2px solid white; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 100px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .shimmer { background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); background-size: 200% 100%; animation: shimmer 2s infinite; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        .donut-hole { position: relative; }
        .donut-hole::after { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 60%; height: 60%; border-radius: 50%; background: white; }
        .gradient-text { background: linear-gradient(135deg, #4F46E5, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    </style>
    @endpush

    <div class="p-6 lg:p-8 max-w-[1600px] mx-auto" style="font-family: 'Inter', sans-serif;">
        
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Welcome Back, Admin 👋</h1>
                <p class="text-gray-500 mt-1.5 text-[15px]">Here's what's happening with your business today.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center bg-white border border-gray-200 rounded-2xl px-4 py-2.5 shadow-sm gap-3">
                    <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                    <span class="text-sm font-medium text-gray-700">Jun 1 - Jun 13, 2026</span>
                    <i class="fas fa-chevron-down text-gray-300 text-xs"></i>
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-2xl text-sm font-semibold shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2">
                    <i class="fas fa-download text-xs"></i>
                    Export
                </button>
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 lg:gap-5 mb-8">
            <!-- Total Revenue -->
            <div class="kpi-card bg-white p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-emerald-600 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">+12.5%</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 tracking-tight">$124,563</p>
                <p class="text-xs text-gray-400 mt-1.5 font-medium">Total Revenue</p>
                <p class="text-[11px] text-green-600 font-medium mt-1">↑ <span class="text-gray-400">vs last month $110,720</span></p>
            </div>

            <!-- Total Orders -->
            <div class="kpi-card bg-white p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-indigo-600 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">+8.2%</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 tracking-tight">2,847</p>
                <p class="text-xs text-gray-400 mt-1.5 font-medium">Total Orders</p>
                <p class="text-[11px] text-green-600 font-medium mt-1">↑ <span class="text-gray-400">+216 vs last period</span></p>
            </div>

            <!-- Total Customers -->
            <div class="kpi-card bg-white p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-purple-50 to-violet-100 flex items-center justify-center">
                        <i class="fas fa-users text-violet-600 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">+18.7%</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 tracking-tight">45,291</p>
                <p class="text-xs text-gray-400 mt-1.5 font-medium">Total Customers</p>
                <p class="text-[11px] text-green-600 font-medium mt-1">↑ <span class="text-gray-400">+7,130 new this month</span></p>
            </div>

            <!-- Total Products -->
            <div class="kpi-card bg-white p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-100 flex items-center justify-center">
                        <i class="fas fa-box text-orange-600 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">+4.3%</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 tracking-tight">12,430</p>
                <p class="text-xs text-gray-400 mt-1.5 font-medium">Total Products</p>
                <p class="text-[11px] text-green-600 font-medium mt-1">↑ <span class="text-gray-400">+520 added this month</span></p>
            </div>

            <!-- Active Deliveries -->
            <div class="kpi-card bg-white p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-cyan-50 to-teal-100 flex items-center justify-center">
                        <i class="fas fa-truck text-teal-600 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full">-2.1%</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 tracking-tight">328</p>
                <p class="text-xs text-gray-400 mt-1.5 font-medium">Active Deliveries</p>
                <p class="text-[11px] text-amber-600 font-medium mt-1">↓ <span class="text-gray-400">-7 vs yesterday</span></p>
            </div>

            <!-- Active Stores -->
            <div class="kpi-card bg-white p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-rose-50 to-pink-100 flex items-center justify-center">
                        <i class="fas fa-store text-pink-600 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-pink-600 bg-pink-50 px-2.5 py-1 rounded-full">+2.5%</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 tracking-tight">12</p>
                <p class="text-xs text-gray-400 mt-1.5 font-medium">Active Stores</p>
                <p class="text-[11px] text-green-600 font-medium mt-1">↑ <span class="text-gray-400">All stores operational</span></p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="xl:col-span-2 bg-white rounded-[20px] border border-gray-100 shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Revenue Analytics</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Monthly revenue performance overview</p>
                    </div>
                    <div class="flex items-center gap-1.5 bg-gray-50 rounded-2xl p-1 border border-gray-100">
                        <button class="filter-btn active px-4 py-1.5 text-xs font-semibold">Today</button>
                        <button class="filter-btn px-4 py-1.5 text-xs font-semibold text-gray-500">7 Days</button>
                        <button class="filter-btn px-4 py-1.5 text-xs font-semibold text-gray-500">30 Days</button>
                        <button class="filter-btn px-4 py-1.5 text-xs font-semibold text-gray-500">12 Months</button>
                    </div>
                </div>
                <div class="relative">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>

            <!-- Revenue Sources Donut -->
            <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Revenue Sources</h3>
                        <p class="text-sm text-gray-400 mt-0.5">By sales channel</p>
                    </div>
                </div>
                <div class="flex items-center justify-center">
                    <canvas id="revenueSourcesChart" height="220" width="220"></canvas>
                </div>
                <div class="grid grid-cols-3 gap-3 mt-2">
                    <div class="text-center">
                        <div class="w-3 h-3 rounded-full bg-indigo-500 mx-auto mb-1.5"></div>
                        <p class="text-xs font-semibold text-gray-900">$62,281</p>
                        <p class="text-[10px] text-gray-400">POS Sales</p>
                    </div>
                    <div class="text-center">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 mx-auto mb-1.5"></div>
                        <p class="text-xs font-semibold text-gray-900">$39,860</p>
                        <p class="text-[10px] text-gray-400">Online Store</p>
                    </div>
                    <div class="text-center">
                        <div class="w-3 h-3 rounded-full bg-violet-500 mx-auto mb-1.5"></div>
                        <p class="text-xs font-semibold text-gray-900">$22,422</p>
                        <p class="text-[10px] text-gray-400">Delivery</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory + Top Products + Delivery Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Inventory Overview -->
            <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Inventory Overview</h3>
                    <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View All →</a>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-4 border border-indigo-100/50">
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center mb-3">
                            <i class="fas fa-boxes text-indigo-600 text-sm"></i>
                        </div>
                        <p class="text-xl font-bold text-gray-900">12,430</p>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Total Products</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl p-4 border border-amber-100/50">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center mb-3">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>
                        </div>
                        <p class="text-xl font-bold text-gray-900">48</p>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Low Stock</p>
                    </div>
                    <div class="bg-gradient-to-br from-rose-50 to-red-50 rounded-2xl p-4 border border-rose-100/50">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center mb-3">
                            <i class="fas fa-times-circle text-rose-600 text-sm"></i>
                        </div>
                        <p class="text-xl font-bold text-gray-900">12</p>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Out of Stock</p>
                    </div>
                    <div class="bg-gradient-to-br from-cyan-50 to-teal-50 rounded-2xl p-4 border border-cyan-100/50">
                        <div class="w-9 h-9 rounded-xl bg-cyan-100 flex items-center justify-center mb-3">
                            <i class="fas fa-warehouse text-cyan-600 text-sm"></i>
                        </div>
                        <p class="text-xl font-bold text-gray-900">5</p>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">Warehouses</p>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Top Selling Products</h3>
                    <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View All →</a>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-mobile-alt text-indigo-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">iPhone 16 Pro Max</p>
                                <p class="text-xs text-gray-400">Electronics</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">$48,290</p>
                        </div>
                        <div class="progress-bar h-2 bg-gray-100">
                            <div class="progress-fill h-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: 85%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tshirt text-emerald-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">Classic Cotton T-Shirt</p>
                                <p class="text-xs text-gray-400">Fashion</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">$32,150</p>
                        </div>
                        <div class="progress-bar h-2 bg-gray-100">
                            <div class="progress-fill h-full bg-gradient-to-r from-green-500 to-emerald-500" style="width: 68%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shoe-prints text-orange-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">Air Max Sneakers 2026</p>
                                <p class="text-xs text-gray-400">Footwear</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">$28,740</p>
                        </div>
                        <div class="progress-bar h-2 bg-gray-100">
                            <div class="progress-fill h-full bg-gradient-to-r from-amber-500 to-orange-500" style="width: 61%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-100 to-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-headphones text-cyan-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">Sony WH-1000XM6</p>
                                <p class="text-xs text-gray-400">Audio</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">$22,380</p>
                        </div>
                        <div class="progress-bar h-2 bg-gray-100">
                            <div class="progress-fill h-full bg-gradient-to-r from-cyan-500 to-blue-500" style="width: 52%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Overview -->
            <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Delivery Overview</h3>
                    <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View All →</a>
                </div>
                <div class="flex items-center justify-center mb-4">
                    <canvas id="deliveryChart" height="180" width="180"></canvas>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500 flex-shrink-0"></div>
                        <span class="text-xs text-gray-500">Assigned</span>
                        <span class="text-xs font-semibold text-gray-800 ml-auto">45</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-amber-500 flex-shrink-0"></div>
                        <span class="text-xs text-gray-500">In Transit</span>
                        <span class="text-xs font-semibold text-gray-800 ml-auto">128</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 flex-shrink-0"></div>
                        <span class="text-xs text-gray-500">Delivered</span>
                        <span class="text-xs font-semibold text-gray-800 ml-auto">1,847</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-rose-500 flex-shrink-0"></div>
                        <span class="text-xs text-gray-500">Returned</span>
                        <span class="text-xs font-semibold text-gray-800 ml-auto">23</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders + POS Overview + Activity Feed Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Recent Orders Table -->
            <div class="lg:col-span-2 bg-white rounded-[20px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Latest 8 orders from your store</p>
                    </div>
                    <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Order ID</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-row border-b border-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900">#ORD-2847</span>
                                        <i class="fas fa-copy text-gray-300 text-xs cursor-pointer hover:text-gray-500"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-xs font-bold text-indigo-600">JD</div>
                                        <span class="text-sm font-medium text-gray-700">John Doe</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">Jun 13, 2026</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">$349.00</td>
                                <td class="px-6 py-4"><span class="status-badge bg-emerald-50 text-emerald-700 border border-emerald-200">Completed</span></td>
                                <td class="px-6 py-4"><span class="status-badge bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span></td>
                            </tr>
                            <tr class="table-row border-b border-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900">#ORD-2846</span>
                                        <i class="fas fa-copy text-gray-300 text-xs cursor-pointer hover:text-gray-500"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-100 to-pink-100 flex items-center justify-center text-xs font-bold text-rose-600">SM</div>
                                        <span class="text-sm font-medium text-gray-700">Sarah Miller</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">Jun 13, 2026</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">$1,299.00</td>
                                <td class="px-6 py-4"><span class="status-badge bg-blue-50 text-blue-700 border border-blue-200">Processing</span></td>
                                <td class="px-6 py-4"><span class="status-badge bg-amber-50 text-amber-700 border border-amber-200">Pending</span></td>
                            </tr>
                            <tr class="table-row border-b border-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900">#ORD-2845</span>
                                        <i class="fas fa-copy text-gray-300 text-xs cursor-pointer hover:text-gray-500"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-100 to-blue-100 flex items-center justify-center text-xs font-bold text-cyan-600">MK</div>
                                        <span class="text-sm font-medium text-gray-700">Michael Kim</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">Jun 12, 2026</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">$89.99</td>
                                <td class="px-6 py-4"><span class="status-badge bg-emerald-50 text-emerald-700 border border-emerald-200">Completed</span></td>
                                <td class="px-6 py-4"><span class="status-badge bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span></td>
                            </tr>
                            <tr class="table-row border-b border-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900">#ORD-2844</span>
                                        <i class="fas fa-copy text-gray-300 text-xs cursor-pointer hover:text-gray-500"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center text-xs font-bold text-amber-600">EJ</div>
                                        <span class="text-sm font-medium text-gray-700">Emily Johnson</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">Jun 12, 2026</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">$567.00</td>
                                <td class="px-6 py-4"><span class="status-badge bg-rose-50 text-rose-700 border border-rose-200">Cancelled</span></td>
                                <td class="px-6 py-4"><span class="status-badge bg-rose-50 text-rose-700 border border-rose-200">Refunded</span></td>
                            </tr>
                            <tr class="table-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900">#ORD-2843</span>
                                        <i class="fas fa-copy text-gray-300 text-xs cursor-pointer hover:text-gray-500"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center text-xs font-bold text-green-600">DW</div>
                                        <span class="text-sm font-medium text-gray-700">David Wilson</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">Jun 11, 2026</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">$2,199.00</td>
                                <td class="px-6 py-4"><span class="status-badge bg-amber-50 text-amber-700 border border-amber-200">Pending</span></td>
                                <td class="px-6 py-4"><span class="status-badge bg-amber-50 text-amber-700 border border-amber-200">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- POS Overview -->
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900">POS Overview</h3>
                        <i class="fas fa-cash-register text-indigo-600 text-xl"></i>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center mb-2">
                                <i class="fas fa-desktop text-indigo-600 text-xs"></i>
                            </div>
                            <p class="text-lg font-bold text-gray-900">6</p>
                            <p class="text-[11px] text-gray-500 font-medium">Open Registers</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center mb-2">
                                <i class="fas fa-clock text-green-600 text-xs"></i>
                            </div>
                            <p class="text-lg font-bold text-gray-900">12</p>
                            <p class="text-[11px] text-gray-500 font-medium">Active Shifts</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mb-2">
                                <i class="fas fa-receipt text-blue-600 text-xs"></i>
                            </div>
                            <p class="text-lg font-bold text-gray-900">47</p>
                            <p class="text-[11px] text-gray-500 font-medium">Today's POS Orders</p>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center mb-2">
                                <i class="fas fa-money-bill-wave text-amber-600 text-xs"></i>
                            </div>
                            <p class="text-lg font-bold text-gray-900">$8,430</p>
                            <p class="text-[11px] text-gray-500 font-medium">Cash Collection</p>
                        </div>
                    </div>
                </div>

                <!-- Sales Goal Widget -->
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-gray-900">Monthly Sales Goal</h3>
                        <span class="text-xs font-semibold text-indigo-600">$124,563 / $180,000</span>
                    </div>
                    <div class="progress-bar h-3 bg-gray-100 mb-3">
                        <div class="progress-fill h-full bg-gradient-to-r from-indigo-500 to-violet-500" style="width: 69%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-400 font-medium">69% Completed</span>
                        <span class="text-gray-400 font-medium">$55,437 remaining</span>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Days Remaining</span>
                            <span class="font-bold text-gray-900">17 Days</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Activity Feed</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Real-time updates from your business</p>
                </div>
                <button class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 bg-indigo-50 px-4 py-2 rounded-full transition-all">View All Activity</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                <div class="activity-item p-3 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-shopping-bag text-emerald-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900">New Order Received</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">Order #ORD-2848 from John Doe - $349.00</p>
                            <p class="text-[10px] text-gray-300 mt-1 font-medium">2 minutes ago</p>
                        </div>
                    </div>
                </div>
                <div class="activity-item p-3 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-credit-card text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900">Payment Completed</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">Payment of $1,299 from Sarah Miller confirmed</p>
                            <p class="text-[10px] text-gray-300 mt-1 font-medium">15 minutes ago</p>
                        </div>
                    </div>
                </div>
                <div class="activity-item p-3 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-truck text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900">Delivery Updated</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">Package #DHL-4821 is now out for delivery</p>
                            <p class="text-[10px] text-gray-300 mt-1 font-medium">28 minutes ago</p>
                        </div>
                    </div>
                </div>
                <div class="activity-item p-3 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900">Low Stock Alert</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">Apple iPhone 16 Pro is below minimum stock</p>
                            <p class="text-[10px] text-gray-300 mt-1 font-medium">1 hour ago</p>
                        </div>
                    </div>
                </div>
                <div class="activity-item p-3 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-user-plus text-purple-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900">New Customer</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">Jessica Williams just created an account</p>
                            <p class="text-[10px] text-gray-300 mt-1 font-medium">2 hours ago</p>
                        </div>
                    </div>
                </div>
                <div class="activity-item p-3 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-undo-alt text-rose-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900">Return Requested</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">Return #RET-129 initiated by Emily Johnson</p>
                            <p class="text-[10px] text-gray-300 mt-1 font-medium">3 hours ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Common chart config
        Chart.defaults.font.family = "'Inter', 'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#94a3b8';

        // Revenue Line Chart
        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        const gradient1 = ctx1.createLinearGradient(0, 0, 0, 250);
        gradient1.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
        gradient1.addColorStop(0.5, 'rgba(79, 70, 229, 0.08)');
        gradient1.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue 2026',
                    data: [28500, 32000, 29800, 35000, 38000, 42000, 39000, 45000, 48000, 52000, 49000, 55000],
                    borderColor: '#4F46E5',
                    backgroundColor: gradient1,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4F46E5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                }, {
                    label: 'Revenue 2025',
                    data: [22000, 25000, 24000, 28000, 30000, 32000, 31000, 35000, 37000, 40000, 38000, 42000],
                    borderColor: '#94a3b8',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [6, 4],
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: '#94a3b8',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: { 
                            padding: 10,
                            callback: function(value) { return '$' + value.toLocaleString(); }
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { padding: 10 },
                        border: { display: false }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Revenue Sources Donut Chart
        new Chart(document.getElementById('revenueSourcesChart'), {
            type: 'doughnut',
            data: {
                labels: ['POS Sales', 'Online Store', 'Delivery Orders'],
                datasets: [{
                    data: [50, 32, 18],
                    backgroundColor: ['#4F46E5', '#10B981', '#7C3AED'],
                    borderWidth: 0,
                    hoverOffset: 12,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });

        // Delivery Overview Donut Chart
        new Chart(document.getElementById('deliveryChart'), {
            type: 'doughnut',
            data: {
                labels: ['Assigned', 'In Transit', 'Delivered', 'Returned'],
                datasets: [{
                    data: [45, 128, 1847, 23],
                    backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#F43F5E'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 12,
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>