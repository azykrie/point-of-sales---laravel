<div class="space-y-6">
    <flux:heading size="xl">Create Category</flux:heading>

    <form wire:submit.prevent="save" class="space-y-4">
        <flux:field>
            <flux:input label="Category Name" type="text" wire:model="name" placeholder="Enter category name" />
        </flux:field>

        <div class="flex justify-end gap-2">
            <flux:button href="{{ route('admin.categories.index') }}" wire:navigate>
                Cancel
            </flux:button>

            <flux:button type="submit">
                Save
            </flux:button>
        </div>
    </form>
</div>
