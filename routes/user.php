<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Author\AuthorController;
use App\Http\Controllers\User\UserController;

Route::middleware([
    'auth',
    'verified',
    'role:user'
])
->prefix('user')
->name('user.')
->group(function () {

    Route::get('/', [UserController::class, 'dashboard'])
        ->name('dashboard');

});