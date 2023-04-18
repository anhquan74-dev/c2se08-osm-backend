<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class AuthController extends BaseController
{
	public function __construct()
	{
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
		$ttl = config('jwt.ttl');
		if (!$token = auth()->attempt($validator->validated())) {
			return response()->json([
				'statusCode' => 404,
				'message' => 'Email or password is incorrect!'
			]);
		} else {
			$userProfile = User::where('id', auth()->user()->id)->first();
			$refreshToken = auth()->setTTL(config('jwt.refresh_ttl'))->claims([ 'type' => 'refresh'])->attempt($validator->validated());
			return $this->responseWithToken($token, ($ttl * 60),$refreshToken,$userProfile);
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
		return $this->respondWithToken(auth()->refresh());
	}
}
