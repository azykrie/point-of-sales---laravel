<div>
    <div class="flex items-center justify-between mb-4">
        <div class="text-2xl font-semibold">My Transactions</div>
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
                            <flux:button size="xs" href="{{ route('cashier.sales.show', $sale->id) }}" wire:navigate icon="eye">
                                View
                            </flux:button>
                            <flux:button size="xs" variant="ghost" wire:click="showReceipt({{ $sale->id }})" icon="printer" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-8">
                        <flux:icon.receipt-percent class="w-12 h-12 mx-auto text-zinc-300 mb-2" />
                        <flux:text>No transactions found.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <!-- Receipt Modal -->
    <flux:modal name="receipt-modal" class="w-96">
        @if ($selectedSale)
            @php
                $storeName = \App\Models\Setting::get('store_name', 'My Store');
                $storeAddress = \App\Models\Setting::get('store_address');
                $storePhone = \App\Models\Setting::get('store_phone');
                $storeLogo = \App\Models\Setting::get('store_logo');
                $receiptFooter = \App\Models\Setting::get('receipt_footer', 'Thank you for your purchase!');
            @endphp
            <div id="receipt-content" class="font-mono text-sm">
                <div class="text-center mb-4">
                    @if($storeLogo)
                        <img src="{{ asset($storeLogo) }}" alt="Logo" class="h-10 mx-auto mb-2" />
                    @endif
                    <div class="text-lg font-bold">{{ $storeName }}</div>
                    @if($storeAddress)
                        <div class="text-xs text-zinc-500">{{ $storeAddress }}</div>
                    @endif
                    @if($storePhone)
                        <div class="text-xs text-zinc-500">Telp: {{ $storePhone }}</div>
                    @endif
                </div>
                
                <div class="border-t border-dashed border-zinc-300 my-2"></div>
                
                <div class="text-xs space-y-1">
                    <div class="flex justify-between">
                        <span>Invoice:</span>
                        <span>{{ $selectedSale->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Date:</span>
                        <span>{{ $selectedSale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cashier:</span>
                        <span>{{ $selectedSale->cashier->name ?? auth()->user()->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Customer:</span>
                        <span>{{ $selectedSale->customer_name }}</span>
                    </div>
                </div>
                
                <div class="border-t border-dashed border-zinc-300 my-2"></div>
                
                <div class="space-y-1">
                    @foreach ($selectedSale->items as $item)
                        <div>
                            <div>{{ $item->product_name }}</div>
                            <div class="flex justify-between text-xs">
                                <span>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t border-dashed border-zinc-300 my-2"></div>
                
                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($selectedSale->subtotal ?? $selectedSale->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if ($selectedSale->tax_amount > 0)
                        <div class="flex justify-between text-xs">
                            <span>{{ $selectedSale->tax_name }} ({{ $selectedSale->tax_percentage }}%)</span>
                            <span>Rp {{ number_format($selectedSale->tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold">
                        <span>TOTAL</span>
                        <span>Rp {{ number_format($selectedSale->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span>Payment ({{ strtoupper($selectedSale->payment_method) }})</span>
                        <span>Rp {{ number_format($selectedSale->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    @if ($selectedSale->payment_method === 'cash')
                        <div class="flex justify-between text-xs">
                            <span>Change</span>
                            <span>Rp {{ number_format($selectedSale->change_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
                
                <div class="border-t border-dashed border-zinc-300 my-2"></div>
                
                <div class="text-center text-xs text-zinc-500">
                    <div>{{ $receiptFooter }}</div>
                </div>
            </div>
            
            <div class="flex gap-2 mt-4">
                <flux:button variant="primary" class="flex-1" wire:click="printReceipt" icon="printer">
                    Print Receipt
                </flux:button>
                <flux:button variant="ghost" wire:click="closeReceipt">
                    Close
                </flux:button>
            </div>
        @endif
    </flux:modal>
</div>

@script
<script>
    $wire.on('print-receipt', () => {
        const content = document.getElementById('receipt-content');
        if (content) {
            const storeName = content.querySelector('.text-lg.font-bold')?.textContent || 'Receipt';
            
            const clonedContent = content.cloneNode(true);
            const images = clonedContent.querySelectorAll('img');
            images.forEach(img => {
                if (img.src) {
                    img.src = img.src;
                }
            });
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>${storeName}</title>
                    <style>
                        @page { margin: 0; size: 80mm auto; }
                        body { font-family: monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 10px; }
                        .text-center { text-align: center; }
                        .flex { display: flex; justify-content: space-between; }
                        .font-bold { font-weight: bold; }
                        .border-t { border-top: 1px dashed #ccc; margin: 8px 0; }
                        .text-xs { font-size: 10px; }
                        .text-lg { font-size: 14px; }
                        .mb-4 { margin-bottom: 16px; }
                        .mb-2 { margin-bottom: 8px; }
                        .my-2 { margin: 8px 0; }
                        .space-y-1 > * + * { margin-top: 4px; }
                        img { max-height: 50px; margin: 0 auto 8px; display: block; }
                        .h-10 { height: 40px; }
                        .mx-auto { margin-left: auto; margin-right: auto; }
                    </style>
                </head>
                <body>
                    ${clonedContent.innerHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
            
            const printImages = printWindow.document.querySelectorAll('img');
            if (printImages.length > 0) {
                let loadedCount = 0;
                printImages.forEach(img => {
                    if (img.complete) {
                        loadedCount++;
                        if (loadedCount === printImages.length) {
                            printWindow.print();
                            printWindow.close();
                        }
                    } else {
                        img.onload = img.onerror = () => {
                            loadedCount++;
                            if (loadedCount === printImages.length) {
                                printWindow.print();
                                printWindow.close();
                            }
                        };
                    }
                });
                setTimeout(() => {
                    if (!printWindow.closed) {
                        printWindow.print();
                        printWindow.close();
                    }
                }, 1000);
            } else {
                printWindow.print();
                printWindow.close();
            }
        }
    });
</script>
@endscript
