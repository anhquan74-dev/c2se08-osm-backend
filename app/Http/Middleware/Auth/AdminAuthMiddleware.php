<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use PHPUnit\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAuthMiddleware extends Middleware{
	public function handle( $request, Closure $next, ...$guards ) {
		try {
			$authParse = JWTAuth::parseToken();
			$user      = $authParse->authenticate();
			if(!$user->hasRole('admin')){
				return response()->json($this->responseTemplate(1003), 401);
			}
		} catch (TokenExpiredException $e) {
			//expired token
			return response()->json($this->responseTemplate(1002), 401);
		} catch (TokenInvalidException $e) {
			//invalid token
			return response()->json($this->responseTemplate(1001), 401);
		} catch (JWTException $e) {
			//invalid token
			return response()->json($this->responseTemplate(1001), 401);
		}


		return $next( $request );
	}

	protected function responseTemplate(int $code): array
	{
		return [
			'status' => 'NG',
			'error'  => [
				[
					'code'    => $code,
					'message' => (config('validate.messages')[$code] ?? ''),
				],
			],
		];
	}
}
