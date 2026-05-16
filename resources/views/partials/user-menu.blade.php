<flux:dropdown position="top" align="start" {{ $attributes->merge(['class' => '']) }}>
    <flux:sidebar.profile name="{{ auth()->user()->name }}" avatar="{{ asset('storage/avatar/' . auth()->user()->avatar) }}" />
    <flux:menu>
        <flux:menu.item href="{{ route('settings.profile') }}" icon="user" wire:navigate>Profile</flux:menu.item>
        <flux:menu.separator />
        <form method="POST" action="{{ route('action.logout') }}" class="contents">
            @csrf
            <flux:menu.item icon="arrow-right-start-on-rectangle" as="button" type="submit">Logout</flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
