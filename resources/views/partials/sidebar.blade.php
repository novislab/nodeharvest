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
        <flux:sidebar.item icon="home" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')" wire:navigate.hover>Dashboard</flux:sidebar.item>
    </flux:sidebar.nav>
    <flux:sidebar.spacer />
    <flux:sidebar.nav>
        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
        <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
    </flux:sidebar.nav>
    @include('partials.user-menu', ['class' => 'max-lg:hidden'])
</flux:sidebar>
