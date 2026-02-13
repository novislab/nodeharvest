<div class="space-y-6">
    <div class="flex justify-center">
        <x-logo />
    </div>

    <flux:heading class="text-center" size="xl">Create your account</flux:heading>

    <form wire:submit="register" class="flex flex-col gap-6">
        <flux:input wire:model="name" label="Name" type="text" placeholder="John Doe"
            autocomplete="name" required/>

        <flux:input wire:model="email" label="Email" type="email" placeholder="email@example.com"
            autocomplete="email" required/>

        <flux:input wire:model="password" label="Password" type="password" placeholder="Create a password"
            autocomplete="new-password" viewable required/>

        <flux:input wire:model="password_confirmation" label="Confirm Password" type="password" placeholder="Confirm your password"
            autocomplete="new-password" viewable required/>

        <flux:button type="submit" variant="primary" class="w-full">Create account</flux:button>
    </form>

    <flux:subheading class="text-center">
        Already have an account? <flux:link href="{{ route('login') }}" wire:navigate.hover>Sign in</flux:link>
    </flux:subheading>
</div>