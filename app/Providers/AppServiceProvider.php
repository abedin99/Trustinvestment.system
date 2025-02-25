<?php

namespace App\Providers;

use Illuminate\Http\Request;
use App\Http\Helpers\Permission;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
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
        // dynamically set the root view based on whether the route is backend or frontend
        // can also be done in a middleware that wraps all admin routes

        if (request()->is('admin/*') or request()->is('admin')) {
            View::getFinder()->setPaths([
                resource_path('admin')
            ]);
        }


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        Blade::if('permit', function ($name) {
            return Permission::permit($name);
        });

        Blade::if('access', function ($name) {
            return Permission::access($name);
        });

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
