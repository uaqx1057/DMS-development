<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotDriver
{
    public function handle(Request $request, Closure $next, $guard = 'driver')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('driver.login'); // 👈 custom login route
        }

        return $next($request);
    }
}
