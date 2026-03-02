<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Inertia\Inertia;

class AuthorController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Author/Dashboard', [
            'stats' => [
                'books' => Book::query()->count(),
                'myRole' => 'author',
            ],
        ]);
    }

    public function index()
    {
        return Inertia::render('Author/Index', [
            'authors' => User::query()
                ->role('author')
                ->latest()
                ->get(['id', 'name', 'email', 'created_at']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Author/Create');
    }

    public function edit(int $id)
    {
        return Inertia::render('Author/Edit', [
            'author' => User::query()->role('author')->findOrFail($id),
        ]);
    }
}
