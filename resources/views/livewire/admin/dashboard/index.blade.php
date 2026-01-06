<div>
    <div class="flex items-center justify-between mb-6">
        <div class="text-2xl font-semibold">Dashboard</div>
        <div class="text-sm text-zinc-500">{{ now()->format('l, d F Y') }}</div>
    </div>

    <!-- Today Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <flux:icon.shopping-cart class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-blue-100">Today's Sales</div>
                    <div class="text-xl font-bold">{{ $todayStats['sales_count'] }} transactions</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <flux:icon.currency-dollar class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-purple-100">Today's Revenue</div>
                    <div class="text-xl font-bold">Rp {{ number_format($todayStats['sales_amount'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <flux:icon.arrow-down-circle class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-green-100">Today's Income</div>
                    <div class="text-xl font-bold">Rp {{ number_format($todayStats['income'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <flux:icon.arrow-up-circle class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-red-100">Today's Expense</div>
                    <div class="text-xl font-bold">Rp {{ number_format($todayStats['expense'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <flux:icon.banknotes class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-amber-100">Today's Profit</div>
                    <div class="text-xl font-bold">Rp {{ number_format($todayStats['profit'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month Stats -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 mb-6">
        <h3 class="text-sm font-medium text-zinc-500 mb-3">This Month Overview ({{ now()->format('F Y') }})</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div>
                <div class="text-xs text-zinc-500">Transactions</div>
                <div class="text-lg font-bold">{{ $monthStats['sales_count'] }}</div>
            </div>
            <div>
                <div class="text-xs text-zinc-500">Sales Revenue</div>
                <div class="text-lg font-bold">Rp {{ number_format($monthStats['sales_amount'], 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-xs text-zinc-500">Total Income</div>
                <div class="text-lg font-bold text-green-600">Rp {{ number_format($monthStats['income'], 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-xs text-zinc-500">Total Expense</div>
                <div class="text-lg font-bold text-red-600">Rp {{ number_format($monthStats['expense'], 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-xs text-zinc-500">Net Profit</div>
                <div class="text-lg font-bold {{ $monthStats['profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    Rp {{ number_format($monthStats['profit'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top 5 Best Selling Products -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.arrow-trending-up class="w-5 h-5 text-green-500" />
                Top 5 Best Selling Products
            </h3>
            
            @if($topProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($topProducts as $index => $product)
                        @php
                            $maxSold = $topProducts->max('total_sold');
                            $percentage = $maxSold > 0 ? ($product->total_sold / $maxSold) * 100 : 0;
                            $colors = ['bg-green-500', 'bg-blue-500', 'bg-purple-500', 'bg-amber-500', 'bg-pink-500'];
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium truncate flex-1">{{ $index + 1 }}. {{ $product->product_name }}</span>
                                <span class="text-zinc-500 ml-2">{{ $product->total_sold }} sold</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                                    <div class="{{ $colors[$index] }} h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs text-zinc-500 w-24 text-right">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-zinc-500">
                    <flux:icon.chart-bar class="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>No sales data yet</p>
                </div>
            @endif
        </div>

        <!-- Top 5 Least Selling Products -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.arrow-trending-down class="w-5 h-5 text-red-500" />
                Top 5 Least Selling Products
            </h3>
            
            @if($leastProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($leastProducts as $index => $product)
                        @php
                            $maxSold = $topProducts->max('total_sold') ?: 1;
                            $percentage = ($product->total_sold / $maxSold) * 100;
                            $colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-lime-500', 'bg-teal-500'];
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium truncate flex-1">{{ $index + 1 }}. {{ $product->product_name }}</span>
                                <span class="text-zinc-500 ml-2">{{ $product->total_sold }} sold</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                                    <div class="{{ $colors[$index] }} h-3 rounded-full transition-all duration-500" style="width: {{ max($percentage, 3) }}%"></div>
                                </div>
                                <span class="text-xs text-zinc-500 w-24 text-right">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-zinc-500">
                    <flux:icon.chart-bar class="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>No products found</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Category Sales & Weekly Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Sales by Category -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.tag class="w-5 h-5 text-purple-500" />
                Revenue by Category
            </h3>
            
            @if($categorySales->count() > 0)
                @php
                    $totalRevenue = $categorySales->sum('total_revenue');
                    $categoryColors = ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444', '#EC4899', '#06B6D4', '#84CC16'];
                @endphp
                
                <!-- Pie Chart Visualization -->
                <div class="flex items-center gap-6">
                    <div class="relative w-32 h-32">
                        <svg viewBox="0 0 36 36" class="w-32 h-32 transform -rotate-90">
                            @php
                                $offset = 0;
                            @endphp
                            @foreach($categorySales as $index => $category)
                                @php
                                    $percentage = $totalRevenue > 0 ? ($category->total_revenue / $totalRevenue) * 100 : 0;
                                    $dashArray = $percentage . ' ' . (100 - $percentage);
                                @endphp
                                <circle
                                    cx="18" cy="18" r="15.915"
                                    fill="transparent"
                                    stroke="{{ $categoryColors[$index % count($categoryColors)] }}"
                                    stroke-width="3"
                                    stroke-dasharray="{{ $dashArray }}"
                                    stroke-dashoffset="-{{ $offset }}"
                                />
                                @php
                                    $offset += $percentage;
                                @endphp
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-xs text-zinc-500">Total</div>
                                <div class="text-sm font-bold">{{ $categorySales->count() }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 space-y-2">
                        @foreach($categorySales->take(5) as $index => $category)
                            @php
                                $percentage = $totalRevenue > 0 ? ($category->total_revenue / $totalRevenue) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $categoryColors[$index % count($categoryColors)] }}"></div>
                                <span class="text-sm flex-1 truncate">{{ $category->category_name }}</span>
                                <span class="text-xs text-zinc-500">{{ number_format($percentage, 1) }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Category Table -->
                <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-zinc-500">
                                <th class="text-left py-1">Category</th>
                                <th class="text-right py-1">Sold</th>
                                <th class="text-right py-1">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categorySales as $category)
                                <tr class="border-t border-zinc-100 dark:border-zinc-700/50">
                                    <td class="py-2">{{ $category->category_name }}</td>
                                    <td class="py-2 text-right">{{ $category->total_sold }}</td>
                                    <td class="py-2 text-right font-medium">Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-zinc-500">
                    <flux:icon.tag class="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>No category sales data yet</p>
                </div>
            @endif
        </div>

        <!-- Weekly Sales Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.chart-bar class="w-5 h-5 text-blue-500" />
                Last 7 Days Sales
            </h3>
            
            @php
                $maxAmount = $weeklySales->max('amount') ?: 1;
            @endphp
            
            <div class="flex items-end justify-between gap-2 h-48">
                @foreach($weeklySales as $day)
                    @php
                        $height = ($day['amount'] / $maxAmount) * 100;
                        $isToday = $loop->last;
                    @endphp
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full flex flex-col items-center justify-end h-36">
                            <div class="text-xs text-zinc-500 mb-1">
                                @if($day['amount'] > 0)
                                    {{ number_format($day['amount'] / 1000, 0) }}K
                                @endif
                            </div>
                            <div 
                                class="w-full max-w-12 rounded-t-md transition-all duration-500 {{ $isToday ? 'bg-blue-500' : 'bg-zinc-300 dark:bg-zinc-600' }}"
                                style="height: {{ max($height, 5) }}%"
                            ></div>
                        </div>
                        <div class="text-xs mt-2 {{ $isToday ? 'font-bold text-blue-500' : 'text-zinc-500' }}">
                            {{ $day['date'] }}
                        </div>
                        <div class="text-xs text-zinc-400">
                            {{ $day['full_date'] }}
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Total (7 days)</span>
                    <span class="font-bold">Rp {{ number_format($weeklySales->sum('amount'), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm mt-1">
                    <span class="text-zinc-500">Daily Average</span>
                    <span class="font-medium">Rp {{ number_format($weeklySales->avg('amount'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <flux:button href="{{ route('admin.cashiers.index') }}" wire:navigate icon="shopping-cart" variant="primary">
                Open Cashier
            </flux:button>
            <flux:button href="{{ route('admin.products.create') }}" wire:navigate icon="plus">
                Add Product
            </flux:button>
            <flux:button href="{{ route('admin.cash-flows.index') }}" wire:navigate icon="banknotes">
                Cash Flow
            </flux:button>
            <flux:button href="{{ route('admin.financial-reports.index') }}" wire:navigate icon="document-chart-bar">
                Financial Report
            </flux:button>
        </div>
    </div>
</div>
