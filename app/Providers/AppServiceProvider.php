<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

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
        // Specify the callback that should be used to generate the redirect path.
        Authenticate::redirectUsing(function (Request $request) {
            if (!$request->expectsJson() && $request->routeIs('admin.*')) {
                return route('admin.login');
            }

            if (!$request->expectsJson() && $request->user('web')) {
                return route('login');
            }
        });

        // The callback that should be used to generate the authentication redirect path.
        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            if ($request->user('admin') && $request->routeIs('admin.*')) {
                return route('admin.dashboard');
            }

            if ($request->user('web')) {
                return route('dashboard');
            }
        });
    }
}
