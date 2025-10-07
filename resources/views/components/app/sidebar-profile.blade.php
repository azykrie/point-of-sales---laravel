<flux:dropdown position="top" align="start" class="max-lg:hidden">
    <flux:sidebar.profile :initials="auth()->user()->initials()" icon-trailing="chevron-down"
        name="{{ auth()->user()->name }}" />

    <flux:menu>
        
        <flux:menu.item>
            <flux:switch x-data x-model="$flux.dark" label="Dark mode" />
        </flux:menu.item>


        <flux:menu.separator />

        <livewire:auth.logout />
    </flux:menu>
</flux:dropdown>
