<flux:dropdown position="top" align="start" {{ $attributes->merge(['class' => '']) }}>
    <flux:sidebar.profile avatar="https://fluxui.dev/img/demo/user.png" name="Olivia Martin" />
    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
            <flux:menu.radio>Truly Delta</flux:menu.radio>
        </flux:menu.radio.group>
        <flux:menu.separator />
        <form method="POST" action="{{ route('logout') }}" class="contents">
            @csrf
            <flux:menu.item icon="arrow-right-start-on-rectangle" as="button" type="submit">Logout</flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
