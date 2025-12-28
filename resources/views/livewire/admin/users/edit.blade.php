<div class="space-y-6">
    <flux:heading size="xl">Edit User</flux:heading>

    <form wire:submit.prevent="update" class="space-y-4">
        <flux:field>
            <flux:input label="Name" type="text" wire:model="name" placeholder="Enter name" />
        </flux:field>

        <flux:field>
            <flux:input label="Email" type="email" wire:model="email" placeholder="Enter email" />
        </flux:field>

        <flux:field>
            <flux:input label="Password"  type="password" wire:model="password"
                placeholder="Leave it blank if not changing" />
        </flux:field>

        <flux:field>
            <flux:input label="Confirm Password" type="password"
                wire:model="password_confirmation" placeholder="Leave it blank if not changing" />
        </flux:field>

        <flux:field>
            <flux:select label="Role" wire:model="role">
                <option value="">-- Choose Role --</option>
                <option value="admin">Admin</option>
                <option value="cashier">Cashier</option>
            </flux:select>
        </flux:field>

        <div class="flex justify-end gap-2">
            <flux:button href="{{ route('admin.users.index') }}" wire:navigate>
                Cancel
            </flux:button>

            <flux:button type="submit">
                Save
            </flux:button>
        </div>
    </form>
</div>
