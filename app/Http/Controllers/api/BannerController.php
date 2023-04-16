<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class BannerController extends Controller
{
    public function createBanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|numeric|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "statusCode" => 400,
                "message" => "Validation error",
                "errors" => $validator->errors()
            ]);
        }
        $checkExistProvider = User::find($request->provider_id);
        if (!$checkExistProvider) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding provider!',
            ]);
        }
        if ($request->has('image')) {
            $image = $request->file('image');
            $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/provider-banner/', $fileName);
            $banner = Banner::create([
                'provider_id' => $request->provider_id,
                'image' => $fileName,
            ]);
            return response()->json([
                'data' => $banner,
                'statusCode' => 201,
                'message' => 'Banner created successfully!',
            ]);
        }
        return response()->json([
            "statusCode" => 400,
            "message" => "Missing image for banner",
        ]);
    }
    public function hardDeleteBanner(Request $request)
    {
        if ($request->id) {
            $checkBanner = Banner::find($request->id);
            if ($checkBanner) {
                $destination = 'uploads/provider-banner/' . $checkBanner->image;
                if (File::exists($destination)) {
                    File::delete($destination);
                }
                Banner::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted banner successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the banner you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing banner id parameter!',
        ]);
    }
}
