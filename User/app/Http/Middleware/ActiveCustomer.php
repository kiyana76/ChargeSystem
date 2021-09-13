<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ActiveCustomer
{

    public function __construct()
    {

    }


    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->user()->status != 'active') {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
