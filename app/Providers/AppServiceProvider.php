<?php

namespace App\Providers;

use App\Auth\ClientAwareUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Auth::provider('client_aware_eloquent', function ($app, array $config) {
            return new ClientAwareUserProvider($app['hash'], $config['model']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            // Force HTTPS in production or if X-Forwarded-Proto is https
            if ($this->app->environment('production') || request()->header('X-Forwarded-Proto') === 'https') {
                URL::forceScheme('https');
            }
        }

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
