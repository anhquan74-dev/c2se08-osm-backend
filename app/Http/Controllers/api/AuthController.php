<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
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
		if (!$token = auth()->attempt($validator->validated())) {
			return response()->json([
				'statusCode' => 404,
				'message' => 'Email or password is incorrect!'
			]);
		} else {
			$userProfile = User::where('id', auth()->user()->id)->first();
			dd($userProfile);
			return $this->respondWithToken($token);
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

	protected function respondWithToken($token)
	{
		return response()->json([
			'access_token' => $token,
			'token_type' => 'bearer',
			'expires_in' => auth()->factory()->getTTL() * 60000
		]);
	}
}
