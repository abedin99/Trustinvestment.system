<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasPermit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $name): Response
    {
        if (isset($name) && $name == true)
        {
            $role = $request->user()->roles()->first();
            $permissions = $role->hasAnyPermission($name);

            if ($permissions) {
                return $next($request);
            }
        }

        return abort(403);
    }
}
