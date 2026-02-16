<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Config;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Config::get('roles');

        foreach ($roles as $roleName => $data) {

            $role = Role::firstOrCreate(['name' => $roleName]);

            foreach ($data['permissions'] as $permissionName) {

                $permission = Permission::firstOrCreate([
                    'name' => $permissionName
                ]);

                $role->givePermissionTo($permission);
            }
        }
    }
}
