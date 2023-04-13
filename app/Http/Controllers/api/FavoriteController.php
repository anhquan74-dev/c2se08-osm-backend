<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    // When customer click like provider create favorite record
    public function createFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $checkExistCustomer = User::find($request->customer_id);
        if (!$checkExistCustomer) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding customer!',
            ]);
        }
        $checkExistProvider = User::find($request->provider_id);
        if (!$checkExistProvider) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding provider!',
            ]);
        }
        $favorite = Favorite::create([
            'provider_id' => $request->provider_id,
            'customer_id' => $request->customer_id,
        ]);
        return response()->json([
            'data' => $favorite,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Get all favorites by customer_id
    public function getFavoritesByCustomerId(Request $request)
    {
        if (!$request->customer_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing customer_id parameter!',
            ]);
        }
        $favorites = Favorite::with('userProvider')->where('customer_id', '=', $request->customer_id)->get();
        return response()->json([
            'data' => $favorites,
            'statusCode' => 200,
            'message' => 'Get all favorites successful!',
        ]);
    }
    // When customer unlike provider
    public function hardDeleteFavorite(Request $request)
    {
        if ($request->id) {
            $checkFavorite = Favorite::where('id', $request->id)->first();
            if ($checkFavorite) {
                Favorite::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted favorite successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the favorite you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing favorite id parameter!',
        ]);
    }
}
