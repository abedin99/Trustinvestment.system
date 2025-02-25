<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminDisabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth = Auth::guard('admin');

        if ($auth->check() && $auth->user()->disabled_at && now()->greaterThan($auth->user()->disabled_at)) {
            $auth->logout();

            $message = 'Your account has been disabled. Please contact administrator.';

            return redirect()->route('admin.login')->withErrors([$message]);
        }

        return $next($request);
    }
}
