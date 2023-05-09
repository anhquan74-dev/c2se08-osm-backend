<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards ) {
        if ( $this->_authenticate( $request, $guards ) === 'authentication_failed' ) {
            return  response()->json([
                'status' => 'NG',
                'error' => [
                    [
                        'code' => 1003,
                        'message' => 'Unauthorized'
                    ]
                ],
            ], 401);;
        }

        return $next( $request );
    }

    protected function _authenticate( $request, array $guards ): ?string
    {
        if ( empty( $guards ) ) {
            $guards = [ null ];
        }
        foreach ( $guards as $guard ) {
            if ( Auth::guard( $guard )->check() ) {
                return $this->auth->shouldUse( $guard );
            }
        }

       return  'authentication_failed';
    }
}
