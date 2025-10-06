<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $operation
     * @param  string  $name
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $operation, string $name)
    {
        if (CheckPermission($name, $operation)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access');
    }
}
