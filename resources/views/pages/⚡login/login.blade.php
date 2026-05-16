<div
    x-data="{
        async signInWithPasskey() {
            if (!window.PublicKeyCredential) {
                $dispatch('toast-show', { text: 'Passkeys are not supported in this browser.', variant: 'danger' });
                return;
            }

            try {
                const response = await fetch('{{ route('passkey.login-options') }}');
                const { options } = await response.json();

                options.challenge = this.base64urlToBuffer(options.challenge);

                if (options.allowCredentials) {
                    options.allowCredentials = options.allowCredentials.map(cred => ({
                        ...cred,
                        id: this.base64urlToBuffer(cred.id),
                    }));
                }

                const credential = await navigator.credentials.get({ publicKey: options });

                if (!credential) {
                    return;
                }

                const storeResponse = await fetch('{{ route('passkey.login') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        credential: this.credentialToJson(credential),
                        remember: true,
                    }),
                });

                if (!storeResponse.ok) {
                    const data = await storeResponse.json();
                    $dispatch('toast-show', { text: data.message || 'Unable to sign in with passkey. Please try again.', variant: 'danger' });
                    return;
                }

                const data = await storeResponse.json();
                window.location.href = data.redirect;
            } catch (error) {
                if (error.name === 'NotAllowedError') {
                    return;
                }

                $dispatch('toast-show', { text: 'Unable to sign in with passkey. Please try again.', variant: 'danger' });
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
                    authenticatorData: this.bufferToBase64url(response.authenticatorData),
                    signature: this.bufferToBase64url(response.signature),
                    userHandle: response.userHandle ? this.bufferToBase64url(response.userHandle) : null,
                },
            };
        },
    }"
    class="space-y-6"
>
    <div class="flex justify-center">
        <x-logo />
    </div>

    <flux:heading class="text-center" size="xl">
        {{ $showTwoFactor ? 'Two-factor authentication' : 'Welcome back' }}
    </flux:heading>

    @if (! $showTwoFactor)
        <x-auth.social-buttons />

        <flux:button
            type="button"
            variant="outline"
            class="w-full"
            x-on:click="signInWithPasskey"
        >
            <flux:icon.finger-print class="size-5" />
            Sign in with passkey
        </flux:button>

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
    @else
        <flux:subheading class="text-center">
            Please enter the 6-digit code from your authenticator app.
        </flux:subheading>

        <form wire:submit="verifyTwoFactor" class="flex flex-col gap-6">
            <flux:input
                wire:model="twoFactorCode"
                label="Authentication code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                placeholder="000000"
                autocomplete="one-time-code"
                required
            />
            <flux:error name="twoFactorCode" />

            <flux:button type="submit" variant="primary" class="w-full">Verify</flux:button>

            <flux:button type="button" variant="ghost" class="w-full" wire:click="cancelTwoFactor">
                Use a different account
            </flux:button>
        </form>
    @endif
</div>
