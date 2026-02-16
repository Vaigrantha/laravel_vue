<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Author\AuthorController;

    Route::middleware([
        'auth',
        'verified',
        'role:author'
    ])
    ->prefix('author')
    ->name('author.')
    ->group(function () {

        Route::get('/', [AuthorController::class, 'dashboard'])
            ->name('dashboard');

    });