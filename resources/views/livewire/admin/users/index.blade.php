<div>
    <div class="flex items-center justify-between mb-4">
        <div class="text-2xl font-semibold">Users Table</div>
        <div class="flex items-center gap-2">
            <flux:input wire:model.live="search" placeholder="Search..." class="w-40" />
            <flux:button href="{{ route('admin.users.create') }}" wire:navigate>
                + Create
            </flux:button>
        </div>
    </div>
    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Action</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row>
                    <flux:table.cell>{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($user->role == 'admin')
                            <flux:badge color="red" size="sm" inset="top bottom">{{ $user->role }}</flux:badge>
                        @elseif ($user->role == 'cashier')
                            <flux:badge color="blue" size="sm" inset="top bottom">{{ $user->role }}
                            </flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" href="{{ route('admin.users.edit', $user->id) }}" wire:navigate>Edit
                        </flux:button>
                        <flux:modal.trigger name="delete-user">
                            <flux:button size="sm" wire:click="confirmDelete({{ $user->id }})">Delete</flux:button>
                        </flux:modal.trigger>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center">
                        No users found.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    @include('livewire.admin.users.delete')
</div>
