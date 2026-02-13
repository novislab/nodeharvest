<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::auth')] #[Title('Register')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);
        Flux::toast(text: 'Welcome to NodeHarvest!');
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
};
?>

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
