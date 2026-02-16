<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard');
    }

    public function index()
    {
        return Inertia::render('Admin/Index');
    }

    public function create()
    {
        return Inertia::render('Admin/Create');
    }

    public function edit($id)
    {
        return Inertia::render('Admin/Edit', [
            'id' => $id
        ]);
    }
}
