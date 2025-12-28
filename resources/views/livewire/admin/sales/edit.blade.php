<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Edit Sale / Refund</flux:heading>
            <flux:text>Invoice: {{ $sale->invoice_number }}</flux:text>
        </div>
        <flux:button href="{{ route('admin.sales.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            Back to Sales
        </flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sale Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Sale Info Card -->
            <flux:card>
                <flux:heading class="mb-4">Sale Information</flux:heading>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <flux:input wire:model="customerName" label="Customer Name" />
                    <div>
                        <flux:text size="sm" class="mb-2">Status</flux:text>
                        <flux:radio.group wire:model="status" variant="segmented">
                            <flux:radio value="completed" label="Completed" />
                            <flux:radio value="pending" label="Pending" />
                            <flux:radio value="cancelled" label="Cancelled" />
                        </flux:radio.group>
                    </div>
                </div>
                
                <flux:textarea wire:model="notes" label="Notes" rows="2" />
                
                <div class="flex justify-end mt-4">
                    <flux:button variant="primary" wire:click="updateSale" icon="check">
                        Update Sale
                    </flux:button>
                </div>
            </flux:card>

            <!-- Items Card -->
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading>Sale Items</flux:heading>
                    @if ($sale->status !== 'cancelled')
                        <flux:badge color="blue">Select items to refund</flux:badge>
                    @endif
                </div>

                <flux:table>
                    <flux:table.columns>
                        @if ($sale->status !== 'cancelled')
                            <flux:table.column class="w-12">Refund</flux:table.column>
                        @endif
                        <flux:table.column>Product</flux:table.column>
                        <flux:table.column>Price</flux:table.column>
                        <flux:table.column>Qty</flux:table.column>
                        <flux:table.column>Subtotal</flux:table.column>
                        @if ($sale->status !== 'cancelled')
                            <flux:table.column>Refund Qty</flux:table.column>
                        @endif
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($sale->items as $item)
                            <flux:table.row>
                                @if ($sale->status !== 'cancelled')
                                    <flux:table.cell>
                                        <flux:checkbox wire:model.live="refundItems.{{ $item->id }}.refund" />
                                    </flux:table.cell>
                                @endif
                                <flux:table.cell>
                                    <div class="flex items-center gap-3">
                                        @if ($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" class="w-10 h-10 object-cover rounded">
                                        @else
                                            <div class="w-10 h-10 bg-zinc-200 dark:bg-zinc-700 rounded flex items-center justify-center">
                                                <flux:icon.photo class="w-5 h-5 text-zinc-400" />
                                            </div>
                                        @endif
                                        <span class="font-medium">{{ $item->product_name }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>Rp {{ number_format($item->price, 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell>{{ $item->quantity }}</flux:table.cell>
                                <flux:table.cell class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</flux:table.cell>
                                @if ($sale->status !== 'cancelled')
                                    <flux:table.cell>
                                        @if (isset($refundItems[$item->id]) && $refundItems[$item->id]['refund'])
                                            <flux:input 
                                                wire:model.live="refundItems.{{ $item->id }}.quantity" 
                                                type="number" 
                                                min="1" 
                                                max="{{ $item->quantity }}" 
                                                class="w-20"
                                            />
                                        @else
                                            <span class="text-zinc-400">-</span>
                                        @endif
                                    </flux:table.cell>
                                @endif
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="text-center py-4">
                                    <flux:text>No items in this sale.</flux:text>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>

                @if ($sale->status !== 'cancelled' && $sale->items->count() > 0)
                    <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <flux:heading size="sm" class="text-red-600 dark:text-red-400 mb-3">Process Refund</flux:heading>
                        <flux:textarea wire:model="refundReason" label="Refund Reason" placeholder="Enter reason for refund..." rows="2" />
                        <div class="flex justify-end mt-3">
                            <flux:button variant="danger" wire:click="processRefund" icon="arrow-uturn-left">
                                Process Refund
                            </flux:button>
                        </div>
                    </div>
                @endif
            </flux:card>
        </div>

        <!-- Summary Sidebar -->
        <div class="space-y-4">
            <flux:card>
                <flux:heading class="mb-4">Summary</flux:heading>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <flux:text>Invoice</flux:text>
                        <span class="font-mono">{{ $sale->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <flux:text>Date</flux:text>
                        <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <flux:text>Cashier</flux:text>
                        <span>{{ $sale->cashier->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <flux:text>Payment Method</flux:text>
                        @if ($sale->payment_method === 'cash')
                            <flux:badge color="green" size="sm">CASH</flux:badge>
                        @elseif ($sale->payment_method === 'qris')
                            <flux:badge color="purple" size="sm">QRIS</flux:badge>
                        @else
                            <flux:badge color="blue" size="sm">TRANSFER</flux:badge>
                        @endif
                    </div>
                    
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-3">
                        <div class="flex justify-between">
                            <flux:text>Total</flux:text>
                            <span class="font-bold text-lg">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if ($sale->total_refunded > 0)
                            <div class="flex justify-between text-red-600">
                                <flux:text class="text-red-600">Refunded</flux:text>
                                <span>- Rp {{ number_format($sale->total_refunded, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <flux:text>Paid</flux:text>
                            <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        @if ($sale->payment_method === 'cash')
                            <div class="flex justify-between">
                                <flux:text>Change</flux:text>
                                <span class="text-green-600">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </flux:card>

            <!-- Refund History for this Sale -->
            @if ($sale->refunds->count() > 0)
                <flux:card>
                    <flux:heading size="sm" class="mb-3">Refund History</flux:heading>
                    <div class="space-y-3">
                        @foreach ($sale->refunds as $refund)
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-mono text-xs text-red-600">{{ $refund->refund_number }}</span>
                                    <span class="text-xs text-zinc-500">{{ $refund->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ Str::limit($refund->reason, 25) }}</span>
                                    <span class="font-bold text-red-600">- Rp {{ number_format($refund->total_refund, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-xs text-zinc-500 mt-1">
                                    {{ $refund->items->count() }} items • by {{ $refund->user->name ?? 'N/A' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-3 border-t dark:border-zinc-700">
                        <a href="{{ route('admin.refunds.index') }}" wire:navigate class="text-sm text-blue-600 hover:underline">
                            View All Refunds →
                        </a>
                    </div>
                </flux:card>
            @endif

            @if ($sale->status !== 'cancelled')
                <flux:card class="border-red-200 dark:border-red-800">
                    <flux:heading size="sm" class="text-red-600 dark:text-red-400 mb-3">Danger Zone</flux:heading>
                    <flux:text size="sm" class="mb-3">Cancel this entire sale and restore all stock.</flux:text>
                    <flux:button variant="danger" class="w-full" wire:click="cancelSale" wire:confirm="Are you sure you want to cancel this sale? All stock will be restored.">
                        Cancel Entire Sale
                    </flux:button>
                </flux:card>
            @else
                <flux:card class="bg-red-50 dark:bg-red-900/20">
                    <div class="text-center">
                        <flux:icon.x-circle class="w-12 h-12 mx-auto text-red-500 mb-2" />
                        <flux:heading size="sm" class="text-red-600">Sale Cancelled</flux:heading>
                        <flux:text size="sm">This sale has been cancelled.</flux:text>
                    </div>
                </flux:card>
            @endif
        </div>
    </div>
</div>
