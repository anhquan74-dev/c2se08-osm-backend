<?php


namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use App\Models\Package;

class PackageController extends Controller
{
    // Get all packages
    public function getAllPackages()
    {
        $packages = Package::all();
        return response()->json([
            'data' => $packages,
            'statusCode' => 200,
            'message' => 'Get all packages successful!',
        ]);
    }
    // Get package by Id
    public function getPackageById(Request $request)
    {
        if ($request->id) {
            $packageInfo = Package::find($request->id);
            if ($packageInfo->isEmpty()) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $packageInfo,
                'statusCode' => 200,
                'message' => 'Get package info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing package id parameter!',
        ]);
    }
    // Get all packages by service_id
    public function getAllPackagesByServiceId(Request $request)
    {
        if (!$request->service_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing service_id parameter!',
            ]);
        }
        $packages = Package::where('service_id', '=', $request->service_id)->get();
        return response()->json([
            'data' => $packages,
            'statusCode' => 200,
            'message' => 'Get all packages successful!',
        ]);
    }
    // Create a new package
    public function createNewPackage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|numeric',
            'name' => 'required|string|min:2|max:255',
            'price' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $checkExistService = Service::find($request->service_id);
        if (!$checkExistService) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding service!',
            ]);
        }
        $package = Package::create([
            'service_id' => $request->service_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'total_rate' => 0,
            'total_star' => 0,
            'avg_star' => 0,
            'is_negotiable' => false,
            'view_priority' => 0,
            'is_valid_flag' => false,
        ]);
        return response()->json([
            'data' => $package,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Update package
    public function updatePackage(Request $request)
    {
        if ($request->id) {
            $packageUpdate = Package::find($request->id);
            if ($packageUpdate) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|min:2|max:255',
                    'price' => 'required|numeric',
                    'total_rate' => 'required|numeric',
                    'total_star' => 'required|numeric',
                    'avg_star' => 'required|numeric',
                    'view_priority' => 'required|numeric',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "statusCode" => 400,
                        "message" => "Validation error!",
                        "errors" => $validator->errors()
                    ]);
                }
                Package::where('id', $request->id)->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'total_rate' => $request->total_rate,
                    'total_star' => $request->total_star,
                    'avg_star' => $request->avg_star,
                    'is_negotiable' => $request->is_negotiable,
                    'view_priority' => $request->view_priority,
                    'is_valid_flag' => $request->is_valid_flag,
                ]);
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Package updated successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the package you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing package id parameter!',
        ]);
    }
    // Hard delete service
    public function hardDeletePackage(Request $request)
    {
        if ($request->id) {
            $checkPackage = Package::where('id', $request->id)->first();
            if ($checkPackage) {
                Package::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted package successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the package you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing package id parameter!',
        ]);
    }
}
