<?php

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use PragmaRX\Google2FAQRCode\Google2FA;

new #[Layout('layouts::app')] #[Title('Profile')] class extends Component
{
    use WithFileUploads;

    public string $name;
    public string $email;
    public $avatar;

    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public bool $showTwoFactorSetup = false;
    public string $twoFactorSecret = '';
    public string $twoFactorQrCode = '';
    public string $twoFactorCode = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ]);

        Auth::user()->update($validated);
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required', 'current_password'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => bcrypt($this->newPassword),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
    }

    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => ['image', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->avatar !== 'delfault.jpg') {
            Storage::disk('public')->delete('avatar/' . $user->avatar);
        }

        $filename = $user->id . '_' . time() . '.' . $this->avatar->getClientOriginalExtension();
        $this->avatar->storeAs('avatar', $filename, 'public');

        $user->update(['avatar' => $filename]);

        $this->reset('avatar');
    }

    public function deleteAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar !== 'delfault.jpg') {
            Storage::disk('public')->delete('avatar/' . $user->avatar);
            $user->update(['avatar' => 'delfault.jpg']);
        }
    }

    public function enableTwoFactor(): void
    {
        $google2fa = new Google2FA();
        $this->twoFactorSecret = $google2fa->generateSecretKey();

        $qrUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            Auth::user()->email,
            $this->twoFactorSecret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($qrUrl);

        $qrDir = 'qr-2fa';
        Storage::disk('public')->makeDirectory($qrDir);
        $qrPath = $qrDir . '/' . Auth::id() . '_2fa.svg';
        Storage::disk('public')->put($qrPath, $svg);

        $this->twoFactorQrCode = asset('storage/' . $qrPath);
        $this->showTwoFactorSetup = true;
        $this->twoFactorCode = '';
    }

    public function confirmTwoFactor(): void
    {
        $this->validate([
            'twoFactorCode' => ['required', 'string', 'size:6'],
        ]);

        $google2fa = new Google2FA();

        if (! $google2fa->verifyKey($this->twoFactorSecret, $this->twoFactorCode)) {
            $this->addError('twoFactorCode', 'The code is invalid. Please try again.');
            $this->twoFactorCode = '';

            return;
        }

        Auth::user()->update([
            'two_factor_secret' => $this->twoFactorSecret,
            'two_factor_enabled' => true,
        ]);

        $this->cleanupQrCode();
        $this->showTwoFactorSetup = false;
        $this->twoFactorSecret = '';
        $this->twoFactorQrCode = '';
        $this->twoFactorCode = '';
    }

    public function cancelTwoFactorSetup(): void
    {
        $this->cleanupQrCode();
        $this->showTwoFactorSetup = false;
        $this->twoFactorSecret = '';
        $this->twoFactorQrCode = '';
        $this->twoFactorCode = '';
    }

    public function disableTwoFactor(): void
    {
        Auth::user()->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ]);
    }

    private function cleanupQrCode(): void
    {
        $qrPath = 'qr-2fa/' . Auth::id() . '_2fa.svg';
        if (Storage::disk('public')->exists($qrPath)) {
            Storage::disk('public')->delete($qrPath);
        }
    }
};
