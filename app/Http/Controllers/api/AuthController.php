<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
	public function __construct()
	{
        parent::__construct();
		$this->middleware('auth:api', ['except' => ['login']]);
	}

	// Login
	public function login(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email|max:255',
			'password' => 'required|string|min:6'
		]);
		if ($validator->fails()) {
			return response()->json($validator->errors());
		}
		if (!$token = auth()->attempt($validator->validated())) {
			return response()->json([
				'statusCode' => 404,
				'message' => 'Email or password is incorrect!'
			]);
		} else {
            $refresh_ttl = config('jwt.refresh_ttl');
			$refreshToken = auth()->setTTL(config('jwt.refresh_ttl'))->claims(['type' => 'refresh'])->attempt($validator->validated());
			return $this->responseWithToken($token, $refreshToken, $refresh_ttl);
		}
	}

	public function me()
	{
		return response()->json(auth()->user());
	}

	public function logout()
	{
		auth()->logout();

		return response()->json(['message' => 'Successfully logged out']);
	}


	public function refresh()
	{
       try{
           $accesstoken = auth()->refresh(true, true);
           $user = auth()->user();
           $refresh_ttl = config('jwt.refresh_ttl');
           $refreshToken = auth()->setTTL(config('jwt.refresh_ttl'))->claims(['type' => 'refresh'])->login($user);
           return $this->responseWithToken($accesstoken, $refreshToken, $refresh_ttl);
       }catch (\Exception $exception){
           return $this->responseWithError(1001);
       }
	}
}
