<?php

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::auth')] #[Title('Login')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = 'login.'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            Flux::toast(
                text: "Too many attempts. Please try again in {$seconds} seconds.",
                variant: 'danger',
            );

            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($key);
            Session::regenerate();
            Flux::toast(text: 'Great to see you again!');
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        RateLimiter::hit($key, 60);
        Flux::toast(
            text: "Oops! That doesn't look right. Want to try again?",
            variant: 'danger',
        );
    }
};
?>

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
