<div>
    <div class="mb-6">
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:text>Welcome back, {{ auth()->user()->name }}! Here's your performance overview.</flux:text>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Today's Sales -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="sm" class="text-zinc-500">Today's Sales</flux:text>
                    <div class="text-2xl font-bold font-mono mt-1">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.banknotes class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <!-- Today's Transactions -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="sm" class="text-zinc-500">Today's Transactions</flux:text>
                    <div class="text-2xl font-bold mt-1">{{ $todayTransactions }}</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.receipt-percent class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <!-- Items Sold Today -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="sm" class="text-zinc-500">Items Sold Today</flux:text>
                    <div class="text-2xl font-bold mt-1">{{ $todayItemsSold }}</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.shopping-bag class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>

        <!-- This Month Sales -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="sm" class="text-zinc-500">This Month</flux:text>
                    <div class="text-2xl font-bold font-mono mt-1">Rp {{ number_format($monthSales, 0, ',', '.') }}</div>
                    <flux:text size="sm" class="text-zinc-400">{{ $monthTransactions }} transactions</flux:text>
                </div>
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.calendar class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Chart (Last 7 Days) -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <flux:heading size="sm" class="mb-4">Your Sales (Last 7 Days)</flux:heading>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Payment Breakdown Today -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <flux:heading size="sm" class="mb-4">Today's Payment Methods</flux:heading>
            @if($paymentBreakdown->count() > 0)
                <div class="space-y-3">
                    @foreach($paymentBreakdown as $payment)
                        @php
                            $colors = [
                                'cash' => 'bg-green-500',
                                'qris' => 'bg-blue-500',
                                'transfer' => 'bg-purple-500',
                            ];
                            $percentage = $todaySales > 0 ? ($payment->total / $todaySales) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium capitalize">{{ $payment->payment_method }}</span>
                                <span class="text-sm text-zinc-500">{{ $payment->count }} txn</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                    <div class="{{ $colors[$payment->payment_method] ?? 'bg-zinc-500' }} h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs font-mono text-zinc-500">Rp {{ number_format($payment->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon.chart-pie class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-2" />
                    <flux:text class="text-zinc-500">No transactions today</flux:text>
                </div>
            @endif

            <!-- Quick Action -->
            <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button variant="primary" class="w-full" href="{{ route('cashier.pos.index') }}" wire:navigate icon="plus">
                    New Transaction
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mt-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="sm">Recent Transactions</flux:heading>
            <flux:button variant="ghost" size="sm" href="{{ route('cashier.sales.index') }}" wire:navigate icon-trailing="arrow-right">
                View All
            </flux:button>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Invoice</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Items</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Payment</flux:table.column>
                <flux:table.column>Time</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($recentTransactions as $transaction)
                    <flux:table.row>
                        <flux:table.cell>
                            <span class="font-mono text-sm">{{ $transaction->invoice_number }}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{ $transaction->customer_name }}</flux:table.cell>
                        <flux:table.cell>{{ $transaction->items->sum('quantity') }} items</flux:table.cell>
                        <flux:table.cell class="font-mono font-medium">
                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($transaction->payment_method === 'cash')
                                <flux:badge color="green" size="sm">CASH</flux:badge>
                            @elseif ($transaction->payment_method === 'qris')
                                <flux:badge color="blue" size="sm">QRIS</flux:badge>
                            @else
                                <flux:badge color="purple" size="sm">TRANSFER</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-500">
                            {{ $transaction->created_at->diffForHumans() }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8">
                            <flux:icon.inbox class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-2" />
                            <flux:text>No transactions yet</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>

@script
<script>
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Sales (Rp)',
                    data: @json($chartData),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y'
                }, {
                    label: 'Transactions',
                    data: @json($chartTransactions),
                    type: 'line',
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.3,
                    fill: false,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return 'Sales: Rp ' + context.raw.toLocaleString('id-ID');
                                }
                                return 'Transactions: ' + context.raw;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endscript
