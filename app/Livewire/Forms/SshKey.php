<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Livewire\Form;

class SshKey extends Form
{
    public string $name = '';

    public string $key = '';

    public bool $is_default = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'regex:/^(ssh-rsa|ssh-dss|ecdsa-sha2-nistp256|ecdsa-sha2-nistp384|ecdsa-sha2-nistp521|ssh-ed25519)\s+/'],
            'is_default' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'key.regex' => 'The key must be a valid SSH key.',
        ];
    }
}
