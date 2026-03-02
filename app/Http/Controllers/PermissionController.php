<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    public function index()
    {
        return Permission::query()->get();
    }

    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Permission creation is disabled. Assign existing permissions to roles only.',
        ], 405);
    }

    public function update(Request $request, Permission $permission)
    {
        return response()->json([
            'message' => 'Permission editing is disabled. Assign existing permissions to roles only.',
        ], 405);
    }

    public function destroy(Permission $permission)
    {
        return response()->json([
            'message' => 'Permission deletion is disabled. Assign existing permissions to roles only.',
        ], 405);
    }
}
