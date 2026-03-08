<?php

use App\Actions\Logout;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/logout', Logout::class)->name('logout');
});
