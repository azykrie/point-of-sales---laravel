<div class="space-y-6">
    <flux:heading size="xl">Create Product</flux:heading>

    <form wire:submit.prevent="save" class="space-y-4">
        <flux:field>
            <flux:input label="Product Name" type="text" wire:model="name" placeholder="Enter product name" />
        </flux:field>

        <flux:field>
            <flux:input label="Barcode" type="text" wire:model="barcode" placeholder="Auto generate if empty" description="Leave empty to auto generate" />
        </flux:field>

        <flux:field>
            <flux:select label="Category" wire:model="category_id">
                <option value="">-- Choose Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
            <div class="mt-1 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-start gap-2">
                    <flux:icon.information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                    <div>
                        <p class="text-sm text-blue-700 dark:text-blue-300 font-medium">Initial stock is set to 0</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">To add stock, please use the <a href="{{ route('admin.stock-movements.index') }}" class="underline font-semibold hover:text-blue-800 dark:hover:text-blue-200">Stock Movements</a> feature after creating this product.</p>
                    </div>
                </div>
            </div>
        </flux:field>

        <flux:field>
            <flux:textarea label="Description" wire:model="description" placeholder="Enter product description (optional)" rows="3" />
        </flux:field>

        <flux:field>
            <flux:label>Product Image</flux:label>
            <input type="file" wire:model="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            @if ($image)
                <div class="mt-2">
                    <img src="{{ $image->temporaryUrl() }}" class="w-32 h-32 object-cover rounded" alt="Preview">
                </div>
            @endif
            @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </flux:field>

        <div class="flex justify-end gap-2">
            <flux:button href="{{ route('admin.products.index') }}" wire:navigate>
                Cancel
            </flux:button>

            <flux:button type="submit">
                Save
            </flux:button>
        </div>
    </form>
</div>
