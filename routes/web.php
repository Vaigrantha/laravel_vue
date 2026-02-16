<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Author\AuthorController;
use App\Http\Controllers\User\UserController;
/*
|--------------------------------------------------------------------------
| Public Home (Login / Welcome)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => \Laravel\Fortify\Features::enabled(
            \Laravel\Fortify\Features::registration()
        ),
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('author')) {
        return redirect()->route('author.dashboard');
    }

    if ($user->hasRole('user')) {
        return redirect()->route('user.dashboard');
    }

    abort(403);

})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/admin.php';
require __DIR__.'/author.php';
require __DIR__.'/settings.php';
require __DIR__.'/user.php';
