<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PragmaRX\Google2FAQRCode\Google2FA;

new #[Layout('layouts::auth')] #[Title('Login')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public bool $showTwoFactor = false;

    public string $twoFactorCode = '';

    public function mount(): void
    {
        if (Session::has('login.2fa.id')) {
            $this->showTwoFactor = true;
        }
    }

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

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key, 60);
            Flux::toast(
                text: "Oops! That doesn't look right. Want to try again?",
                variant: 'danger',
            );

            return;
        }

        $user = Auth::user();

        if ($user instanceof User && $user->two_factor_enabled) {
            Session::put('login.2fa.id', $user->id);
            Session::put('login.2fa.remember', $this->remember);
            Auth::logout();
            $this->showTwoFactor = true;

            return;
        }

        RateLimiter::clear($key);
        Session::regenerate();
        Flux::toast(text: 'Great to see you again!', variant: 'success');
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function verifyTwoFactor(): void
    {
        $this->validate([
            'twoFactorCode' => ['required', 'string', 'size:6'],
        ]);

        $userId = Session::get('login.2fa.id');

        if (! $userId) {
            $this->cancelTwoFactor();

            return;
        }

        $user = User::find($userId);

        if (! $user || ! $user->two_factor_secret) {
            $this->cancelTwoFactor();

            return;
        }

        $google2fa = new Google2FA();

        if (! $google2fa->verifyKey($user->two_factor_secret, $this->twoFactorCode)) {
            Flux::toast(
                text: 'Invalid authentication code. Please try again.',
                variant: 'danger',
            );
            $this->twoFactorCode = '';

            return;
        }

        $remember = (bool) Session::get('login.2fa.remember', false);

        Auth::login($user, $remember);
        Session::forget(['login.2fa.id', 'login.2fa.remember']);
        Session::regenerate();
        Flux::toast(text: 'Great to see you again!', variant: 'success');
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function cancelTwoFactor(): void
    {
        Session::forget(['login.2fa.id', 'login.2fa.remember']);
        $this->showTwoFactor = false;
        $this->twoFactorCode = '';
    }
};
