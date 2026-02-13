<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

uses()->group('auth');

describe('register page', function () {
    it('displays the register page', function () {
        get(route('register'))
            ->assertSuccessful()
            ->assertSee('Create your account')
            ->assertSee('Name')
            ->assertSee('Email')
            ->assertSee('Password')
            ->assertSee('Confirm Password');
    });

    it('redirects authenticated users to dashboard', function () {
        /** @var User $user */
        $user = User::factory()->create();

        actingAs($user)
            ->get(route('register'))
            ->assertRedirect(route('dashboard'));
    });
});

describe('registration functionality', function () {
    it('registers new user with valid data', function () {
        Livewire::test('pages::register')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        assertAuthenticated();
    });

    it('fails with missing name', function () {
        Livewire::test('pages::register')
            ->set('name', '')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors('name');

        assertGuest();
    });

    it('fails with missing email', function () {
        Livewire::test('pages::register')
            ->set('name', 'John Doe')
            ->set('email', '')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors('email');

        assertGuest();
    });

    it('fails with invalid email format', function () {
        Livewire::test('pages::register')
            ->set('name', 'John Doe')
            ->set('email', 'invalid-email')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors('email');

        assertGuest();
    });

    it('fails with duplicate email', function () {
        User::factory()->create(['email' => 'john@example.com']);

        Livewire::test('pages::register')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors('email');

        assertGuest();
    });

    it('fails with missing password', function () {
        Livewire::test('pages::register')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('register')
            ->assertHasErrors('password');

        assertGuest();
    });

    it('fails with short password', function () {
        Livewire::test('pages::register')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('register')
            ->assertHasErrors('password');

        assertGuest();
    });

    it('fails with mismatched password confirmation', function () {
        Livewire::test('pages::register')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'differentpassword')
            ->call('register')
            ->assertHasErrors('password');

        assertGuest();
    });

    it('fails with name exceeding max length', function () {
        Livewire::test('pages::register')
            ->set('name', str_repeat('a', 256))
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors('name');

        assertGuest();
    });
});
