<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

describe('profile page', function () {
    it('displays the profile page with passkey section', function () {
        /** @var User $user */
        $user = User::factory()->create();

        actingAs($user)
            ->get(route('settings.profile'))
            ->assertSuccessful()
            ->assertSee('Passkeys')
            ->assertSee('Add passkey');
    });

    it('lists existing passkeys', function () {
        /** @var User $user */
        $user = User::factory()->create();

        $user->passkeys()->create([
            'name' => 'My MacBook',
            'credential_id' => 'test-credential-id',
            'credential' => ['aaguid' => '00000000-0000-0000-0000-000000000000'],
        ]);

        actingAs($user)
            ->get(route('settings.profile'))
            ->assertSuccessful()
            ->assertSee('My MacBook');
    });
});
