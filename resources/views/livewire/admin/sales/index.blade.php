<div>
    <div class="flex items-center justify-between mb-4">
        <div class="text-2xl font-semibold">Sales History</div>
        <div class="flex items-center gap-2">
            <flux:dropdown>
                <flux:button variant="ghost" icon="funnel" icon-trailing="chevron-down">
                    Filter
                    @if($filterStatus || $filterPaymentMethod || $filterDate)
                        <flux:badge color="blue" size="sm">!</flux:badge>
                    @endif
                </flux:button>
                <flux:menu>
                    <flux:menu.submenu heading="Status">
                        <flux:menu.radio.group wire:model.live="filterStatus">
                            <flux:menu.radio value="">All Status</flux:menu.radio>
                            <flux:menu.radio value="completed">Completed</flux:menu.radio>
                            <flux:menu.radio value="pending">Pending</flux:menu.radio>
                            <flux:menu.radio value="cancelled">Cancelled</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu.submenu>
                    <flux:menu.submenu heading="Payment Method">
                        <flux:menu.radio.group wire:model.live="filterPaymentMethod">
                            <flux:menu.radio value="">All Methods</flux:menu.radio>
                            <flux:menu.radio value="cash">Cash</flux:menu.radio>
                            <flux:menu.radio value="qris">QRIS</flux:menu.radio>
                            <flux:menu.radio value="transfer">Transfer</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu.submenu>
                    <flux:menu.separator />
                    <flux:menu.item wire:click="resetFilters" icon="x-mark" variant="danger">Reset Filters</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
            
            <flux:input type="date" wire:model.live="filterDate" />
            <flux:input wire:model.live="search" placeholder="Search invoice/customer..." icon="magnifying-glass" class="w-64" />
        </div>
    </div>

    <flux:table :paginate="$sales">
        <flux:table.columns>
            <flux:table.column>Invoice</flux:table.column>
            <flux:table.column>Date</flux:table.column>
            <flux:table.column>Customer</flux:table.column>
            <flux:table.column>Items</flux:table.column>
            <flux:table.column>Payment</flux:table.column>
            <flux:table.column>Total</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($sales as $sale)
                <flux:table.row>
                    <flux:table.cell>
                        <span class="font-mono text-sm">{{ $sale->invoice_number }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div>{{ $sale->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-zinc-500">{{ $sale->created_at->format('H:i') }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $sale->customer_name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm">{{ $sale->items->count() }} items</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($sale->payment_method === 'cash')
                            <flux:badge color="green" size="sm">CASH</flux:badge>
                        @elseif ($sale->payment_method === 'qris')
                            <flux:badge color="purple" size="sm">QRIS</flux:badge>
                        @else
                            <flux:badge color="blue" size="sm">TRANSFER</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="font-medium">
                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($sale->status === 'completed')
                            <flux:badge color="green" size="sm">Completed</flux:badge>
                        @elseif ($sale->status === 'pending')
                            <flux:badge color="yellow" size="sm">Pending</flux:badge>
                        @else
                            <flux:badge color="red" size="sm">Cancelled</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-1">
                            <flux:button size="xs" href="{{ route('admin.sales.edit', $sale->id) }}" wire:navigate icon="pencil">
                                Edit
                            </flux:button>
                            <flux:modal.trigger name="delete-sale">
                                <flux:button size="xs" variant="danger" wire:click="confirmDelete({{ $sale->id }})" icon="trash" />
                            </flux:modal.trigger>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-8">
                        <flux:icon.receipt-percent class="w-12 h-12 mx-auto text-zinc-300 mb-2" />
                        <flux:text>No sales found.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <!-- Delete Modal -->
    <flux:modal name="delete-sale" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Sale</flux:heading>
                <flux:text class="mt-2">Are you sure you want to delete this sale? Stock will be restored. This action cannot be undone.</flux:text>
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
