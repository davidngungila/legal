<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        Blade::if('hasPermission', function (string $permission) {
            $user = auth()->user();
            return $user && ($user->hasRole('super_admin') || $user->hasPermission($permission));
        });
        
        Blade::if('hasRole', function (string $role) {
            $user = auth()->user();
            return $user && $user->hasRole($role);
        });
    }
}
