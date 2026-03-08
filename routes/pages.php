<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'pages::login')->name('login');
    Route::livewire('/register', 'pages::register')->name('register');
});

Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/nodes', 'pages::nodes')->name('nodes');
    Route::livewire('/recipes', 'pages::recipes')->name('recipes');
    Route::livewire('/analytics', 'pages::analytics')->name('analytics');
    Route::livewire('/node-gtp', 'pages::node-gtp')->name('node-gtp');
    Route::livewire('/payouts', 'pages::payouts')->name('payouts');
    Route::livewire('/settings/profile', 'pages::settings.profile')->name('settings.profile');
    Route::livewire('/settings/users', 'pages::settings.users')->name('settings.users');
    Route::livewire('/settings/ssh-key', 'pages::settings.ssh-key')->name('settings.ssh-key');
    Route::livewire('/settings/notification', 'pages::settings.notification')->name('settings.notification');
    Route::livewire('/settings/ai', 'pages::settings.ai')->name('settings.ai');
    Route::livewire('/settings/integrations', 'pages::settings.integrations')->name('settings.integrations');
});
