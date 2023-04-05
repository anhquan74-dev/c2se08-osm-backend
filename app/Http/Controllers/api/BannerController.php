<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function createMultipleBanners(Request $request)
    {
        if ($request->has('banners')) {
            foreach ($request->file('banners') as $banner) {
                $bannerName = 'Provider-' . $request->provider_id . '-banner-' . time() . rand(1, 1000) . '.' . $banner->extension();
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
}
