<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login/cms', [LoginController::class, 'loginHub'])
        ->name('login.cms');

    Route::get('/login/{login}', [LoginController::class, 'dynamicLoginView'])
        ->name('login.role');

    Route::get('/register/{role}', [LoginController::class, 'dynamicRegisterView'])
        ->name('register.role');
});
