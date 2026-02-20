<flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-950 border-r border-zinc-200 dark:border-zinc-800">
    <flux:sidebar.header>
        <flux:sidebar.brand
            href="#"
            logo="{{ asset('assets/icon.webp') }}"
            name="{{ config('app.name') }}"
        />
        <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
    </flux:sidebar.header>
        <flux:sidebar.nav>
            <flux:sidebar.group heading="Home" />
            <flux:sidebar.item icon="layout-dashboard" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')" wire:navigate.hover>Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="server" href="{{ route('nodes') }}" :current="request()->routeIs('nodes')" wire:navigate.hover>Nodes</flux:sidebar.item>
            <flux:sidebar.item icon="folder-archive" href="{{ route('recipes') }}" :current="request()->routeIs('recipes')" wire:navigate.hover>Recipes</flux:sidebar.item>
            <flux:sidebar.item icon="chart-area" href="{{ route('analytics') }}" :current="request()->routeIs('analytics')" wire:navigate.hover>Analytics</flux:sidebar.item>
            <flux:sidebar.item icon="bot-message-square" href="{{ route('node-gtp') }}" :current="request()->routeIs('node-gtp')" wire:navigate.hover>Node GTP</flux:sidebar.item>
            <flux:sidebar.item icon="banknote-arrow-down" href="{{ route('payouts') }}" :current="request()->routeIs('payouts')" wire:navigate.hover>Payouts</flux:sidebar.item>
            <flux:sidebar.group heading="Settings" />
            <flux:sidebar.item icon="user" href="{{ route('settings.profile') }}" :current="request()->routeIs('settings.profile')" wire:navigate.hover>Profile</flux:sidebar.item>
            <flux:sidebar.item icon="users" href="{{ route('settings.users') }}" :current="request()->routeIs('settings.users')" wire:navigate.hover>Users</flux:sidebar.item>
            <flux:sidebar.item icon="key" href="{{ route('settings.ssh-key') }}" :current="request()->routeIs('settings.ssh-key')" wire:navigate.hover>SSH Key</flux:sidebar.item>
            <flux:sidebar.item icon="bell" href="{{ route('settings.notification') }}" :current="request()->routeIs('settings.notification')" wire:navigate.hover>Notification</flux:sidebar.item>
            <flux:sidebar.item icon="bot" href="{{ route('settings.ai') }}" :current="request()->routeIs('settings.ai')" wire:navigate.hover>AI</flux:sidebar.item>
            <flux:sidebar.item icon="file-cog" href="{{ route('settings.integrations') }}" :current="request()->routeIs('settings.integrations')" wire:navigate.hover>Integrations</flux:sidebar.item>
        </flux:sidebar.nav>
    <flux:sidebar.spacer />
    @include('partials.user-menu', ['class' => 'max-lg:hidden'])
</flux:sidebar>
