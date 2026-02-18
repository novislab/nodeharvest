<?php

use App\Actions\Logout;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::livewire('/login', 'pages::login')->name('login');
    Route::livewire('/register', 'pages::register')->name('register');
});

Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/nodes', 'pages::nodes')->name('nodes');
    Route::livewire('/settings/profile', 'pages::settings.profile')->name('settings.profile');
    Route::post('/logout', Logout::class)->name('logout');
});
