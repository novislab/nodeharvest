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
    </div>
</div>
