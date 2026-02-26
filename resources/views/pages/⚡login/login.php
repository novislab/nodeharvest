<?php

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
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
            Flux::toast(text: 'Great to see you again!', variant: 'success');
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
