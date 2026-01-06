<div>
    <div class="flex items-center justify-between mb-6">
        <div class="text-2xl font-semibold">Cash Flow</div>
        <div class="flex items-center gap-2">
            <flux:modal.trigger name="cash-flow-form">
                <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
                    Add Transaction
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/40 rounded-lg">
                    <flux:icon.arrow-down-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <div class="text-sm text-green-600 dark:text-green-400">Total Income</div>
                    <div class="text-xl font-bold text-green-700 dark:text-green-300">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-100 dark:bg-red-900/40 rounded-lg">
                    <flux:icon.arrow-up-circle class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <div class="text-sm text-red-600 dark:text-red-400">Total Expense</div>
                    <div class="text-xl font-bold text-red-700 dark:text-red-300">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                    <flux:icon.banknotes class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <div class="text-sm text-blue-600 dark:text-blue-400">Balance</div>
                    <div class="text-xl font-bold {{ $balance >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-red-700 dark:text-red-300' }}">
                        Rp {{ number_format($balance, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <flux:dropdown>
            <flux:button variant="ghost" icon="funnel" icon-trailing="chevron-down">
                Filter
                @if($filterType || $filterCategory || $filterDateFrom || $filterDateTo)
                    <flux:badge color="blue" size="sm">!</flux:badge>
                @endif
            </flux:button>
            <flux:menu>
                <flux:menu.submenu heading="Type">
                    <flux:menu.radio.group wire:model.live="filterType">
                        <flux:menu.radio value="">All</flux:menu.radio>
                        <flux:menu.radio value="income">Income</flux:menu.radio>
                        <flux:menu.radio value="expense">Expense</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu.submenu>
                <flux:menu.submenu heading="Category">
                    <flux:menu.radio.group wire:model.live="filterCategory">
                        <flux:menu.radio value="">All Categories</flux:menu.radio>
                        @foreach($allCategories as $key => $label)
                            <flux:menu.radio value="{{ $key }}">{{ $label }}</flux:menu.radio>
                        @endforeach
                    </flux:menu.radio.group>
                </flux:menu.submenu>
                <flux:menu.separator />
                <flux:menu.item wire:click="resetFilters" icon="x-mark" variant="danger">Reset Filters</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
        
        <div class="flex items-center gap-2">
            <span class="text-sm text-zinc-500">From:</span>
            <flux:input type="date" wire:model.live="filterDateFrom" />
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-zinc-500">To:</span>
            <flux:input type="date" wire:model.live="filterDateTo" />
        </div>
        <flux:input wire:model.live="search" placeholder="Search reference/description..." icon="magnifying-glass" class="w-64" />
    </div>

    <!-- Table -->
    <flux:table :paginate="$cashFlows">
        <flux:table.columns>
            <flux:table.column>Reference</flux:table.column>
            <flux:table.column>Date</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Category</flux:table.column>
            <flux:table.column>Description</flux:table.column>
            <flux:table.column>Amount</flux:table.column>
            <flux:table.column>Recorded By</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($cashFlows as $cashFlow)
                <flux:table.row>
                    <flux:table.cell>
                        <span class="font-mono text-sm">{{ $cashFlow->reference_number }}</span>
                        @if($cashFlow->sale_id)
                            <div class="text-xs text-zinc-500">{{ $cashFlow->sale?->invoice_number }}</div>
                        @elseif($cashFlow->refund_id)
                            <div class="text-xs text-zinc-500">Refund #{{ $cashFlow->refund_id }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div>{{ $cashFlow->transaction_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-zinc-500">{{ $cashFlow->created_at->format('H:i') }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($cashFlow->type === 'income')
                            <flux:badge color="green" size="sm">
                                <flux:icon.arrow-down class="w-3 h-3 mr-1" />
                                In
                            </flux:badge>
                        @else
                            <flux:badge color="red" size="sm">
                                <flux:icon.arrow-up class="w-3 h-3 mr-1" />
                                Out
                            </flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="zinc">{{ $cashFlow->category_label }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="max-w-xs truncate">{{ $cashFlow->description }}</div>
                        @if($cashFlow->notes)
                            <div class="text-xs text-zinc-500 truncate">{{ $cashFlow->notes }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="font-medium">
                        <span class="{{ $cashFlow->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $cashFlow->type === 'income' ? '+' : '-' }} Rp {{ number_format($cashFlow->amount, 0, ',', '.') }}
                        </span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="text-sm">{{ $cashFlow->user?->name ?? '-' }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if(!$cashFlow->sale_id && !$cashFlow->refund_id)
                            <div class="flex items-center gap-1">
                                <flux:modal.trigger name="cash-flow-form">
                                    <flux:button size="xs" wire:click="openEditModal({{ $cashFlow->id }})" icon="pencil" />
                                </flux:modal.trigger>
                                <flux:modal.trigger name="delete-cash-flow">
                                    <flux:button size="xs" variant="danger" wire:click="confirmDelete({{ $cashFlow->id }})" icon="trash" />
                                </flux:modal.trigger>
                            </div>
                        @else
                            <flux:badge size="sm" color="zinc">Auto</flux:badge>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-8">
                        <flux:icon.banknotes class="w-12 h-12 mx-auto text-zinc-300 mb-2" />
                        <flux:text>No cash flow transactions found.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <!-- Create/Edit Modal -->
    <flux:modal name="cash-flow-form" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editMode ? 'Edit' : 'Add' }} Cash Flow</flux:heading>
                <flux:text class="mt-2">{{ $editMode ? 'Edit cash flow transaction' : 'Record income or expense transaction' }}</flux:text>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:radio.group wire:model.live="type" label="Transaction Type" variant="segmented">
                    <flux:radio value="income" label="Income" />
                    <flux:radio value="expense" label="Expense" />
                </flux:radio.group>

                <flux:select wire:model="category" label="Category" placeholder="Select category...">
                    @foreach($this->getCategories() as $key => $label)
                        <flux:select.option value="{{ $key }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="amount" type="number" label="Amount (Rp)" placeholder="0" min="1" />

                <flux:input wire:model="description" label="Description" placeholder="E.g: Employee Salary December" />

                <flux:textarea wire:model="notes" label="Notes (optional)" placeholder="Additional notes..." rows="2" />

                <flux:input wire:model="transaction_date" type="date" label="Transaction Date" />

                <div class="flex gap-2 pt-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">
                        {{ $editMode ? 'Update' : 'Save' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Delete Modal -->
    <flux:modal name="delete-cash-flow" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Transaction</flux:heading>
                <flux:text class="mt-2">Are you sure you want to delete this transaction? This action cannot be undone.</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="delete">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
