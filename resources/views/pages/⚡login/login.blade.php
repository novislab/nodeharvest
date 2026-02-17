<div class="space-y-6">
    <div class="flex justify-center">
        <x-logo />
    </div>

    <flux:heading class="text-center" size="xl">Welcome back</flux:heading>

    <x-auth.social-buttons />

    <flux:separator text="or" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <flux:input wire:model="email" label="Email" type="email" placeholder="email@example.com"
            autocomplete="email" required/>

        <flux:input wire:model="password" label="Password" type="password" placeholder="Your password"
                autocomplete="current-password" viewable required/>

        <flux:checkbox wire:model="remember" label="Remember me" />

        <flux:button type="submit" variant="primary" class="w-full">Log in</flux:button>
    </form>

    <flux:subheading class="text-center">
        First time around here? <flux:link href="{{ route('register') }}" wire:navigate.hover>Create an account</flux:link>
    </flux:subheading>
</div>
