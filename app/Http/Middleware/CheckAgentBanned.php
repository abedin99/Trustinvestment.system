<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAgentBanned
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

            if ($agentUser->banned_at == null) {
                return $next($request);
            } else {
                $notify[] = ['warning', 'Agent permission has been ban.'];
                return redirect()->back()->withNotify($notify);
            }
        }
    }
}