<?php

namespace App\Http\Middleware;

use App\user;
use Closure;
use Illuminate\Support\Facades\Auth;


class Checkk
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if ( (Auth::user()->role)!=$role) {

            return redirect()->home(); 
        }

        return $next($request);
    }

}