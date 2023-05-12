<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
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
        $user = User::where('email', '=', $request->email)->first();
        if (!$user) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Email or password is incorrect!'
            ]);
        }
        if (!$token = auth()->claims(['type' => 'access', 'user' => $user->id, 'role' => $user->roles[0]->name])->attempt($validator->validated())) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Email or password is incorrect!'
            ]);
        } else {
            $refresh_ttl = config('jwt.refresh_ttl');
            $refreshToken = auth()->setTTL(config('jwt.refresh_ttl'))->claims(['type' => 'refresh', 'user' => $user->id, 'role' => $user->roles[0]->name])->attempt($validator->validated());
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
        try {
            $accessToken = auth()->refresh(true, true);
            // $user = auth()->user();
            // $refresh_ttl = config('jwt.refresh_ttl');
            // $refreshToken = auth()->setTTL(config('jwt.refresh_ttl'))->claims(['type' => 'refresh'])->login($user);
            return $this->responseWithAccessTokenWhenRefresh($accessToken);
        } catch (TokenInvalidException $e) {
            return $this->responseWithError(1002, 403);
        } catch (JWTException $e) {
            return $this->responseWithError(1003, 403);
        } catch (\Exception $e) {
            return $this->responseWithError(1001, 403);
        }
    }
}
