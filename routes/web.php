<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
| Login Redirect
|--------------------------------------------------------------------------
*/

Route::get('/redirect/by-role', [LoginController::class, 'redirectByRole'])
    ->middleware(['auth'])
    ->name('redirect.by.role');

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
require __DIR__.'/auth.php';
