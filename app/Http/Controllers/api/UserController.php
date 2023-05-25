<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Location;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Validator;

class UserController extends Controller
{
    // Get count customers
    public function getTotalCustomer()
    {
        $customersCount = User::with('roles')->whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })->count();
        return response()->json([
            'data' => $customersCount,
            'statusCode' => 200,
            'message' => 'Count all customers successfully!',
        ]);
    }
    // Get all customer
    public function getAllCustomers()
    {
        $customers = User::with(['roles'])->whereHas('roles', function ($query) {
            return $query->where('name', '=', 'customer');
        })->get()->map(function ($customers) {
            unset($customers->introduction);
            unset($customers->is_favorite);
            unset($customers->is_working);
            unset($customers->total_rate);
            unset($customers->total_star);
            unset($customers->avg_star);
            unset($customers->clicks);
            unset($customers->views);
            unset($customers->click_rate);
            return $customers;
        });
        return response()->json([
            'data' => $customers,
            'statusCode' => 200,
            'message' => 'Get all customers successful!',
        ]);
    }
    // Get customer by Id
    public function getCustomerById(Request $request)
    {
        if ($request->id) {
            $customerInfo = User::with(['location', 'avatar', 'roles'])->whereHas('roles', function ($query) {
                return $query->where('name', '=', 'customer');
            })->where('id', $request->id)->get()->map(function ($customerInfo) {
                unset($customerInfo->email_verified_at);
                unset($customerInfo->remember_token);
                unset($customerInfo->is_favorite);
                unset($customerInfo->is_working);
                unset($customerInfo->total_rate);
                unset($customerInfo->total_star);
                unset($customerInfo->avg_star);
                unset($customerInfo->clicks);
                unset($customerInfo->views);
                unset($customerInfo->click_rate);
                unset($customerInfo->introduction);
                unset($customerInfo->password);
                return $customerInfo;
            });
            if ($customerInfo->isEmpty()) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $customerInfo[0],
                'statusCode' => 200,
                'message' => 'Get customer info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing customer id parameter!',
        ]);
    }

    // Get admin by Id
    public function getAdminById(Request $request)
    {
        if ($request->id) {
            $adminInfo = User::with('roles')->whereHas('roles', function ($query) {
                return $query->where('name', '=', 'admin');
            })->where('id', $request->id)->get()->map(function ($adminInfo) {
                unset($adminInfo->email_verified_at);
                unset($adminInfo->remember_token);
                unset($adminInfo->is_favorite);
                unset($adminInfo->is_working);
                unset($adminInfo->total_rate);
                unset($adminInfo->total_star);
                unset($adminInfo->avg_star);
                unset($adminInfo->clicks);
                unset($adminInfo->views);
                unset($adminInfo->click_rate);
                unset($adminInfo->introduction);
                unset($adminInfo->password);
                return $adminInfo;
            });
            if ($adminInfo->isEmpty()) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $adminInfo[0],
                'statusCode' => 200,
                'message' => 'Get admin info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing admin id parameter!',
        ]);
    }

    // Create a new customer
    public function createNewCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string|min:2|max:255',
            'birthday' => 'date_format:Y-m-d H:i:s',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
        $service = new ImageService();
        $image = $request->file('avatar');
        if ($image) {
            try {
                $service->uploadImage($image, $customer->id, 'avatar');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        $customer->assignRole('customer');
        $customer->save();
        if ($request->has('location')) {
            // $locationArr = $request->location
            Location::create([
                'user_id' => $customer->id,
                'address' => $request->input('location.address'),
                'province_name' => $request->input('location.province_name'),
                'district_name' => $request->input('location.district_name'),
                'coords_latitude' => $request->input('location.coords_latitude'),
                'coords_longitude' => $request->input('location.coords_longitude'),
                'is_primary' => $request->input('location.is_primary'),
            ]);
        }
        return response()->json([
            'data' => $customer,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Update customer
    public function updateCustomer(Request $request)
    {
        if ($request->id) {
            $customerUpdate = User::find($request->id);
            if ($customerUpdate) {
                if ($request->file('avatar') == null) {
                    $validatorUpdate = Validator::make($request->all(), [
                        'full_name' => 'string|min:2|max:255',
                        'birthday' => 'date_format:Y-m-d H:i:s',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $customerUpdate->full_name = $request->full_name;
                    $customerUpdate->birthday = $request->birthday;
                    $customerUpdate->gender = $request->gender;
                    $customerUpdate->phone_number = $request->phone_number;
                    $customerUpdate->is_valid = $request->is_valid;
                    $customerUpdate->save();
                }
                if ($request->hasFile('avatar')) {
                    Image::where('parent_type', 'avatar')->where('parent_id', $request->id)->delete();
                    $validatorUpdate = Validator::make($request->all(), [
                        'full_name' => 'string|min:2|max:255',
                        'birthday' => 'date_format:Y-m-d H:i:s',
                        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $image = $request->file('avatar');
                    if ($image) {
                        $service = new ImageService();
                        $avatar = $customerUpdate->avatar;
                        if ($avatar) {
                            $service->deleteImage($avatar->id);
                            $avatar->delete();
                        }
                        $service->uploadImage($image, $customerUpdate->id, 'avatar');
                    }
                    $customerUpdate->full_name = $request->full_name;
                    $customerUpdate->birthday = $request->birthday;
                    $customerUpdate->gender = $request->gender;
                    $customerUpdate->phone_number = $request->phone_number;
                    $customerUpdate->is_valid = $request->is_valid;
                    $customerUpdate->save();

                    $service = new ImageService();
                    try {
                        $service->uploadImage($image, $customerUpdate->id, 'avatar');
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
                $oldLocation = count($customerUpdate->location) ? $customerUpdate->location[0] : null;
                if ($request->has('location')) {
                    $oldLocation->address = $request->input('location.address');
                    $oldLocation->province_name = $request->input('location.province_name');
                    $oldLocation->district_name = $request->input('location.district_name');
                    $oldLocation->coords_latitude = $request->input('location.coords_latitude');
                    $oldLocation->coords_longitude = $request->input('location.coords_longitude');
                    $oldLocation->is_primary = $request->input('location.is_primary');
                    $oldLocation->save();
                }
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Customer updated successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the customer you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing customer id parameter!',
        ]);
    }
    // Hard delete customer
    public function hardDeleteCustomer(Request $request)
    {
        if ($request->id) {
            $checkCustomer = User::find($request->id);
            if ($checkCustomer) {
                $image = $checkCustomer->avatar;
                if ($image) {
                    (new ImageService())->deleteImage($image->id);
                    $image->delete();
                }
                User::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted customer successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the customer you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing customer id parameter!',
        ]);
    }
    // Searching, paginating and sorting customers
    public function searchPaginationCustomers(Request $request)
    {
        $sort   = $request->sort;
        $filter = $request->filter;
        $limit  = $request->limit ?? 10;
        $page   = $request->page ?? 1;
        $user   = User::with(['location', 'avatar', 'roles'])->whereHas('roles', function ($query) {
            return $query->where('name', '=', 'customer');
        });
        if ($filter) {
            $user = $this->_filterCustomer($user, $filter);
        }
        if ($sort) {
            foreach ($sort as $sortArray) {
                $user->orderBy($sortArray['sort_by'], $sortArray['sort_dir']);
            }
        }
        return $user->paginate($limit, ['*'], 'page', $page);
    }

    public function _filterCustomer(&$users, $filter)
    {
        if (isset($filter['full_name'])) {
            $users->where('full_name', 'LIKE', '%' . $filter['full_name'] . '%');
        }
        if (isset($filter['province_name'])) {
            $users->whereHas('location', function ($query) use ($filter) {
                $query->where('province_name', 'LIKE', '%' . $filter['province_name'] . '%');
            });
        }
        if (isset($filter['is_valid'])) {
            $users->where('is_valid', $filter['is_valid']);
        }
        return $users;
    }
    // Get count providers
    public function getTotalProvider()
    {
        $providersCount = User::with(['roles', 'avatar'])->whereHas('roles', function ($query) {
            return $query->where('name', '=', 'provider');
        })->count();
        return response()->json([
            'data' => $providersCount,
            'statusCode' => 200,
            'message' => 'Count all providers successfully!',
        ]);
    }
    // Get all provider
    public function getAllProviders()
    {
        $providers = User::with('roles')->whereHas('roles', function ($query) {
            return $query->where('name', '=', 'provider');
        })->get();
        return response()->json([
            'data' => $providers,
            'statusCode' => 200,
            'message' => 'Get all providers successful!',
        ]);
    }
    // Get provider by Id
    public function getProviderById(Request $request)
    {
        if ($request->id) {
            $providerWithServiceBannerLocation = User::with(['location', 'roles', 'avatar', 'banner', 'service'])->whereHas('roles', function ($query) {
                return $query->where('name', '=', 'provider');
            })->where('id', $request->id)->get();
            if ($providerWithServiceBannerLocation->isEmpty()) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $providerWithServiceBannerLocation[0],
                'statusCode' => 200,
                'message' => 'Get provider info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing provider id parameter!',
        ]);
    }
    // Create a new provider
    public function createNewProvider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string|min:2|max:255',
            'birthday' => 'date_format:Y-m-d H:i:s',
            // 'introduction' => 'string|max:500',
            'address' => 'string|min:2|max:255',
            'province_name' => 'string|min:2|max:255',
            'district_name' => 'string|min:2|max:255',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $provider = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'is_favorite' => false,
            'is_working' => false,
            'total_rate' => 0,
            'total_star' => 0,
            'avg_star' => 0,
            'clicks' => 0,
            'views' => 0,
            'click_rate' => 0,
            'is_valid' => true,
        ]);
        $service = new ImageService();
        $image = $request->file('avatar');
        if ($image) {
            try {
                $service->uploadImage($image, $provider->id, 'avatar');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
        if ($request->file('banner')) {
            $imageBanners = $request->file('banner');
            if (count($imageBanners)) {
                foreach ($imageBanners as $imageBanner)
                    try {
                        $service->uploadImage($imageBanner, $provider->id, 'provider');
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
            }
        }

        $provider->assignRole('provider');
        Location::create([
            'user_id' => $provider->id,
            'address' => $request->input('location.address'),
            'province_name' => $request->input('location.province_name'),
            'district_name' => $request->input('location.district_name'),
            'coords_latitude' => $request->input('location.coords_latitude'),
            'coords_longitude' => $request->input('location.coords_longitude'),
            'is_primary' => $request->input('location.is_primary'),
        ]);
        $dataReturn = User::with(['roles', 'location'])->where('id', '=', $provider->id)->get();
        return response()->json([
            'data' => $dataReturn,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Update provider
    public function updateProvider(Request $request)
    {
        if ($request->id) {
            $providerUpdate = User::find($request->id);
            if ($providerUpdate) {
                $validatorUpdate = Validator::make($request->all(), [
                    'full_name' => 'string|min:2|max:255',
                    'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
                    'birthday' => 'date_format:Y-m-d H:i:s',
                    'introduction' => 'string|max:500',
                    'is_favorite' => 'integer|between:0,1',
                    'is_working' => 'integer|between:0,1',
                    'total_rate' => 'numeric|integer',
                    'total_star' => 'numeric|integer',
                    'avg_star' => 'numeric',
                    'clicks' => 'numeric|integer',
                    'views' => 'numeric|integer',
                    'click_rate' => 'numeric',
                    'is_valid' => 'integer|between:0,1'
                ]);
                if ($validatorUpdate->fails()) {
                    return response()->json([
                        "statusCode" => 400,
                        "message" => "Validation update error",
                        "errors" => $validatorUpdate->errors()
                    ]);
                }
                if ($request->hasFile('avatar')) {
                    Image::where('parent_type', 'avatar')->where('parent_id', $request->id)->delete();
                    $image = $request->file('avatar');
                    $image = (new ImageService())->uploadImage($image, $providerUpdate->id, 'avatar');
                }

                if ($request->hasFile('banner')) {
                    $imageBanners = $request->file('banner');
                    if (count($imageBanners)) {
                        $imageOld = $providerUpdate->banner;
                        foreach ($imageBanners as $imageBanner)
                            try {
                                (new ImageService())->uploadImage($imageBanner, $providerUpdate->id, 'provider');
                            } catch (\Exception $e) {
                                Log::error($e->getMessage());
                            }
                    }
                }
                $providerUpdate->full_name = $request->full_name;
                $providerUpdate->password = Hash::make($request->password);
                $providerUpdate->birthday = $request->birthday;
                $providerUpdate->gender = $request->gender;
                $providerUpdate->phone_number = $request->phone_number;
                $providerUpdate->introduction = $request->introduction;
                $providerUpdate->is_favorite = $request->is_favorite;
                $providerUpdate->is_working = $request->is_working;
                $providerUpdate->total_rate = $request->total_rate;
                $providerUpdate->total_star = $request->total_star;
                $providerUpdate->avg_star = $request->avg_star;
                $providerUpdate->clicks = $request->clicks;
                $providerUpdate->views = $request->views;
                $providerUpdate->click_rate = $request->click_rate;
                $providerUpdate->is_valid = $request->is_valid;
                $providerUpdate->save();
                $oldLocation = count($providerUpdate->location) ? $providerUpdate->location[0] : null;
                if ($request->has('location')) {
                    $oldLocation->address = $request->input('location.address');
                    $oldLocation->province_name = $request->input('location.province_name');
                    $oldLocation->district_name = $request->input('location.district_name');
                    $oldLocation->coords_latitude = $request->input('location.coords_latitude');
                    $oldLocation->coords_longitude = $request->input('location.coords_longitude');
                    $oldLocation->is_primary = $request->input('location.is_primary');
                    $oldLocation->save();
                }

                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Provider updated successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the provider you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing provider id parameter!',
        ]);
    }
    // updateWorkingStatus
    public function updateWorkingStatus(Request $request)
    {
        if ($request->id) {
            $providerUpdate = User::find($request->id);
            if ($providerUpdate) {
                if ($providerUpdate->is_working == 1) {
                    $providerUpdate->is_working = 0;
                } else {
                    $providerUpdate->is_working = 1;
                }
                $providerUpdate->save();
                return response()->json([
                    'data' => $providerUpdate->is_working,
                    'statusCode' => 200,
                    'message' => 'Provider updated successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the provider you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing provider id parameter!',
        ]);
    }
    // Hard delete provider
    public function hardDeleteProvider(Request $request)
    {
        if ($request->id) {
            $checkProvider =
                User::find($request->id);
            if ($checkProvider) {
                $avatar = $checkProvider->avatar;
                if ($avatar) {
                    (new ImageService())->deleteImage($avatar->id);
                    $avatar->delete();
                }
                User::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted provider successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the provider you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing provider id parameter!',
        ]);
    }
    // Searching, paginating and sorting providers
    public function searchPaginationProviders(Request $request)
    {
        $sort   = $request->sort;
        $filter = $request->filter;
        $limit  = $request->limit ?? 10;
        $page   = $request->page ?? 1;
        $providers = User::with(['avatar', 'location', 'service'])->whereHas('roles', function ($query) {
            return $query->where('name', '=', 'provider');
        });
        if ($filter) {
            $providers = $this->_filterProvider($providers, $filter);
        }
        if ($sort) {
            foreach ($sort as $sortArray) {
                $providers->orderBy($sortArray['sort_by'], $sortArray['sort_dir']);
            }
        }
        return $providers->paginate($limit, ['*'], 'page', $page);
    }

    private function _filterProvider(&$providers, $filter)
    {
        if (isset($filter['category_id'])) {
            $providers->whereHas('service', function ($query) use ($filter) {
                $query->where('category_id', '=', $filter['category_id']);
            });
        }
        if (isset($filter['province_name'])) {
            $providers->whereHas('location', function ($query) use ($filter) {
                // $query->where('province_name', '=', $filter['province_name']);
                $query->where('province_name', 'LIKE', '%' . $filter['province_name'] . '%');
            });
        }
        if (isset($filter['district_name'])) {
            $providers->whereHas('location', function ($query) use ($filter) {
                $query->where('district_name', 'LIKE', '%' . $filter['district_name'] . '%');
            });
        }
        if (isset($filter['avg_star'])) {
            $providers->whereRaw('ROUND(avg_star) = ?', $filter['avg_star']);
        }
        if (isset($filter['price_min'])) {
            $providers->whereHas('service', function ($query) use ($filter) {
                $query->where('price_min', '<=', $filter['price_min']);
            });
        }
        if (isset($filter['price_max'])) {
            $providers->whereHas('service', function ($query) use ($filter) {
                $query->where('price_max', '>=', $filter['price_min']);
            });
        }
        if (isset($filter['full_name'])) {
            $providers->where('full_name', 'LIKE', '%' . $filter['full_name'] . '%');
        }

        if (isset($filter['is_valid'])) {
            $providers->where('is_valid', $filter['is_valid']);
        }
        return $providers;
    }
}
