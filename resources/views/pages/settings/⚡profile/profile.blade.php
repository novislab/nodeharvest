<div class="space-y-6 pb-16">
    <x-dashboard.page-header :breadcrumbs="[['label' => 'Profile']]" />

    <div class="grid grid-cols-1 gap-6">
        <flux:card class="space-y-6">
            <div class="flex items-center gap-4">
                <flux:avatar
                    src="{{ asset('storage/avatar/' . auth()->user()->avatar) }}"
                    :name="auth()->user()->name"
                    size="xl"
                />

                <div class="flex flex-col gap-2">
                    <flux:button icon="arrow-up-tray" wire:loading.attr="disabled">
                        <label for="avatar-upload" class="cursor-pointer">
                            Upload new picture
                        </label>
                    </flux:button>
                    <input
                        id="avatar-upload"
                        type="file"
                        wire:model="avatar"
                        class="hidden"
                        accept="image/*"
                    />

                    @if (auth()->user()->avatar !== 'delfault.jpg')
                        <flux:button
                            variant="ghost"
                            size="sm"
                            wire:click="deleteAvatar"
                            wire:confirm="Are you sure you want to remove your profile picture?"
                        >
                            Remove picture
                        </flux:button>
                    @endif
                </div>
            </div>

            <flux:separator />

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input type="email" wire:model="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:button variant="primary" wire:click="save">
                    Save changes
                </flux:button>
            </div>
        </flux:card>

        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">Password</flux:heading>
                <flux:subheading>
                    Update your account password.
                </flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Current password</flux:label>
                    <flux:input
                        wire:model="currentPassword"
                        type="password"
                        autocomplete="current-password"
                        viewable
                    />
                    <flux:error name="currentPassword" />
                </flux:field>

                <flux:field>
                    <flux:label>New password</flux:label>
                    <flux:input
                        wire:model="newPassword"
                        type="password"
                        autocomplete="new-password"
                        viewable
                    />
                    <flux:error name="newPassword" />
                </flux:field>

                <flux:field>
                    <flux:label>Confirm new password</flux:label>
                    <flux:input
                        wire:model="newPasswordConfirmation"
                        type="password"
                        autocomplete="new-password"
                        viewable
                    />
                    <flux:error name="newPasswordConfirmation" />
                </flux:field>

                <flux:button variant="primary" wire:click="updatePassword">
                    Update password
                </flux:button>
            </div>
        </flux:card>

        <flux:card class="space-y-6" x-data="{ open: false }">
            <div>
                <flux:heading size="lg">Two-factor authentication</flux:heading>
                <flux:subheading>
                    Add an extra layer of security to your account.
                </flux:subheading>
            </div>

            @if ($showTwoFactorSetup)
                <div class="space-y-4">
                    <flux:callout variant="warning">
                        Scan the QR code with your authenticator app and enter the 6-digit code to confirm.
                    </flux:callout>

                    <div class="flex justify-center">
                        <img src="{{ $twoFactorQrCode }}" alt="Two-factor QR code" class="h-48 w-48 rounded-lg border bg-white" />
                    </div>

                    <flux:field>
                        <flux:label>Authentication code</flux:label>
                        <flux:input
                            wire:model="twoFactorCode"
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="6"
                            placeholder="000000"
                            autocomplete="one-time-code"
                        />
                        <flux:error name="twoFactorCode" />
                    </flux:field>

                    <div class="flex gap-2">
                        <flux:button variant="primary" wire:click="confirmTwoFactor">
                            Confirm and enable
                        </flux:button>
                        <flux:button variant="ghost" wire:click="cancelTwoFactorSetup">
                            Cancel
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="sm">
                            {{ auth()->user()->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                        </flux:heading>
                        <flux:subheading size="sm">
                            {{ auth()->user()->two_factor_enabled
                                ? 'Your account is protected with an authenticator app.'
                                : 'Your account is not protected with two-factor authentication.' }}
                        </flux:subheading>
                    </div>

                    @if (auth()->user()->two_factor_enabled)
                        <flux:button variant="danger" x-on:click="open = true">
                            Disable
                        </flux:button>
                    @else
                        <flux:button variant="primary" wire:click="enableTwoFactor">
                            Enable
                        </flux:button>
                    @endif
                </div>
            @endif

            <div x-show="open" x-cloak class="relative z-50">
                <div
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
                    x-on:click="open = false"
                ></div>
                <div class="fixed inset-0 flex items-center justify-center p-4">
                    <div
                        class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-900"
                        x-on:click.away="open = false"
                    >
                        <flux:heading size="lg">Disable two-factor authentication?</flux:heading>
                        <flux:subheading class="mt-2">
                            This will remove the extra security layer from your account. You can re-enable it at any time.
                        </flux:subheading>

                        <div class="mt-6 flex justify-end gap-3">
                            <flux:button variant="ghost" x-on:click="open = false">
                                Cancel
                            </flux:button>
                            <flux:button variant="danger" x-on:click="open = false; $wire.disableTwoFactor()">
                                Disable
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card class="space-y-6" x-data="{
            registering: false,
            passkeyName: '',
            async registerPasskey() {
                if (! window.PublicKeyCredential) {
                    $dispatch('toast-show', { text: 'Passkeys are not supported in this browser.', variant: 'danger' });
                    return;
                }

                this.registering = true;

                try {
                    const response = await fetch('{{ route('passkey.registration-options') }}');

                    if (response.status === 403 || response.redirected) {
                        $dispatch('toast-show', { text: 'Please confirm your password first.', variant: 'danger' });
                        this.registering = false;
                        return;
                    }

                    const { options } = await response.json();

                    options.challenge = this.base64urlToBuffer(options.challenge);
                    options.user.id = this.base64urlToBuffer(options.user.id);

                    if (options.excludeCredentials) {
                        options.excludeCredentials = options.excludeCredentials.map(cred => ({
                            ...cred,
                            id: this.base64urlToBuffer(cred.id),
                        }));
                    }

                    const credential = await navigator.credentials.create({ publicKey: options });

                    if (! credential) {
                        this.registering = false;
                        return;
                    }

                    const storeResponse = await fetch('{{ route('passkey.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            name: this.passkeyName || 'Passkey',
                            credential: this.credentialToJson(credential),
                        }),
                    });

                    if (storeResponse.ok) {
                        $dispatch('toast-show', { text: 'Passkey added successfully.', variant: 'success' });
                        $wire.showPasskeyModal = false;
                        $wire.loadPasskeys();
                        this.passkeyName = '';
                    } else {
                        const data = await storeResponse.json();
                        $dispatch('toast-show', { text: data.message || 'Unable to register passkey. Please try again.', variant: 'danger' });
                    }
                } catch (error) {
                    if (error.name === 'NotAllowedError') {
                        this.registering = false;
                        return;
                    }

                    $dispatch('toast-show', { text: 'Unable to register passkey. Please try again.', variant: 'danger' });
                } finally {
                    this.registering = false;
                }
            },

            base64urlToBuffer(base64url) {
                const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
                const binary = atob(base64);
                const buffer = new ArrayBuffer(binary.length);
                const view = new Uint8Array(buffer);

                for (let i = 0; i < binary.length; i++) {
                    view[i] = binary.charCodeAt(i);
                }

                return buffer;
            },

            bufferToBase64url(buffer) {
                const binary = String.fromCharCode(...new Uint8Array(buffer));
                return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            },

            credentialToJson(credential) {
                const response = credential.response;

                return {
                    id: credential.id,
                    rawId: this.bufferToBase64url(credential.rawId),
                    type: credential.type,
                    response: {
                        clientDataJSON: this.bufferToBase64url(response.clientDataJSON),
                        attestationObject: this.bufferToBase64url(response.attestationObject),
                    },
                };
            },
        }">
            <div>
                <flux:heading size="lg">Passkeys</flux:heading>
                <flux:subheading>
                    Sign in securely without a password using your device biometrics or security key.
                </flux:subheading>
            </div>

            @if (count($passkeys) > 0)
                <div class="space-y-3">
                    @foreach ($passkeys as $passkey)
                        <div class="flex items-center justify-between rounded-lg border px-4 py-3">
                            <div class="flex items-center gap-3">
                                <flux:icon.key class="size-5 text-zinc-500" />
                                <div>
                                    <p class="text-sm font-medium">{{ $passkey['name'] }}</p>
                                    <p class="text-xs text-zinc-500">
                                        @if ($passkey['authenticator'])
                                            {{ $passkey['authenticator'] }} &middot;
                                        @endif
                                        Last used {{ $passkey['last_used_at'] }}
                                    </p>
                                </div>
                            </div>
                            <flux:button
                                variant="ghost"
                                size="sm"
                                wire:click="deletePasskey({{ $passkey['id'] }})"
                                wire:confirm="Are you sure you want to remove this passkey?"
                            >
                                <flux:icon.trash class="size-4 text-red-500" />
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            @else
                <flux:subheading size="sm">
                    You haven't added any passkeys yet.
                </flux:subheading>
            @endif

            <flux:button
                type="button"
                variant="primary"
                x-on:click="$wire.showPasskeyModal = true"
            >
                <flux:icon.plus class="size-4" />
                Add passkey
            </flux:button>

            <div x-show="$wire.showPasskeyModal" x-cloak class="relative z-50">
                <div
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
                    x-on:click="$wire.showPasskeyModal = false"
                ></div>
                <div class="fixed inset-0 flex items-center justify-center p-4">
                    <div
                        class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-900"
                        x-on:click.away="$wire.showPasskeyModal = false"
                    >
                        <flux:heading size="lg">Add a new passkey</flux:heading>
                        <flux:subheading class="mt-2">
                            Give your passkey a name so you can identify it later.
                        </flux:subheading>

                        <div class="mt-4">
                            <flux:field>
                                <flux:label>Passkey name</flux:label>
                                <flux:input
                                    x-model="passkeyName"
                                    placeholder="e.g. MacBook Touch ID"
                                    autocomplete="off"
                                />
                            </flux:field>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <flux:button variant="ghost" x-on:click="$wire.showPasskeyModal = false">
                                Cancel
                            </flux:button>
                            <flux:button
                                variant="primary"
                                x-bind:disabled="registering"
                                x-on:click="registerPasskey()"
                            >
                                <span x-show="! registering">Register passkey</span>
                                <span x-show="registering">Registering...</span>
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>
</div>
