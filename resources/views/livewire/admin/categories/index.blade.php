<div>
    <div class="flex items-center justify-between mb-4">
        <div class="text-2xl font-semibold">Categories Table</div>
        <div class="flex items-center gap-2">
            <flux:input wire:model.live="search" placeholder="Search..." class="w-40" />
            <flux:button href="{{ route('admin.categories.create') }}" wire:navigate>
                + Create
            </flux:button>
        </div>
    </div>
    <flux:table :paginate="$categories">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Created At</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($categories as $category)
                <flux:table.row>
                    <flux:table.cell>{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>{{ $category->created_at->format('d M Y') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" href="{{ route('admin.categories.edit', $category->id) }}" wire:navigate>Edit
                        </flux:button>
                        <flux:modal.trigger name="delete-category">
                            <flux:button size="sm" wire:click="confirmDelete({{ $category->id }})">Delete</flux:button>
                        </flux:modal.trigger>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center">
                        No categories found.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    @include('livewire.admin.categories.delete')
</div>
