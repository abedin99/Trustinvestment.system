<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth = Auth::guard('admin');

        if ($auth->check() && $auth->user()->banned_at && now()->lessThan($auth->user()->banned_at)) {
            $banned_days = now()->diffInDays($auth->user()->banned_at);
            $auth->logout();

            if ($banned_days > 30) {
                $message = 'Your account has been ban. Please contact administrator.';
            } else {
                $banned_days = ($banned_days == 0) ? 1 : $banned_days;
                $message = 'Your account has been ban for ' . round($banned_days) . ' ' . Str::plural('day', $banned_days) . '. Please contact administrator.';
            }

            return redirect()->route('admin.login')->withErrors([$message]);
        }

        return $next($request);
    }
}
