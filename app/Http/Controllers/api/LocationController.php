<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class LocationController extends Controller
{
    // Get all locations
    public function getAllLocations()
    {
        $locations = Location::all();
        return response()->json([
            'data' => $locations,
            'statusCode' => 200,
            'message' => 'Get all locations successful!',
        ]);
    }
    // Get location by Id
    public function getLocationById(Request $request)
    {
        if ($request->id) {
            $locationInfo = Location::find($request->id);
            if (!$locationInfo) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $locationInfo,
                'statusCode' => 200,
                'message' => 'Get location info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing location id parameter!',
        ]);
    }
    // Get all locations by user_id
    public function getAllLocationsByUserId(Request $request)
    {
        if (!$request->user_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing user_id parameter!',
            ]);
        }

       

        $locations = Location::where('user_id', '=', $request->user_id)->get();
        if (count($locations) == 0) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Location not found!',
            ]);
        }
        return response()->json([
            'data' => $locations,
            'statusCode' => 200,
            'message' => 'Get all locations successful!',
        ]);
    }
    // Create a new location
    public function createNewLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|integer',
            'address' => 'string|min:2|max:255',
            'province_name' => 'string|min:2|max:255',
            'district_name' => 'string|min:2|max:255',
            // 'coords_latitude' => 'numeric|between:0,99.99',
            // 'coords_longitude' => 'numeric|between:0,99.99',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $checkExistUser = User::find($request->user_id);
        if (!$checkExistUser) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding user!',
            ]);
        }
        $location = Location::create([
            'user_id' => $request->user_id,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'province_name' => $request->province_name,
            'district_id' => $request->district_id,
            'district_name' => $request->district_name,
            'ward_id' => $request->ward_id,
            'ward_name' => $request->ward_name,
            'coords_latitude' => $request->coords_latitude,
            'coords_longitude' => $request->coords_longitude,
            'is_primary' => true,
        ]);
        return response()->json([
            'data' => $location,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Update location
    public function updateLocation(Request $request)
    {
        if ($request->id) {
            $locationUpdate = Location::find($request->id);
            if ($locationUpdate) {
                $validator = Validator::make($request->all(), [
                    'address' => 'string|min:2|max:255',
                    'province_name' => 'string|min:2|max:255',
                    'district_name' => 'string|min:2|max:255',
                    // 'coords_latitude' => 'numeric|between:0,99.99',
                    // 'coords_longitude' => 'numeric|between:0,99.99',
                    'is_primary' => 'integer|between:0,1',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "statusCode" => 400,
                        "message" => "Validation error!",
                        "errors" => $validator->errors()
                    ]);
                }
                Location::where('id', $request->id)->update([
                    'address' => $request->address,
                    'province_id' => $request->province_id,
                    'province_name' => $request->province_name,
                    'district_id' => $request->district_id,
                    'district_name' => $request->district_name,
                    'ward_id' => $request->ward_id,
                    'ward_name' => $request->ward_name,
                    'coords_latitude' => $request->coords_latitude,
                    'coords_longitude' => $request->coords_longitude,
                    'is_primary' => $request->is_primary,
                ]);
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Location updated successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the location you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing location id parameter!',
        ]);
    }
    // Hard delete location
    public function hardDeleteLocation(Request $request)
    {
        if ($request->id) {
            $checkLocation = Location::where('id', $request->id)->first();
            if ($checkLocation) {
                Location::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted location successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the location you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing location id parameter!',
        ]);
    }
}
