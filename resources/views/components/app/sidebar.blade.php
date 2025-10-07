<flux:sidebar.nav>

    @if (auth()->user()->role === 'admin')

        <flux:sidebar.item icon="home" href="{{ route('admin.dashboard.index') }}"
            :current="request()->routeIs('admin.dashboard.index')" wire:navigate>
            {{ __('Dashboard') }}
        </flux:sidebar.item>

        <flux:sidebar.item icon="users" href="{{ route('admin.users.index') }}"
            :current="request()->routeIs('admin.users.*')" wire:navigate>
            {{ __('Users') }}
        </flux:sidebar.item>

    @elseif (auth()->user()->role === 'user')

        <flux:sidebar.item icon="home" href="{{ route('user.dashboard.index') }}"
            :current="request()->routeIs('user.dashboard.*')" wire:navigate>
            {{ __('Dashboard') }}
        </flux:sidebar.item>

    @endif


</flux:sidebar.nav>

<flux:sidebar.spacer />
