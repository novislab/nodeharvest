<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses()->group('auth');

describe('login page', function () {
    it('displays the login page', function () {
        get(route('login'))
            ->assertSuccessful()
            ->assertSee('Welcome back')
            ->assertSee('Email')
            ->assertSee('Password');
    });

    it('redirects authenticated users to dashboard', function () {
        /** @var User $user */
        $user = User::factory()->create();

        actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    });
});

describe('login functionality', function () {
    it('logs in user with valid credentials', function () {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test('pages::login')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        assertAuthenticatedAs($user);
    });

    it('fails with invalid credentials', function () {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test('pages::login')
            ->set('email', 'test@example.com')
            ->set('password', 'wrongpassword')
            ->call('login');

        assertGuest();
    });

    it('fails with missing email', function () {
        Livewire::test('pages::login')
            ->set('email', '')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');
    });

    it('fails with missing password', function () {
        Livewire::test('pages::login')
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors('password');
    });

    it('fails with invalid email format', function () {
        Livewire::test('pages::login')
            ->set('email', 'invalid-email')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');
    });
});

describe('remember me functionality', function () {
    it('sets remember me cookie when checked', function () {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test('pages::login')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('remember', true)
            ->call('login')
            ->assertRedirect(route('dashboard'));

        assertAuthenticatedAs($user);
    });
});

describe('logout functionality', function () {
    it('logs out authenticated user', function () {
        /** @var User $user */
        $user = User::factory()->create();

        actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        assertGuest();
    });

    it('prevents guest from accessing logout', function () {
        post(route('logout'))
            ->assertRedirect(route('login'));
    });
});
