<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register the middleware alias
        $middleware->alias([
            'HasPermit' => App\Http\Middleware\HasPermit::class,
            'Permission' => App\Http\Helpers\Permission::class,
            'user.banned' => App\Http\Middleware\CheckUserBanned::class,
            'user.disabled' => App\Http\Middleware\CheckUserDisabled::class,
            'user.last.activity' => \App\Http\Middleware\LastUserActivity::class,
            'admin.banned' => App\Http\Middleware\CheckAdminBanned::class,
            'admin.disabled' => App\Http\Middleware\CheckAdminDisabled::class,
            'admin.last.activity' => \App\Http\Middleware\LastAdminActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
