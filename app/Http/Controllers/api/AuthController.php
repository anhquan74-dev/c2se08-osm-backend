<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    // register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:20',
            'full_name' => 'required|string|min:2|max:255',
            // 'birthday' => 'date_format:Y-m-d H:i:s',
            // 'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $customer = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'is_valid' => true,
        ]);
        $customer->assignRole('customer');
        $customer->save();
        Location::create([
            'user_id' => $customer->id,
            'address' => $request->input('location.address'),
            'province_name' => $request->input('location.province_name'),
            'district_name' => $request->input('location.district_name'),
            'coords_latitude' => $request->input('location.coords_latitude'),
            'coords_longitude' => $request->input('location.coords_longitude'),
            'is_primary' => $request->input('location.is_primary'),
        ]);
        return response()->json([
            'data' => $customer,
            'statusCode' => 201,
            'message' => 'Successful register!',
        ]);
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
        try {
            $payload = auth()->payload();
            auth()->logout();
        } catch (\Exception $e) {
            return $this->responseWithError(1001, 403);
        }
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function refresh()
    {
        try {
            $payload = auth()->payload();
            $user = auth()->user();
            $accessToken = auth()->claims([
                'type' => 'access', 'user' => $user->id,
                'role' => $user->roles[0]->name
            ])->login($user);
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
