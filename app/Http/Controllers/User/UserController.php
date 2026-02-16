<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class UserController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('User/Dashboard');
    }

    public function index()
    {
        return Inertia::render('User/Index');
    }

    public function create()
    {
        return Inertia::render('User/Create');
    }

    public function edit($id)
    {
        return Inertia::render('User/Edit', [
            'id' => $id
        ]);
    }
}
