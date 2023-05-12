<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;

class OrMiddleware
{

    public function handle(Request $request, Closure $next, ...$middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (app($middleware)->handle($request, function () {})) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}
