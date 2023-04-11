<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class BannerController extends Controller
{
    public function createBanner(Request $request)
    {
        if ($request->has('banners')) {
            foreach ($request->file('banners') as $banner) {
                $bannerName = 'provider-' . $request->provider_id . '-banner-' . time() . rand(1, 1000) . '.' . $banner->extension();
                $banner->move(public_path('uploads/provider-banner'), $bannerName);
                Banner::create([
                    'provider_id' => $request->provider_id,
                    'image' => $bannerName
                ]);
            }
        }
        return response()->json([
            'statusCode' => 201,
            'message' => 'Add banners successfully!',
        ]);
    }
    public function hardDeleteBanner(Request $request)
    {
    }
}