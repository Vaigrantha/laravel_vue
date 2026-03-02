<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'admins' => User::role('admin')->count(),
                'authors' => User::role('author')->count(),
                'users' => User::role('user')->count(),
                'books' => Book::query()->count(),
            ],
        ]);
    }

    public function index()
    {
        return Inertia::render('Admin/Index', [
            'admins' => User::query()
                ->role('admin')
                ->latest()
                ->get(['id', 'name', 'email', 'created_at']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Create');
    }

    public function edit(int $id)
    {
        return Inertia::render('Admin/Edit', [
            'admin' => User::query()->role('admin')->findOrFail($id),
        ]);
    }
}
