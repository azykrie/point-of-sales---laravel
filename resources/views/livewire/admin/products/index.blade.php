<div>
    <div class="flex items-center justify-between mb-4">
        <div class="text-2xl font-semibold">Products Table</div>
        <div class="flex items-center gap-2">
            <flux:dropdown>
                <flux:button variant="ghost" icon="funnel" icon-trailing="chevron-down">
                    Filter
                    @if($filterCategory || $filterAvailability || $filterStock)
                        <flux:badge color="blue" size="sm">!</flux:badge>
                    @endif
                </flux:button>
                <flux:menu>
                    <flux:menu.submenu heading="Category">
                        <flux:menu.radio.group wire:model.live="filterCategory">
                            <flux:menu.radio value="">All Categories</flux:menu.radio>
                            @foreach ($categories as $category)
                                <flux:menu.radio value="{{ $category->id }}">{{ $category->name }}</flux:menu.radio>
                            @endforeach
                        </flux:menu.radio.group>
                    </flux:menu.submenu>
                    <flux:menu.submenu heading="Availability">
                        <flux:menu.radio.group wire:model.live="filterAvailability">
                            <flux:menu.radio value="">All Status</flux:menu.radio>
                            <flux:menu.radio value="available">Available</flux:menu.radio>
                            <flux:menu.radio value="unavailable">Unavailable</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu.submenu>
                    <flux:menu.submenu heading="Stock Level">
                        <flux:menu.radio.group wire:model.live="filterStock">
                            <flux:menu.radio value="">All Stock</flux:menu.radio>
                            <flux:menu.radio value="in_stock">In Stock (>10)</flux:menu.radio>
                            <flux:menu.radio value="low_stock">Low Stock (1-10)</flux:menu.radio>
                            <flux:menu.radio value="out_of_stock">Out of Stock</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu.submenu>
                    <flux:menu.separator />
                    <flux:menu.item wire:click="resetFilters" icon="x-mark" variant="danger">Reset Filters</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
            
            <flux:dropdown>
                <flux:button variant="ghost" icon-trailing="chevron-down">
                    Actions
                    @if (count($selectedProducts) > 0)
                        <flux:badge color="blue" size="sm" class="ml-1">{{ count($selectedProducts) }}</flux:badge>
                    @endif
                </flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="exportCsv" icon="document-text">Export CSV</flux:menu.item>
                    <flux:menu.item wire:click="exportExcel" icon="table-cells">Export Excel</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item href="{{ route('admin.products.print-barcode', ['ids' => implode(',', $selectedProducts)]) }}" target="_blank" icon="printer">Print Barcode</flux:menu.item>
                    @if (count($selectedProducts) > 0)
                        <flux:menu.separator />
                        <flux:modal.trigger name="delete-selected">
                            <flux:menu.item icon="trash" variant="danger">Delete Selected</flux:menu.item>
                        </flux:modal.trigger>
                    @endif
                </flux:menu>
            </flux:dropdown>
            
            <flux:input wire:model.live="search" placeholder="Search..." icon="magnifying-glass" class="w-48" />
            
            <flux:button href="{{ route('admin.products.create') }}" wire:navigate>
                + Create
            </flux:button>
        </div>
    </div>

    <flux:table :paginate="$products">
        <flux:table.columns>
            <flux:table.column class="w-12">
                <flux:checkbox wire:model.live="selectAll" />
            </flux:table.column>
            <flux:table.column>Image</flux:table.column>
            <flux:table.column>Barcode</flux:table.column>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Category</flux:table.column>
            <flux:table.column>Buy Price</flux:table.column>
            <flux:table.column>Sell Price</flux:table.column>
            <flux:table.column>Stock</flux:table.column>
            <flux:table.column>Available</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($products as $product)
                <flux:table.row>
                    <flux:table.cell>
                        <flux:checkbox wire:model.live="selectedProducts" value="{{ $product->id }}" />
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-gray-400 text-xs">No Image</span>
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="font-mono text-sm">{{ $product->barcode ?? '-' }}</span>
                    </flux:table.cell>
                    <flux:table.cell>{{ $product->name }}</flux:table.cell>
                    <flux:table.cell>{{ $product->category->name ?? '-' }}</flux:table.cell>
                    <flux:table.cell>Rp {{ number_format($product->price, 0, ',', '.') }}</flux:table.cell>
                    <flux:table.cell>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($product->stock > 10)
                            <flux:badge color="green" size="sm" inset="top bottom">{{ $product->stock }}</flux:badge>
                        @elseif ($product->stock > 0)
                            <flux:badge color="yellow" size="sm" inset="top bottom">{{ $product->stock }}</flux:badge>
                        @else
                            <flux:badge color="red" size="sm" inset="top bottom">Out of Stock</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:checkbox wire:click="toggleAvailable({{ $product->id }})" :checked="$product->is_available" />
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" href="{{ route('admin.products.edit', $product->id) }}" wire:navigate>Edit
                        </flux:button>
                        <flux:modal.trigger name="delete-product">
                            <flux:button size="sm" wire:click="confirmDelete({{ $product->id }})">Delete</flux:button>
                        </flux:modal.trigger>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="10" class="text-center">
                        No products found.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    
    @include('livewire.admin.products.delete')

    <!-- Delete Selected Modal -->
    <flux:modal name="delete-selected" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Selected Products</flux:heading>
                <flux:text class="mt-2">Are you sure you want to delete {{ count($selectedProducts) }} selected products? This action cannot be undone.</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="deleteSelected">Delete All</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
