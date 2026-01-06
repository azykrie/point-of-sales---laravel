<div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <flux:heading>Refund Management</flux:heading>
            <flux:subheading>Manage refund requests and track approved refunds</flux:subheading>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Pending Requests</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingRefunds }}</p>
                </div>
            </div>
        </flux:card>
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
                    <flux:icon.check-circle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Approved</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalRefunds) }}</p>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search"
            placeholder="Search refund number, invoice, customer..." class="md:w-80" />
        <flux:select wire:model.live="filterStatus" class="md:w-48">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </flux:select>
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
                <flux:table.column>Requested By</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($refunds as $refund)
                    <flux:table.row class="{{ $refund->status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                        <flux:table.cell class="font-mono text-sm">
                            <flux:badge color="{{ $refund->status === 'pending' ? 'yellow' : ($refund->status === 'approved' ? 'green' : 'red') }}" size="sm">
                                {{ $refund->refund_number }}
                            </flux:badge>
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
                            Rp {{ number_format($refund->total_refund, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $refund->user->name ?? 'N/A' }}</flux:table.cell>
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
                            <div class="flex items-center gap-1">
                                <flux:button icon="eye" size="sm" wire:click="viewRefund({{ $refund->id }})" title="View Details" />
                                @if ($refund->status === 'pending')
                                    <flux:button icon="check" size="sm" variant="primary" 
                                        wire:click="approveRefund({{ $refund->id }})" 
                                        wire:confirm="Are you sure you want to approve this refund? Stock will be restored."
                                        title="Approve" />
                                    <flux:button icon="x-mark" size="sm" variant="danger" 
                                        wire:click="openRejectModal({{ $refund->id }})"
                                        title="Reject" />
                                @endif
                            </div>
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
    <flux:modal wire:model="showViewModal" class="max-w-2xl">
        @if($viewRefund)
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading>Refund Details</flux:heading>
                        <div class="flex items-center gap-2 mt-1">
                            <flux:badge color="{{ $viewRefund->status === 'pending' ? 'yellow' : ($viewRefund->status === 'approved' ? 'green' : 'red') }}">
                                {{ $viewRefund->refund_number }}
                            </flux:badge>
                            @if ($viewRefund->status === 'pending')
                                <flux:badge color="yellow">Pending</flux:badge>
                            @elseif ($viewRefund->status === 'approved')
                                <flux:badge color="green">Approved</flux:badge>
                            @else
                                <flux:badge color="red">Rejected</flux:badge>
                            @endif
                        </div>
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
                        <span class="text-zinc-500">Requested By:</span>
                        <span class="ml-2">{{ $viewRefund->user->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Request Date:</span>
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
                    @if ($viewRefund->processedBy)
                        <div>
                            <span class="text-zinc-500">Processed By:</span>
                            <span class="ml-2">{{ $viewRefund->processedBy->name }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500">Processed At:</span>
                            <span class="ml-2">{{ $viewRefund->processed_at?->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                    @if ($viewRefund->reject_reason)
                        <div class="col-span-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <span class="text-red-600 font-medium">Rejection Reason:</span>
                            <p class="text-red-600 mt-1">{{ $viewRefund->reject_reason }}</p>
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
                                    Rp {{ number_format($viewRefund->total_refund, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="flex justify-between">
                    @if ($viewRefund->status === 'pending')
                        <div class="flex gap-2">
                            <flux:button variant="primary" icon="check" 
                                wire:click="approveRefund({{ $viewRefund->id }})"
                                wire:confirm="Are you sure you want to approve this refund?">
                                Approve
                            </flux:button>
                            <flux:button variant="danger" icon="x-mark" 
                                wire:click="openRejectModal({{ $viewRefund->id }})">
                                Reject
                            </flux:button>
                        </div>
                    @else
                        <div></div>
                    @endif
                    <flux:button wire:click="closeView">Close</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Reject Modal -->
    <flux:modal wire:model="showRejectModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading>Reject Refund Request</flux:heading>
            <flux:subheading>Please provide a reason for rejecting this refund request.</flux:subheading>

            <flux:textarea wire:model="rejectReason" label="Rejection Reason *" 
                placeholder="Explain why this refund request is being rejected..." rows="4" />
            
            @error('rejectReason')
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror

            <div class="flex justify-end gap-2">
                <flux:button wire:click="closeRejectModal" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="rejectRefund" variant="danger" icon="x-mark">Reject Request</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
