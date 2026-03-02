<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('User/Dashboard', [
            'stats' => [
                'availableBooks' => Book::query()->count(),
            ],
        ]);
    }

    public function index()
    {
        return Inertia::render('User/Index', [
            'users' => User::query()
                ->role('user')
                ->latest()
                ->get(['id', 'name', 'email', 'created_at']),
        ]);
    }

    public function create()
    {
        return Inertia::render('User/Create');
    }

    public function edit(int $id)
    {
        return Inertia::render('User/Edit', [
            'userRecord' => User::query()->role('user')->findOrFail($id),
        ]);
    }
}
