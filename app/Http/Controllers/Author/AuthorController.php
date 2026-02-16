<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AuthorController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Author/Dashboard');
    }

    public function index()
    {
        return Inertia::render('Author/Index');
    }

    public function create()
    {
        return Inertia::render('Author/Create');
    }

    public function edit($id)
    {
        return Inertia::render('Author/Edit', [
            'id' => $id
        ]);
    }
}
