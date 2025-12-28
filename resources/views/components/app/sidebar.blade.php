<flux:sidebar.nav>

    @if (auth()->user()->role === 'admin')
        <flux:sidebar.item icon="home" href="{{ route('admin.dashboard.index') }}"
            :current="request()->routeIs('admin.dashboard.index')" wire:navigate>
            {{ __('Dashboard') }}
        </flux:sidebar.item>

        <flux:sidebar.group expandable heading="Point of Sale" class="grid">
            <flux:sidebar.item icon="shopping-cart" href="{{ route('admin.cashiers.index') }}"
                :current="request()->routeIs('admin.cashiers.index')" wire:navigate>
                {{ __('Cashier') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="receipt-percent" href="{{ route('admin.sales.index') }}"
                :current="request()->routeIs('admin.sales.*')" wire:navigate>
                {{ __('Sales History') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="arrow-uturn-left" href="{{ route('admin.refunds.index') }}"
                :current="request()->routeIs('admin.refunds.*')" wire:navigate>
                {{ __('Refund History') }}
            </flux:sidebar.item>
        </flux:sidebar.group>


        <flux:sidebar.group expandable heading="Product Management" class="grid">
            <flux:sidebar.item icon="cube" href="{{ route('admin.categories.index') }}"
                :current="request()->routeIs('admin.categories.*')" wire:navigate>
                {{ __('Categories') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="shopping-cart" href="{{ route('admin.products.index') }}"
                :current="request()->routeIs('admin.products.*')" wire:navigate>
                {{ __('Products') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="arrow-path" href="{{ route('admin.stock-movements.index') }}"
                :current="request()->routeIs('admin.stock-movements.*')" wire:navigate>
                {{ __('Stock Movements') }}
            </flux:sidebar.item>
        </flux:sidebar.group>


        <flux:sidebar.item icon="users" href="{{ route('admin.users.index') }}"
            :current="request()->routeIs('admin.users.*')" wire:navigate>
            {{ __('Users') }}
        </flux:sidebar.item>
    @elseif (auth()->user()->role === 'cashier')
        <flux:sidebar.item icon="home" href="{{ route('cashier.dashboard.index') }}"
            :current="request()->routeIs('cashier.dashboard.*')" wire:navigate>
            {{ __('Dashboard') }}
        </flux:sidebar.item>
    @endif


</flux:sidebar.nav>

<flux:sidebar.spacer />
