<?php


namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Package;
use App\Models\Service;

class PackageController extends Controller
{
    // Get all packages
    public function getAllPackages()
    {
        $packages = Package::with(['service', 'provider'])->get();
        return response()->json([
            'data' => $packages,
            'statusCode' => 200,
            'message' => 'Get all packages successful!',
        ]);
    }
    // Get package by Id
    // public function getPackageById(Request $request)
    // {
    //     if ($request->id) {
    //         $packageInfo = Package::join('services', 'services.id', '=', 'packages.service_id')
    //             ->join('users', 'users.id', '=', 'services.provider_id')
    //             ->where('packages.id', '=', $request->id)
    //             ->select(
    //                 'packages.*',
    //                 'users.id as provider_id',
    //                 'users.email',
    //                 'users.full_name',
    //                 'users.birthday',
    //                 'users.gender',
    //                 'users.phone_number',
    //                 'users.avatar',
    //                 'users.introduction',
    //                 'users.is_favorite',
    //                 'users.is_working',
    //                 'users.total_rate',
    //                 'users.total_star',
    //                 'users.avg_star',
    //                 'users.clicks',
    //                 'users.views',
    //                 'users.click_rate',
    //                 'users.is_valid as is_valid_provider',
    //             )
    //             ->get();
    //         if (!$packageInfo) {
    //             return response()->json([
    //                 'statusCode' => 404,
    //                 'message' => 'Not found!',
    //             ]);
    //         }
    //         return response()->json([
    //             'data' => $packageInfo,
    //             'statusCode' => 200,
    //             'message' => 'Get package info successfully!',
    //         ]);
    //     }
    //     return response()->json([
    //         'statusCode' => 400,
    //         'message' => 'Missing package id parameter!',
    //     ]);
    // }
    public function getPackageById(Request $request)
    {
        if ($request->id) {
            $packageInfo = Package::find($request->id);
            if (!$packageInfo) {
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
        $packages = Package::join('services', 'services.id', '=', 'packages.service_id')
            ->where('services.id', '=', $request->service_id)
            ->get();
        if (count($packages) == 0) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Package not found!',
            ]);
        }
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
            'service_id' => 'required|numeric|integer',
            'name' => 'required|string|min:2|max:255',
            // 'description' => 'string|max:500',
            'price' => 'required|numeric|integer',
            'is_negotiable' => 'integer|between:0,1',
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
            'is_negotiable' => $request->is_negotiable,
            'view_priority' => 0,
            'is_valid' => true,
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
                    'name' => 'string|min:2|max:255',
                    'description' => 'string|max:500',
                    'price' => 'numeric|integer',
                    'is_negotiable' => 'integer|between:0,1',
                    'total_rate' => 'numeric|integer',
                    'total_star' => 'numeric|integer',
                    'avg_star' => 'numeric',
                    'is_negotiable' => 'integer|between:0,1',
                    'view_priority' => 'numeric',
                    'is_valid' => 'integer|between:0,1',
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
                    'is_valid' => $request->is_valid,
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
    // Hard delete package
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

    // Searching, paginating and sorting packages
    public function searchPaginationPackages(Request $request)
    {
        $sort   = $request->sort;
        $filter = $request->filter;
        $limit  = $request->limit ?? 10;
        $page   = $request->page ?? 1;
        $packages = Package::with(['service', 'provider']);
        if ($filter) {
            $packages = $this->_filterPackage($packages, $filter);
        }
        if ($sort) {
            foreach ($sort as $sortArray) {
                $packages->orderBy($sortArray['sort_by'], $sortArray['sort_dir']);
            }
        }
        return $packages->paginate($limit, ['*'], 'page', $page);
    }

    private function _filterPackage(&$packages, $filter)
    {
        if (isset($filter['category_id'])) {
            $packages->whereHas('service', function ($query) use ($filter) {
                $query->where('category_id', '=', $filter['category_id']);
            });
        }
        if (isset($filter['province_name'])) {
            // $packages->whereHas('provider', function ($query) use ($filter) {
            //     $query->join('locations', 'locations.user_id', 'users.id')->where('locations.province_name', '=', $filter['province_name']);
            // });
            $packages->whereHas('provider', function ($query) use ($filter) {
                $query->join('locations', 'locations.user_id', 'users.id')->where('locations.province_name', 'LIKE', '%' . $filter['province_name'] . '%');
            });
        }
        if (isset($filter['district_name'])) {
            // $packages->whereHas('provider', function ($query) use ($filter) {
            //     $query->join('locations', 'locations.user_id', 'users.id')->where('locations.district_name', '=', $filter['district_name']);
            // });
            $packages->whereHas('provider', function ($query) use ($filter) {
                $query->join('locations', 'locations.user_id', 'users.id')->where('locations.district_name', 'LIKE', '%' . $filter['district_name'] . '%');
            });
        }
        if (isset($filter['avg_star'])) {
            $packages->where('avg_star', '=', $filter['avg_star']);
        }
        // if (isset($filter['price_min'])) {
        //     $packages->where('price_min', '=', $filter['price_min']);
        // }
        // if (isset($filter['price_max'])) {
        //     $packages->where('price_max', '=', $filter['price_max']);
        // }
        if (isset($filter['price_min']) && isset($filter['price_max'])) {
            $packages->whereBetween('price', [$filter['price_min'], $filter['price_max']]);
        } else if (isset($filter['price_min'])) {
            $packages->where('price', ">=", $filter['price_min']);
        } else if (isset($filter['price_max'])) {
            $packages->where('price', "<=", $filter['price_max']);
        }
        if (isset($filter['name'])) {
            $packages->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }
        if (isset($filter['is_valid'])) {
            $packages->where('is_valid', $filter['is_valid']);
        }
        return $packages;
    }
    // getAllPackagesByServiceIdCategoryId
    public function getAllPackagesByServiceIdCategoryId(Request $request)
    {
        $serviceFind = Service::where('category_id', '=', $request->category_id)
            ->where('category_id', '=', $request->category_id)->get();
        $service = Service::with('package')->where('id', '=', $serviceFind[0]->id)->get();
        return response()->json([
            'statusCode' => 200,
            'data' => $service,
            'message' => 'Successfully!',
        ]);
    }
}
