<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Transaction Detail</flux:heading>
            <flux:text>Invoice: {{ $sale->invoice_number }}</flux:text>
        </div>
        <div class="flex items-center gap-2">
            <flux:button wire:click="printReceipt" variant="primary" icon="printer">
                Print Receipt
            </flux:button>
            <flux:button href="{{ route('cashier.sales.index') }}" wire:navigate variant="ghost" icon="arrow-left">
                Back to Transactions
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transaction Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Transaction Info Card -->
            <flux:card>
                <flux:heading class="mb-4">Transaction Information</flux:heading>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:text size="sm" class="text-zinc-500">Customer Name</flux:text>
                        <div class="font-medium">{{ $sale->customer_name }}</div>
                    </div>
                    <div>
                        <flux:text size="sm" class="text-zinc-500">Status</flux:text>
                        <div class="mt-1">
                            @if ($sale->status === 'completed')
                                <flux:badge color="green">Completed</flux:badge>
                            @elseif ($sale->status === 'pending')
                                <flux:badge color="yellow">Pending</flux:badge>
                            @else
                                <flux:badge color="red">Cancelled</flux:badge>
                            @endif
                        </div>
                    </div>
                    @if ($sale->notes)
                        <div class="col-span-2">
                            <flux:text size="sm" class="text-zinc-500">Notes</flux:text>
                            <div class="font-medium">{{ $sale->notes }}</div>
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Items Card -->
            <flux:card>
                <flux:heading class="mb-4">Items</flux:heading>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Product</flux:table.column>
                        <flux:table.column>Price</flux:table.column>
                        <flux:table.column>Qty</flux:table.column>
                        <flux:table.column>Subtotal</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($sale->items as $item)
                            <flux:table.row>
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
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="text-center py-4">
                                    <flux:text>No items in this sale.</flux:text>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
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
                                    {{ $refund->items->count() }} items
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endif

            @if ($sale->status === 'cancelled')
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

    <!-- Hidden Receipt Content for Printing -->
    @php
        $storeName = \App\Models\Setting::get('store_name', 'My Store');
        $storeAddress = \App\Models\Setting::get('store_address');
        $storePhone = \App\Models\Setting::get('store_phone');
        $storeLogo = \App\Models\Setting::get('store_logo');
        $receiptFooter = \App\Models\Setting::get('receipt_footer', 'Thank you for your purchase!');
    @endphp
    <div id="receipt-content" class="hidden font-mono text-sm">
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
                <span>{{ $sale->invoice_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Date:</span>
                <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Cashier:</span>
                <span>{{ $sale->cashier->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Customer:</span>
                <span>{{ $sale->customer_name }}</span>
            </div>
        </div>
        
        <div class="border-t border-dashed border-zinc-300 my-2"></div>
        
        <div class="space-y-1">
            @foreach ($sale->items as $item)
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
                <span>Rp {{ number_format($sale->subtotal ?? $sale->total_amount, 0, ',', '.') }}</span>
            </div>
            @if ($sale->tax_amount > 0)
                <div class="flex justify-between text-xs">
                    <span>{{ $sale->tax_name }} ({{ $sale->tax_percentage }}%)</span>
                    <span>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold">
                <span>TOTAL</span>
                <span>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span>Payment ({{ strtoupper($sale->payment_method) }})</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            @if ($sale->payment_method === 'cash')
                <div class="flex justify-between text-xs">
                    <span>Change</span>
                    <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
        
        <div class="border-t border-dashed border-zinc-300 my-2"></div>
        
        <div class="text-center text-xs text-zinc-500">
            <div>{{ $receiptFooter }}</div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('print-receipt', () => {
        const content = document.getElementById('receipt-content');
        if (content) {
            const storeName = content.querySelector('.text-lg.font-bold')?.textContent || 'Receipt';
            
            // Clone content and fix image URLs to absolute
            const clonedContent = content.cloneNode(true);
            clonedContent.classList.remove('hidden');
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
            
            // Wait for images to load before printing
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
                // Fallback timeout in case images don't trigger events
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
