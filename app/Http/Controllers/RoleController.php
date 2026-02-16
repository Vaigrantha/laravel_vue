<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'permission:manage roles']);
    }

    public function index()
    {
        return Role::with('permissions')->get();
    }

    public function store(Request $request)
    {
        $role = Role::create([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return $role;
    }

    public function update(Request $request, Role $role)
    {
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return $role;
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

