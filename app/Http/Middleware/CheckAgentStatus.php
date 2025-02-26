<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAgentStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            $agentUser = $user->agent;

            if ($agentUser->status) {
                return $next($request);
            } else {
                $notify[] = ['warning', 'Your agent panel has been disabled.'];
                return redirect()->back()->withNotify($notify);
            }
        }
    }
}