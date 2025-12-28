<div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <flux:heading>Refund History</flux:heading>
            <flux:subheading>Track all refunds processed from sales</flux:subheading>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <flux:icon.arrow-uturn-left class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Today's Refunds</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($todayRefunds, 0, ',', '.') }}</p>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <flux:icon.calendar class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">This Month</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($monthRefunds, 0, ',', '.') }}</p>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Refunds</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalRefunds) }}</p>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search"
            placeholder="Search refund number, invoice, customer..." class="md:w-80" />
        <flux:input type="date" wire:model.live="filterDate" />
    </div>

    <!-- Table -->
    <flux:card class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Refund #</flux:table.column>
                <flux:table.column>Sale Invoice</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Items</flux:table.column>
                <flux:table.column>Total Refund</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>Processed By</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($refunds as $refund)
                    <flux:table.row>
                        <flux:table.cell class="font-mono text-sm">
                            <flux:badge color="red" size="sm">{{ $refund->refund_number }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('admin.sales.edit', $refund->sale_id) }}" wire:navigate 
                               class="font-mono text-sm text-blue-600 hover:underline">
                                {{ $refund->sale->invoice_number }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>{{ $refund->sale->customer_name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm">{{ $refund->items->count() }} items</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="font-bold text-red-600">
                            - Rp {{ number_format($refund->total_refund, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm">{{ Str::limit($refund->reason, 30) }}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{ $refund->user->name ?? 'N/A' }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm">{{ $refund->created_at->format('d/m/Y') }}</span>
                            <p class="text-xs text-zinc-500">{{ $refund->created_at->format('H:i') }}</p>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button icon="eye" size="sm" wire:click="viewRefund({{ $refund->id }})">
                                View
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9" class="text-center py-8 text-zinc-500">
                            <flux:icon.arrow-uturn-left class="w-12 h-12 mx-auto mb-2 text-zinc-300" />
                            <p>No refunds found</p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $refunds->links() }}
        </div>
    </flux:card>

    <!-- View Refund Modal -->
    @if($viewRefund)
        <flux:modal wire:model.self="viewRefundId" class="max-w-2xl">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading>Refund Details</flux:heading>
                        <flux:badge color="red" class="mt-1">{{ $viewRefund->refund_number }}</flux:badge>
                    </div>
                </div>

                <!-- Refund Info -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-zinc-500">Sale Invoice:</span>
                        <span class="font-mono ml-2">{{ $viewRefund->sale->invoice_number }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Customer:</span>
                        <span class="ml-2">{{ $viewRefund->sale->customer_name }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Processed By:</span>
                        <span class="ml-2">{{ $viewRefund->user->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Date:</span>
                        <span class="ml-2">{{ $viewRefund->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-zinc-500">Reason:</span>
                        <span class="ml-2">{{ $viewRefund->reason }}</span>
                    </div>
                    @if($viewRefund->notes)
                        <div class="col-span-2">
                            <span class="text-zinc-500">Notes:</span>
                            <span class="ml-2">{{ $viewRefund->notes }}</span>
                        </div>
                    @endif
                </div>

                <!-- Refund Items -->
                <div>
                    <flux:heading size="sm" class="mb-3">Refunded Items</flux:heading>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-zinc-700">
                                <th class="text-left py-2">Product</th>
                                <th class="text-center py-2">Qty</th>
                                <th class="text-right py-2">Price</th>
                                <th class="text-right py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($viewRefund->items as $item)
                                <tr class="border-b dark:border-zinc-700">
                                    <td class="py-2">
                                        <div class="flex items-center gap-2">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                    class="w-8 h-8 rounded object-cover">
                                            @endif
                                            <span>{{ $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center py-2">{{ $item->quantity }}</td>
                                    <td class="text-right py-2">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-right py-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 dark:border-zinc-600">
                                <td colspan="3" class="py-3 text-right font-medium">Total Refund:</td>
                                <td class="py-3 text-right font-bold text-red-600 text-lg">
                                    - Rp {{ number_format($viewRefund->total_refund, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="closeView">Close</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
