<flux:modal name="delete-category" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Are you sure?</flux:heading>
            <flux:text class="mt-2">
                <p>This category will be deleted permanently.</p>
                <p>This action cannot be undone.</p>
            </flux:text>
        </div>
        <div class="flex gap-4">
            <flux:spacer />
            <flux:modal.close>
                <flux:button size="sm" variant="primary">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="delete" size="sm" variant="danger">Delete</flux:button>
        </div>
    </div>
</flux:modal>
