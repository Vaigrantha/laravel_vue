<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\PermissionManagementController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Author\AuthorController;
use App\Http\Controllers\Library\BookController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::prefix('admins')->name('admins.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/create', [AdminController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('edit');
        });

        Route::prefix('authors')->name('authors.')->group(function () {
            Route::get('/', [AuthorController::class, 'index'])->name('index');
            Route::get('/create', [AuthorController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [AuthorController::class, 'edit'])->name('edit');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        });

        Route::prefix('books')->name('books.')->group(function () {
            Route::get('/', [BookController::class, 'index'])->name('index');
            Route::get('/create', [BookController::class, 'create'])->name('create');
            Route::post('/metadata-preview', [BookController::class, 'previewMetadata'])->name('metadata-preview');
            Route::post('/', [BookController::class, 'store'])->name('store');
            Route::get('/{book}/edit', [BookController::class, 'edit'])->name('edit');
            Route::put('/{book}', [BookController::class, 'update'])->name('update');
            Route::delete('/{book}', [BookController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleManagementController::class, 'index'])->name('index');
            Route::get('/create', [RoleManagementController::class, 'create'])->name('create');
            Route::get('/{role}/edit', [RoleManagementController::class, 'edit'])->name('edit');
        });

        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [PermissionManagementController::class, 'index'])->name('index');
        });

        Route::prefix('app-settings')->name('app-settings.')->group(function () {
            Route::get('/', [AppSettingController::class, 'index'])->name('index');
            Route::get('/create', [AppSettingController::class, 'create'])->name('create');
            Route::post('/', [AppSettingController::class, 'store'])->name('store');
            Route::get('/{appSetting}/edit', [AppSettingController::class, 'edit'])->name('edit');
            Route::put('/{appSetting}', [AppSettingController::class, 'update'])->name('update');
            Route::delete('/{appSetting}', [AppSettingController::class, 'destroy'])->name('destroy');
        });
    });
