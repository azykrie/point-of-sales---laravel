<div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <flux:heading>Request Refund</flux:heading>
            <flux:subheading>Submit refund requests for your transactions</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openRequestModal">
            New Request
        </flux:button>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</p>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Approved</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $approvedCount }}</p>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <flux:icon.x-circle class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Rejected</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $rejectedCount }}</p>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search"
            placeholder="Search refund number or invoice..." class="md:w-80" />
        <flux:select wire:model.live="filterStatus" class="md:w-48">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </flux:select>
    </div>

    <!-- Table -->
    <flux:card class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Refund #</flux:table.column>
                <flux:table.column>Invoice</flux:table.column>
                <flux:table.column>Items</flux:table.column>
                <flux:table.column>Total Refund</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Requested</flux:table.column>
                <flux:table.column>Processed By</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($refunds as $refund)
                    <flux:table.row>
                        <flux:table.cell class="font-mono text-sm">
                            <flux:badge color="zinc" size="sm">{{ $refund->refund_number }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('cashier.sales.show', $refund->sale_id) }}" wire:navigate 
                               class="font-mono text-sm text-blue-600 hover:underline">
                                {{ $refund->sale->invoice_number }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm">{{ $refund->items->count() }} items</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="font-bold text-red-600">
                            Rp {{ number_format($refund->total_refund, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm" title="{{ $refund->reason }}">{{ Str::limit($refund->reason, 30) }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($refund->status === 'pending')
                                <flux:badge color="yellow" size="sm">Pending</flux:badge>
                            @elseif ($refund->status === 'approved')
                                <flux:badge color="green" size="sm">Approved</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">Rejected</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm">{{ $refund->created_at->format('d/m/Y') }}</span>
                            <p class="text-xs text-zinc-500">{{ $refund->created_at->format('H:i') }}</p>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($refund->processedBy)
                                <div class="text-sm">{{ $refund->processedBy->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $refund->processed_at?->format('d/m/Y H:i') }}</div>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500">
                            <flux:icon.arrow-uturn-left class="w-12 h-12 mx-auto mb-2 text-zinc-300" />
                            <p>No refund requests found</p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $refunds->links() }}
        </div>
    </flux:card>

    <!-- Request Refund Modal -->
    <flux:modal wire:model="showRequestModal" class="max-w-3xl">
        <div class="space-y-6">
            <div>
                <flux:heading>New Refund Request</flux:heading>
                <flux:subheading>Search for your transaction and select items to refund</flux:subheading>
            </div>

            <!-- Search Invoice -->
            <div class="flex gap-3">
                <flux:input wire:model="invoiceSearch" placeholder="Enter invoice number (e.g. INV-20251230-0001)" class="flex-1" />
                <flux:button wire:click="searchSale" icon="magnifying-glass">Search</flux:button>
            </div>

            @if (session('search-error'))
                <div class="p-3 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm">
                    {{ session('search-error') }}
                </div>
            @endif

            @if ($selectedSale)
                <!-- Sale Info -->
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-zinc-500">Invoice:</span>
                            <span class="font-mono ml-1">{{ $selectedSale->invoice_number }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500">Customer:</span>
                            <span class="ml-1">{{ $selectedSale->customer_name }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500">Date:</span>
                            <span class="ml-1">{{ $selectedSale->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500">Total:</span>
                            <span class="font-bold ml-1">Rp {{ number_format($selectedSale->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Select Items -->
                <div>
                    <flux:heading size="sm" class="mb-3">Select Items to Refund</flux:heading>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach ($selectedSale->items as $item)
                            @php
                                $isSelected = isset($selectedItems[$item->id]);
                                $refundedQty = \App\Models\RefundItem::whereHas('refund', function($q) use ($selectedSale, $item) {
                                    $q->where('sale_id', $selectedSale->id)
                                      ->where('status', '!=', 'rejected');
                                })->where('product_id', $item->product_id)->sum('quantity');
                                $availableQty = $item->quantity - $refundedQty;
                            @endphp
                            <div class="flex items-center justify-between p-3 border dark:border-zinc-700 rounded-lg {{ $isSelected ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700' : '' }} {{ $availableQty <= 0 ? 'opacity-50' : '' }}">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" 
                                        wire:click="toggleItem({{ $item->id }})" 
                                        {{ $isSelected ? 'checked' : '' }}
                                        {{ $availableQty <= 0 ? 'disabled' : '' }}
                                        class="rounded border-zinc-300 dark:border-zinc-600" />
                                    <div class="flex items-center gap-2">
                                        @if ($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" class="w-10 h-10 rounded object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-zinc-200 dark:bg-zinc-700 rounded flex items-center justify-center">
                                                <flux:icon.photo class="w-5 h-5 text-zinc-400" />
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-medium">{{ $item->product_name }}</div>
                                            <div class="text-xs text-zinc-500">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-sm text-zinc-500">
                                        Available: {{ $availableQty }}/{{ $item->quantity }}
                                    </div>
                                    @if ($isSelected)
                                        <div class="flex items-center gap-2">
                                            <flux:button size="sm" icon="minus" 
                                                wire:click="updateItemQuantity({{ $item->id }}, {{ $selectedItems[$item->id]['quantity'] - 1 }})" />
                                            <span class="w-8 text-center font-bold">{{ $selectedItems[$item->id]['quantity'] }}</span>
                                            <flux:button size="sm" icon="plus"
                                                wire:click="updateItemQuantity({{ $item->id }}, {{ $selectedItems[$item->id]['quantity'] + 1 }})" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reason & Notes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:textarea wire:model="reason" label="Reason *" placeholder="Explain why you need to refund these items..." rows="3" />
                    <flux:textarea wire:model="notes" label="Additional Notes (optional)" placeholder="Any additional notes..." rows="3" />
                </div>

                @error('selectedItems')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
                @error('reason')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror

                <!-- Summary -->
                @if (count($selectedItems) > 0)
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ count($selectedItems) }} items selected</div>
                                <div class="text-2xl font-bold text-red-600">
                                    Rp {{ number_format($this->totalRefund, 0, ',', '.') }}
                                </div>
                            </div>
                            <flux:button variant="primary" wire:click="submitRequest" icon="paper-airplane">
                                Submit Request
                            </flux:button>
                        </div>
                    </div>
                @endif
            @endif

            <div class="flex justify-end">
                <flux:button wire:click="closeRequestModal" variant="ghost">Cancel</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
