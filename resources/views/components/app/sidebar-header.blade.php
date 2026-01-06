@php
    $storeName = \App\Models\Setting::get('store_name', 'My Store');
    $storeLogo = \App\Models\Setting::get('store_logo');
    $storeLogoDark = \App\Models\Setting::get('store_logo_dark');
@endphp

<flux:sidebar.header>
    <flux:sidebar.brand 
        href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard.index') : route('cashier.dashboard.index') }}" 
        wire:navigate
        :logo="$storeLogo ? asset($storeLogo) : null"
        :logo:dark="$storeLogoDark ? asset($storeLogoDark) : null"
        :name="$storeName" 
    />

    <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
</flux:sidebar.header>
