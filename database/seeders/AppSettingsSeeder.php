<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'Library Management',
                'type' => 'string',
                'description' => 'Application display name',
            ],
            [
                'key' => 'theme',
                'value' => 'default',
                'type' => 'string',
                'description' => 'Default application theme',
            ],
            [
                'key' => 'login_path_admin',
                'value' => '/admin',
                'type' => 'string',
                'description' => 'Dynamic login path for admin role',
            ],
            [
                'key' => 'login_path_author',
                'value' => '/author',
                'type' => 'string',
                'description' => 'Dynamic login path for author role',
            ],
            [
                'key' => 'login_path_user',
                'value' => '/user',
                'type' => 'string',
                'description' => 'Dynamic login path for user role',
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}
