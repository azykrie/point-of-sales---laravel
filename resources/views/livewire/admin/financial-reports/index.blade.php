<div>
    <div class="flex items-center justify-between mb-6">
        <div class="text-2xl font-semibold">Financial Report</div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <flux:input type="date" wire:model.live="dateFrom" label="From Date" />
            </div>
            <div>
                <flux:input type="date" wire:model.live="dateTo" label="To Date" />
            </div>
            <div>
                <flux:button variant="ghost" wire:click="$set('dateFrom', '{{ now()->startOfMonth()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->format('Y-m-d') }}')" icon="calendar">
                    This Month
                </flux:button>
            </div>
            <div>
                <flux:button variant="ghost" wire:click="$set('dateFrom', '{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}')" icon="calendar">
                    Last Month
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/40 rounded-lg">
                    <flux:icon.shopping-cart class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Total Sales</div>
                    <div class="text-lg font-bold">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                    <flux:icon.receipt-percent class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Transactions</div>
                    <div class="text-lg font-bold">{{ $summary['total_transactions'] }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/40 rounded-lg">
                    <flux:icon.arrow-down-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Total Income</div>
                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-100 dark:bg-red-900/40 rounded-lg">
                    <flux:icon.arrow-up-circle class="w-5 h-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Total Expense</div>
                    <div class="text-lg font-bold text-red-600">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                    <flux:icon.banknotes class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Net Profit</div>
                    <div class="text-lg font-bold {{ $summary['net_profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Download Reports</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Sales Report -->
            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.shopping-cart class="w-5 h-5 text-purple-600" />
                    <span class="font-medium">Sales Report</span>
                </div>
                <p class="text-sm text-zinc-500 mb-3">Download all sales transactions for the selected period.</p>
                <div class="flex gap-2">
                    <flux:button size="sm" wire:click="downloadSales('csv')" icon="document-arrow-down">
                        CSV
                    </flux:button>
                    <flux:button size="sm" variant="primary" wire:click="downloadSales('xlsx')" icon="table-cells">
                        Excel
                    </flux:button>
                </div>
            </div>

            <!-- Income Report -->
            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.arrow-down-circle class="w-5 h-5 text-green-600" />
                    <span class="font-medium">Income Report</span>
                </div>
                <p class="text-sm text-zinc-500 mb-3">Download all income (money in) for the selected period.</p>
                <div class="flex gap-2">
                    <flux:button size="sm" wire:click="downloadIncome('csv')" icon="document-arrow-down">
                        CSV
                    </flux:button>
                    <flux:button size="sm" variant="primary" wire:click="downloadIncome('xlsx')" icon="table-cells">
                        Excel
                    </flux:button>
                </div>
            </div>

            <!-- Expense Report -->
            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.arrow-up-circle class="w-5 h-5 text-red-600" />
                    <span class="font-medium">Expense Report</span>
                </div>
                <p class="text-sm text-zinc-500 mb-3">Download all expenses (money out) for the selected period.</p>
                <div class="flex gap-2">
                    <flux:button size="sm" wire:click="downloadExpense('csv')" icon="document-arrow-down">
                        CSV
                    </flux:button>
                    <flux:button size="sm" variant="primary" wire:click="downloadExpense('xlsx')" icon="table-cells">
                        Excel
                    </flux:button>
                </div>
            </div>

            <!-- All Reports -->
            <div class="border-2 border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon.document-chart-bar class="w-5 h-5 text-blue-600" />
                    <span class="font-medium">Complete Report</span>
                </div>
                <p class="text-sm text-zinc-500 mb-3">Download all reports combined in one file.</p>
                <div class="flex gap-2">
                    <flux:button size="sm" wire:click="downloadAll('csv')" icon="document-arrow-down">
                        CSV
                    </flux:button>
                    <flux:button size="sm" variant="primary" wire:click="downloadAll('xlsx')" icon="table-cells">
                        Excel
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Recent Sales ({{ $sales->count() }} transactions)</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-2 px-3">Invoice</th>
                        <th class="text-left py-2 px-3">Date</th>
                        <th class="text-left py-2 px-3">Customer</th>
                        <th class="text-left py-2 px-3">Payment</th>
                        <th class="text-right py-2 px-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales->take(10) as $sale)
                        <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                            <td class="py-2 px-3 font-mono text-xs">{{ $sale->invoice_number }}</td>
                            <td class="py-2 px-3">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-3">{{ $sale->customer_name }}</td>
                            <td class="py-2 px-3">
                                <flux:badge size="sm" color="{{ $sale->payment_method === 'cash' ? 'green' : ($sale->payment_method === 'qris' ? 'purple' : 'blue') }}">
                                    {{ strtoupper($sale->payment_method) }}
                                </flux:badge>
                            </td>
                            <td class="py-2 px-3 text-right font-medium">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500">No sales found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->count() > 10)
            <div class="mt-3 text-sm text-zinc-500">Showing 10 of {{ $sales->count() }} transactions. Download report for complete data.</div>
        @endif
    </div>

    <!-- Income & Expense Tables Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Income Table -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.arrow-down-circle class="w-5 h-5 text-green-600" />
                Income ({{ $income->count() }} records)
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="text-left py-2 px-2">Date</th>
                            <th class="text-left py-2 px-2">Category</th>
                            <th class="text-right py-2 px-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($income->take(5) as $item)
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-2 px-2">{{ $item->transaction_date->format('d/m/Y') }}</td>
                                <td class="py-2 px-2">
                                    <flux:badge size="sm" color="green">{{ $item->category_label }}</flux:badge>
                                </td>
                                <td class="py-2 px-2 text-right font-medium text-green-600">
                                    +Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-zinc-500">No income found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expense Table -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <flux:icon.arrow-up-circle class="w-5 h-5 text-red-600" />
                Expense ({{ $expense->count() }} records)
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="text-left py-2 px-2">Date</th>
                            <th class="text-left py-2 px-2">Category</th>
                            <th class="text-right py-2 px-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expense->take(5) as $item)
                            <tr class="border-b border-zinc-100 dark:border-zinc-700/50">
                                <td class="py-2 px-2">{{ $item->transaction_date->format('d/m/Y') }}</td>
                                <td class="py-2 px-2">
                                    <flux:badge size="sm" color="red">{{ $item->category_label }}</flux:badge>
                                </td>
                                <td class="py-2 px-2 text-right font-medium text-red-600">
                                    -Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-zinc-500">No expense found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
