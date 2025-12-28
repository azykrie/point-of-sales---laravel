<div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <flux:heading>Stock Movements</flux:heading>
            <flux:subheading>Manage stock in and stock out</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button icon="arrow-down-tray" variant="primary" wire:click="openStockInModal">
                Stock In
            </flux:button>
            <flux:button icon="arrow-up-tray" variant="danger" wire:click="openStockOutModal">
                Stock Out
            </flux:button>
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

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search"
            placeholder="Search product or reference..." />

        <flux:select wire:model.live="filterType" placeholder="All Types">
            <flux:select.option value="">All Types</flux:select.option>
            <flux:select.option value="in">Stock In</flux:select.option>
            <flux:select.option value="out">Stock Out</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="filterReason" placeholder="All Reasons">
            <flux:select.option value="">All Reasons</flux:select.option>
            <flux:select.option value="purchase">Purchase</flux:select.option>
            <flux:select.option value="return">Return</flux:select.option>
            <flux:select.option value="adjustment">Adjustment</flux:select.option>
            <flux:select.option value="damage">Damage</flux:select.option>
            <flux:select.option value="expired">Expired</flux:select.option>
            <flux:select.option value="other">Other</flux:select.option>
        </flux:select>

        <flux:input type="date" wire:model.live="filterDate" />
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @php
            $todayIn = \App\Models\StockMovement::where('type', 'in')->whereDate('created_at', today())->sum('quantity');
            $todayOut = \App\Models\StockMovement::where('type', 'out')->whereDate('created_at', today())->sum('quantity');
        @endphp
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.arrow-down-tray class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Stock In Today</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">+{{ number_format($todayIn) }}</p>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <flux:icon.arrow-up-tray class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Stock Out Today</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">-{{ number_format($todayOut) }}</p>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.scale class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Net Change Today</p>
                    <p class="text-2xl font-bold {{ ($todayIn - $todayOut) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ ($todayIn - $todayOut) >= 0 ? '+' : '' }}{{ number_format($todayIn - $todayOut) }}
                    </p>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Table -->
    <flux:card class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Reference</flux:table.column>
                <flux:table.column>Product</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Quantity</flux:table.column>
                <flux:table.column>Stock Before</flux:table.column>
                <flux:table.column>Stock After</flux:table.column>
                <flux:table.column>Reason</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($movements as $movement)
                    <flux:table.row>
                        <flux:table.cell class="font-mono text-sm">
                            {{ $movement->reference_number }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                @if ($movement->product->image)
                                    <img src="{{ asset('storage/' . $movement->product->image) }}"
                                        class="w-8 h-8 rounded object-cover">
                                @endif
                                <div>
                                    <p class="font-medium">{{ $movement->product->name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $movement->product->barcode }}</p>
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($movement->type === 'in')
                                <flux:badge color="green" size="sm">
                                    <flux:icon.arrow-down-tray class="w-3 h-3 mr-1" /> In
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm">
                                    <flux:icon.arrow-up-tray class="w-3 h-3 mr-1" /> Out
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="{{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }} font-bold">
                                {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>{{ number_format($movement->stock_before) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($movement->stock_after) }}</flux:table.cell>
                        <flux:table.cell>
                            @php
                                $reasonLabels = [
                                    'purchase' => 'Purchase',
                                    'return' => 'Return',
                                    'adjustment' => 'Adjustment',
                                    'damage' => 'Damage',
                                    'expired' => 'Expired',
                                    'other' => 'Other',
                                ];
                            @endphp
                            <span class="text-sm">{{ $reasonLabels[$movement->reason] ?? $movement->reason }}</span>
                            @if ($movement->notes)
                                <p class="text-xs text-zinc-500">{{ Str::limit($movement->notes, 30) }}</p>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $movement->user->name }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm">{{ $movement->created_at->format('d/m/Y') }}</span>
                            <p class="text-xs text-zinc-500">{{ $movement->created_at->format('H:i') }}</p>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button icon="trash" variant="danger" size="sm"
                                wire:click="confirmDelete({{ $movement->id }})" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="10" class="text-center py-8 text-zinc-500">
                            No stock movement data found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $movements->links() }}
        </div>
    </flux:card>

    <!-- Stock In Modal -->
    <flux:modal wire:model="showStockInModal" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading>Stock In</flux:heading>
                <flux:subheading>Add product stock</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:select wire:model="stockInProductId" label="Product" placeholder="Select product...">
                    @foreach ($products as $product)
                        <flux:select.option value="{{ $product->id }}">
                            {{ $product->name }} (Stock: {{ $product->stock }})
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="number" wire:model="stockInQuantity" label="Quantity" min="1" />

                <flux:select wire:model="stockInReason" label="Reason">
                    <flux:select.option value="purchase">Purchase</flux:select.option>
                    <flux:select.option value="return">Customer Return</flux:select.option>
                    <flux:select.option value="adjustment">Stock Adjustment</flux:select.option>
                    <flux:select.option value="other">Other</flux:select.option>
                </flux:select>

                <flux:textarea wire:model="stockInNotes" label="Notes (Optional)" rows="2"
                    placeholder="Add notes if needed..." />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="closeStockInModal">Cancel</flux:button>
                <flux:button variant="primary" icon="arrow-down-tray" wire:click="processStockIn">
                    Save Stock In
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Stock Out Modal -->
    <flux:modal wire:model="showStockOutModal" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading>Stock Out</flux:heading>
                <flux:subheading>Reduce product stock</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:select wire:model="stockOutProductId" label="Product" placeholder="Select product...">
                    @foreach ($products as $product)
                        <flux:select.option value="{{ $product->id }}">
                            {{ $product->name }} (Stock: {{ $product->stock }})
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="number" wire:model="stockOutQuantity" label="Quantity" min="1" />

                <flux:select wire:model="stockOutReason" label="Reason">
                    <flux:select.option value="adjustment">Stock Adjustment</flux:select.option>
                    <flux:select.option value="damage">Damage</flux:select.option>
                    <flux:select.option value="expired">Expired</flux:select.option>
                    <flux:select.option value="other">Other</flux:select.option>
                </flux:select>

                <flux:textarea wire:model="stockOutNotes" label="Notes (Optional)" rows="2"
                    placeholder="Add notes if needed..." />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="closeStockOutModal">Cancel</flux:button>
                <flux:button variant="danger" icon="arrow-up-tray" wire:click="processStockOut">
                    Save Stock Out
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model.self="deleteMovementId" class="max-w-sm">
        <div class="space-y-6">
            <div>
                <flux:heading>Delete Data?</flux:heading>
                <flux:subheading>The stock movement data will be deleted and the product stock will be restored.</flux:subheading>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('deleteMovementId', null)">Cancel</flux:button>
                <flux:button variant="danger" wire:click="delete">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
