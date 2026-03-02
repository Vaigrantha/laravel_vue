<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class PermissionManagementController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.roles.index');
    }
}
