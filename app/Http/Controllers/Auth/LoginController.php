<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function loginHub(): Response
    {
        $this->ensureDefaultAdminExists();

        return Inertia::render('auth/LoginHub', [
            'targets' => [
                [
                    'label' => 'Root',
                    'description' => 'Full administrative access',
                    'path' => '/login/root',
                ],
                [
                    'label' => 'Editors',
                    'description' => 'Author and editorial access',
                    'path' => '/login/editors',
                ],
                [
                    'label' => 'Users',
                    'description' => 'General library user access',
                    'path' => '/login/users',
                ],
                [
                    'label' => 'Careers',
                    'description' => 'Recruitment and onboarding access',
                    'path' => '/login/careers',
                ],
            ],
        ]);
    }

    public function dynamicLoginView(Request $request, string $login): RedirectResponse
    {
        $this->ensureDefaultAdminExists();
        $role = $this->resolveRoleFromLoginPath($login);
        $this->ensureValidRole($role);

        $request->session()->put('auth_role', $role);

        return redirect()->route('login');
    }

    public function dynamicRegisterView(Request $request, string $role): RedirectResponse
    {
        $this->ensureDefaultAdminExists();
        $this->ensureValidRole($role);

        $request->session()->put('auth_role', $role);

        return redirect()->route('register');
    }

    public function redirectByRole(Request $request): RedirectResponse
    {
        $user = $request->user();
        $selectedRole = $request->session()->pull('auth_role');

        if (is_string($selectedRole) && $this->isRoleAllowed($selectedRole) && $user->hasRole($selectedRole)) {
            return redirect()->route($selectedRole.'.dashboard');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('author')) {
            return redirect()->route('author.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    private function ensureValidRole(string $role): void
    {
        abort_unless($this->isRoleAllowed($role), 404);
    }

    private function resolveRoleFromLoginPath(string $login): string
    {
        $login = strtolower(trim($login));
        $normalizedLoginPath = '/'.trim($login, '/');

        if ($this->isRoleAllowed($login)) {
            return $login;
        }

        foreach (array_keys(config('roles')) as $role) {
            $configuredPath = AppSetting::getValue("login_path_{$role}", "/{$role}");
            if (! is_string($configuredPath) || $configuredPath === '') {
                continue;
            }

            if ($normalizedLoginPath === strtolower('/'.trim($configuredPath, '/'))) {
                return $role;
            }
        }

        $aliasMap = [
            'root' => 'admin',
            'editors' => 'author',
            'users' => 'user',
            'careers' => 'user',
        ];

        $settingsAliasJson = AppSetting::getValue('login_role_aliases');
        if (is_string($settingsAliasJson) && $settingsAliasJson !== '') {
            $decoded = json_decode($settingsAliasJson, true);
            if (is_array($decoded)) {
                foreach ($decoded as $alias => $role) {
                    if (is_string($alias) && is_string($role)) {
                        $aliasMap[strtolower(trim($alias))] = strtolower(trim($role));
                    }
                }
            }
        }

        if (array_key_exists($login, $aliasMap) && $this->isRoleAllowed($aliasMap[$login])) {
            return $aliasMap[$login];
        }

        abort(404);
    }

    private function isRoleAllowed(string $role): bool
    {
        return in_array($role, array_keys(config('roles')), true);
    }

    private function ensureDefaultAdminExists(): void
    {
        $email = (string) env('DEFAULT_ADMIN_EMAIL', 'admin@library.test');
        if ($email === '') {
            return;
        }

        $admin = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => (string) env('DEFAULT_ADMIN_NAME', 'System Admin'),
                'password' => Hash::make((string) env('DEFAULT_ADMIN_PASSWORD', Str::random(24))),
            ],
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
