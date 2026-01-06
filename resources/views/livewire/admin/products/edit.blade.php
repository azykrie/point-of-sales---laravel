<div class="space-y-6">
    <flux:heading size="xl">Edit Product</flux:heading>

    <form wire:submit.prevent="update" class="space-y-4">
        <flux:field>
            <flux:input label="Product Name" type="text" wire:model="name" placeholder="Enter product name" />
        </flux:field>

        <flux:field>
            <flux:input label="Barcode" type="text" wire:model="barcode" placeholder="Enter barcode (optional)" />
        </flux:field>

        <flux:field>
            <flux:select label="Category" wire:model="category_id" variant="listbox" searchable placeholder="Choose category...">
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:input label="Buy Price" type="number" wire:model="price" placeholder="Enter buy price" step="0.01" min="0" />
        </flux:field>

        <flux:field>
            <flux:input label="Sell Price" type="number" wire:model="selling_price" placeholder="Enter sell price" step="0.01" min="0" />
        </flux:field>

        <flux:field>
            <flux:label>Stock</flux:label>
            <div class="mt-1 p-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:icon.cube class="w-5 h-5 text-zinc-500" />
                        <span class="text-lg font-semibold">{{ $stock }}</span>
                        <span class="text-sm text-zinc-500">units</span>
                    </div>
                    <a href="{{ route('admin.stock-movements.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">
                        Manage Stock →
                    </a>
                </div>
                <p class="text-xs text-zinc-500 mt-2">Stock can only be modified through Stock Movements</p>
            </div>
        </flux:field>

        <flux:field>
            <flux:textarea label="Description" wire:model="description" placeholder="Enter product description (optional)" rows="3" />
        </flux:field>

        <flux:field>
            <flux:label>Product Image</flux:label>
            @if ($existingImage && !$image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $existingImage) }}" class="w-32 h-32 object-cover rounded" alt="Current Image">
                    <p class="text-sm text-gray-500 mt-1">Current image</p>
                </div>
            @endif
            <input type="file" wire:model="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            @if ($image)
                <div class="mt-2">
                    <img src="{{ $image->temporaryUrl() }}" class="w-32 h-32 object-cover rounded" alt="Preview">
                    <p class="text-sm text-gray-500 mt-1">New image preview</p>
                </div>
            @endif
            @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </flux:field>

        <div class="flex justify-end gap-2">
            <flux:button href="{{ route('admin.products.index') }}" wire:navigate>
                Cancel
            </flux:button>

            <flux:button type="submit">
                Update
            </flux:button>
        </div>
    </form>
</div>
