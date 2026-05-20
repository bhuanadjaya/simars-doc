<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(function () {
            $user = Auth::user();

            if (! $user) {
                return '/login';
            }

            $role = $user->role->name ?? '';

            return in_array($role, ['super_admin', 'admin_unit', 'auditor'])
                ? '/admin'
                : '/portal/documents';
        });
    }
}
