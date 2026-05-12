<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class SshKey extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string')]
    public string $key = '';

    public function validateKey($component): void
    {
        if (! preg_match('/^(ssh-rsa|ssh-dss|ecdsa-sha2-nistp256|ecdsa-sha2-nistp384|ecdsa-sha2-nistp521|ssh-ed25519)\s+/', $this->key)) {
            $component->addError('form.key', 'The key must be a valid SSH key.');
        }
    }

    #[Validate('boolean')]
    public bool $is_default = false;
}
