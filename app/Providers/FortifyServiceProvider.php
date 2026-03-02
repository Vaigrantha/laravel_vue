<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\AppSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::authenticateUsing(function ($request) {
            $selectedRole = (string) $request->input('role', $request->session()->get('auth_role', 'user'));
            if (in_array($selectedRole, array_keys(config('roles')), true)) {
                $request->session()->put('auth_role', $selectedRole);
            }
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                if (in_array($selectedRole, array_keys(config('roles')), true) && ! $user->hasRole($selectedRole)) {
                    return null;
                }

                return $user;
            }

            return null;
        });
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(function (Request $request) {
            $selectedRole = $this->resolveSelectedRole($request);

            return Inertia::render('auth/Login', [
                'canResetPassword' => Features::enabled(Features::resetPasswords()),
                'canRegister' => Features::enabled(Features::registration()),
                'status' => $request->session()->get('status'),
                'roles' => array_keys(config('roles')),
                'selectedRole' => $selectedRole,
                'branding' => [
                    'appName' => AppSetting::getValue('app_name', config('app.name')),
                    'theme' => AppSetting::getValue('theme', 'default'),
                    'loginTitle' => AppSetting::getValue('login_title', 'Log in to your account'),
                    'loginDescription' => AppSetting::getValue('login_description', 'Enter your email and password below to log in'),
                ],
            ]);
        });

        Fortify::resetPasswordView(fn (Request $request) => Inertia::render('auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]));

        Fortify::requestPasswordResetLinkView(fn (Request $request) => Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::verifyEmailView(fn (Request $request) => Inertia::render('auth/VerifyEmail', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::registerView(function (Request $request) {
            $selectedRole = $this->resolveSelectedRole($request);

            return Inertia::render('auth/Register', [
                'roles' => array_keys(config('roles')),
                'selectedRole' => $selectedRole,
                'branding' => [
                    'appName' => AppSetting::getValue('app_name', config('app.name')),
                    'theme' => AppSetting::getValue('theme', 'default'),
                    'registerTitle' => AppSetting::getValue('register_title', 'Create an account'),
                    'registerDescription' => AppSetting::getValue('register_description', 'Enter your details below to create your account'),
                ],
            ]);
        });

        Fortify::twoFactorChallengeView(fn () => Inertia::render('auth/TwoFactorChallenge'));

        Fortify::confirmPasswordView(fn () => Inertia::render('auth/ConfirmPassword'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }

    private function resolveSelectedRole(Request $request): string
    {
        $roles = array_keys(config('roles'));
        $selectedRole = (string) $request->session()->get('auth_role', 'user');

        if (! in_array($selectedRole, $roles, true)) {
            $selectedRole = 'user';
        }

        $request->session()->put('auth_role', $selectedRole);

        return $selectedRole;
    }
}
