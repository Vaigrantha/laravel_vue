<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Author\AuthorController;
use App\Http\Controllers\User\UserController;

    Route::middleware([
        'auth',
        'verified',
        'role:admin'
    ])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/list', [AdminController::class, 'index'])
            ->middleware('permission:view admin')
            ->name('index');

        Route::get('/create', [AdminController::class, 'create'])
            ->middleware('permission:create admin')
            ->name('create');

        Route::get('/{id}/edit', [AdminController::class, 'edit'])
            ->middleware('permission:edit admin')
            ->name('edit');
    });


    Route::middleware([
        'auth',
        'verified',
        'role:author'
    ])
    ->prefix('author')
    ->name('author.')
    ->group(function () {

        Route::get('/list', [AuthorController::class, 'index'])
            ->middleware('permission:view books')
            ->name('index');

        Route::get('/create', [AuthorController::class, 'create'])
            ->middleware('permission:create books')
            ->name('create');

        Route::get('/{id}/edit', [AuthorController::class, 'edit'])
            ->middleware('permission:edit books')
            ->name('edit');
    });

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

        Route::get('/list', [UserController::class, 'index'])
            ->middleware('permission:view books')
            ->name('index');

        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:create books')
            ->name('create');

        Route::get('/{id}/edit', [UserController::class, 'edit'])
            ->middleware('permission:edit books')
            ->name('edit');
    });