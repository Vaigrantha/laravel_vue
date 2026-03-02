<?php

use App\Http\Controllers\Author\AuthorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:author'])
    ->prefix('author')
    ->name('author.')
    ->group(function () {
        Route::get('/', [AuthorController::class, 'dashboard'])->name('dashboard');
    });
