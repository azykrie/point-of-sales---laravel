<div class="flex gap-6 h-full">
    <!-- LEFT: Cart & Checkout -->
    <div class="flex-1 flex flex-col">
        <div class="mb-4">
            <flux:heading size="xl">Point of Sale</flux:heading>
            <flux:text>Process customer transactions</flux:text>
        </div>

        <!-- Customer Info -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <flux:input wire:model="customerName" label="Customer Name" placeholder="Guest" />
            <flux:input wire:model="notes" label="Notes" placeholder="Optional notes..." />
        </div>

        <!-- Cart Table -->
        <div class="flex-1 overflow-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Product</flux:table.column>
                    <flux:table.column>Price</flux:table.column>
                    <flux:table.column>Qty</flux:table.column>
                    <flux:table.column>Subtotal</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($cart as $index => $item)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    @if ($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['product_name'] }}" class="w-10 h-10 object-cover rounded">
                                    @else
                                        <div class="w-10 h-10 bg-zinc-200 dark:bg-zinc-700 rounded flex items-center justify-center">
                                            <flux:icon.photo class="w-5 h-5 text-zinc-400" />
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-medium">{{ $item['product_name'] }}</span>
                                        <span class="text-xs text-zinc-500 block">Stock: {{ $item['stock'] }}</span>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>Rp {{ number_format($item['price'], 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-1">
                                    <flux:button size="xs" variant="ghost" wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})">-</flux:button>
                                    <span class="w-8 text-center">{{ $item['quantity'] }}</span>
                                    <flux:button size="xs" variant="ghost" wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})">+</flux:button>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="xs" variant="danger" wire:click="removeFromCart({{ $index }})" icon="trash" />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8">
                                <flux:icon.shopping-cart class="w-12 h-12 mx-auto text-zinc-300 mb-2" />
                                <flux:text>Cart is empty. Add products to start.</flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <!-- Payment Section -->
        <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <flux:text size="sm" class="mb-2">Payment Method</flux:text>
                    <flux:radio.group wire:model.live="paymentMethod" variant="segmented">
                        <flux:radio value="cash" label="Cash" />
                        <flux:radio value="qris" label="QRIS" />
                        <flux:radio value="transfer" label="Transfer" />
                    </flux:radio.group>
                </div>
                @if ($paymentMethod === 'cash')
                    <flux:input wire:model.live="paidAmount" label="Paid Amount" type="number" placeholder="0" />
                    <div>
                        <flux:text size="sm" class="mb-2">Change</flux:text>
                        <div class="text-2xl font-bold text-green-600">Rp {{ number_format($change, 0, ',', '.') }}</div>
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-between border-t border-zinc-200 dark:border-zinc-700 pt-4">
                <div>
                    <flux:text size="sm">Total</flux:text>
                    <div class="text-3xl font-bold">Rp {{ number_format($total, 0, ',', '.') }}</div>
                </div>
                <flux:button variant="primary" wire:click="processSale" icon="banknotes">
                    Process Payment
                </flux:button>
            </div>
        </div>
    </div>

    <!-- RIGHT: Product Selection -->
    <div class="w-80 flex flex-col">
        <flux:card>
            <flux:heading class="mb-4">Add Product</flux:heading>
            
            <div class="space-y-4">
                <flux:select wire:model="selectedProductId" label="Select Product" placeholder="Choose a product...">
                    @foreach ($products as $product)
                        <flux:select.option value="{{ $product->id }}">
                            {{ $product->name }} - Rp {{ number_format($product->selling_price, 0, ',', '.') }} ({{ $product->stock }})
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="quantity" label="Quantity" type="number" min="1" />

                <flux:button variant="primary" class="w-full" wire:click="addToCart" icon="plus">
                    Add to Cart
                </flux:button>
            </div>
        </flux:card>

        <!-- Quick Products -->
        <div class="mt-4 flex-1 overflow-auto">
            <flux:text size="sm" class="mb-2">Quick Add</flux:text>
            <div class="grid grid-cols-2 gap-2">
                @foreach ($products->take(8) as $product)
                    <button 
                        wire:click="$set('selectedProductId', {{ $product->id }})"
                        class="p-2 text-left text-xs border rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition {{ $selectedProductId == $product->id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-zinc-200 dark:border-zinc-700' }}"
                    >
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-16 object-cover rounded mb-1">
                        @else
                            <div class="w-full h-16 bg-zinc-200 dark:bg-zinc-700 rounded mb-1 flex items-center justify-center">
                                <flux:icon.photo class="w-6 h-6 text-zinc-400" />
                            </div>
                        @endif
                        <div class="font-medium truncate">{{ $product->name }}</div>
                        <div class="text-zinc-500">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                        <div class="text-zinc-400">Stock: {{ $product->stock }}</div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <flux:modal name="receipt-modal" class="w-96">
        @if ($lastSale)
            <div id="receipt-content" class="font-mono text-sm">
                <div class="text-center mb-4">
                    <div class="text-lg font-bold">STORE NAME</div>
                    <div class="text-xs text-zinc-500">Jl. Example No. 123</div>
                    <div class="text-xs text-zinc-500">Telp: 08123456789</div>
                </div>
                
                <div class="border-t border-dashed border-zinc-300 my-2"></div>
                
                <div class="text-xs space-y-1">
                    <div class="flex justify-between">
                        <span>Invoice:</span>
                        <span>{{ $lastSale->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Date:</span>
                        <span>{{ $lastSale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cashier:</span>
                        <span>{{ $lastSale->cashier->name ?? 'Admin' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Customer:</span>
                        <span>{{ $lastSale->customer_name }}</span>
                    </div>
                </div>
                
                <div class="border-t border-dashed border-zinc-300 my-2"></div>
                
                <div class="space-y-1">
                    @foreach ($lastSale->items as $item)
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
                    <div class="flex justify-between font-bold">
                        <span>TOTAL</span>
                        <span>Rp {{ number_format($lastSale->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span>Payment ({{ strtoupper($lastSale->payment_method) }})</span>
                        <span>Rp {{ number_format($lastSale->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    @if ($lastSale->payment_method === 'cash')
                        <div class="flex justify-between text-xs">
                            <span>Change</span>
                            <span>Rp {{ number_format($lastSale->change_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
                
                <div class="border-t border-dashed border-zinc-300 my-2"></div>
                
                <div class="text-center text-xs text-zinc-500">
                    <div>Thank you for your purchase!</div>
                    <div>Please come again</div>
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
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        body { font-family: monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 10px; }
                        .text-center { text-align: center; }
                        .flex { display: flex; justify-content: space-between; }
                        .font-bold { font-weight: bold; }
                        .border-t { border-top: 1px dashed #ccc; margin: 8px 0; }
                        .text-xs { font-size: 10px; }
                        .text-lg { font-size: 14px; }
                        .mb-4 { margin-bottom: 16px; }
                        .my-2 { margin: 8px 0; }
                        .space-y-1 > * + * { margin-top: 4px; }
                    </style>
                </head>
                <body>
                    ${content.innerHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        }
    });
</script>
@endscript
