<?
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'permission:manage permissions']);
    }

    public function index()
    {
        return Permission::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        return Permission::create($validated);
    }

    public function update(Request $request, Permission $permission)
    {
        $permission->update($request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]));

        return $permission;
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
