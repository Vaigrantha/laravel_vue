<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('DEFAULT_ADMIN_EMAIL', 'admin@library.test');
        $password = env('DEFAULT_ADMIN_PASSWORD', 'password');
        $name = env('DEFAULT_ADMIN_NAME', 'System Admin');

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ],
        );

        $admin->syncRoles(['admin']);
    }
}
