<?php

namespace App\Http\Middleware;

use App\user;
use Closure;
use Illuminate\Support\Facades\Auth;


class CheckAdminOrg
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $role2)
    {
        if ( (Auth::user()->role)!=$role && (Auth::user()->role)!=$role2) {
            return redirect()->home(); 
        }

        return $next($request);
    }

}