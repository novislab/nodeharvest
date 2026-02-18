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
            <flux:sidebar.item icon="home" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')" wire:navigate.hover>Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="server" href="{{ route('nodes') }}" :current="request()->routeIs('nodes')" wire:navigate.hover>Nodes</flux:sidebar.item>
            <flux:sidebar.group heading="Settings" />
            <flux:sidebar.item icon="user" href="{{ route('settings.profile') }}" :current="request()->routeIs('settings.profile')" wire:navigate.hover>Profile</flux:sidebar.item>
        </flux:sidebar.nav>
    <flux:sidebar.spacer />
    @include('partials.user-menu', ['class' => 'max-lg:hidden'])
</flux:sidebar>
