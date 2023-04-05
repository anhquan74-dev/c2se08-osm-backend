<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\RoleDetailUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Validator;

class UserController extends Controller
{
    // Get all customer
    public function getAllCustomers()
    {
        $customers = User::with('roleDetails')->whereHas('roleDetails', function ($query) {
            return $query->where('role_details_id', '=', 3);
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
            $customerInfo = DB::table('users')
                ->join('role_detail_users', 'users.id', '=', 'role_detail_users.user_id')
                ->join('role_details', 'role_details.id', '=', 'role_detail_users.role_details_id')
                ->where('users.id', $request->id)
                ->where('role_detail_users.role_details_id', 3)
                ->get()->map(function ($customerInfo) {
                    unset($customerInfo->user_id);
                    unset($customerInfo->created_at);
                    unset($customerInfo->updated_at);
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
    // Create a new customer
    public function createNewCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string|min:2|max:255',
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
            'is_valid_flag' => false,
        ]);
        RoleDetailUser::create([
            'user_id' => $customer->id,
            'role_details_id' => 3
        ]);
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
                    $customerUpdate->is_valid_flag = $request->is_valid_flag;
                    $customerUpdate->save();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Customer updated successfully!',
                    ]);
                }
                if ($request->hasFile('avatar')) {
                    $validatorUpdate = Validator::make($request->all(), [
                        'full_name' => 'string|min:2|max:255',
                        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $destination = 'uploads/avatar/' . $customerUpdate->avatar;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }
                    $image = $request->file('avatar');
                    $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
                    $image->move('uploads/avatar/', $fileName);
                    $customerUpdate->full_name = $request->full_name;
                    $customerUpdate->birthday = $request->birthday;
                    $customerUpdate->gender = $request->gender;
                    $customerUpdate->phone_number = $request->phone_number;
                    $customerUpdate->avatar = $fileName;
                    $customerUpdate->is_valid_flag = $request->is_valid_flag;
                    $customerUpdate->save();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Customer updated successfully!',
                    ]);
                }
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
            // $checkCustomer = User::where('id', $request->id)->first();
            $checkCustomer = User::find($request->id);
            if ($checkCustomer) {
                $destination = 'uploads/avatar/' . $checkCustomer->avatar;
                if (File::exists($destination)) {
                    File::delete($destination);
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
        dd(request('q'));
    }
    // Get all provider
    public function getAllProviders()
    {
        $providers = User::whereHas('user_role_details', function ($query) {
            return $query->where('role_details_id', '=', 2);
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
            $providerInfo = DB::table('users')
                ->join('role_detail_users', 'users.id', '=', 'role_detail_users.user_id')
                ->join('role_details', 'role_details.id', '=', 'role_detail_users.role_details_id')
                ->where('users.id', $request->id)
                ->get()->map(function ($providerInfo) {
                    unset($providerInfo->user_id);
                    unset($providerInfo->created_at);
                    unset($providerInfo->updated_at);
                    unset($providerInfo->email_verified_at);
                    unset($providerInfo->remember_token);
                    return $providerInfo;
                });
            if ($providerInfo->isEmpty()) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $providerInfo[0],
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
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string|min:2|max:255',
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
            'is_valid_flag' => false,
        ]);
        RoleDetailUser::create([
            'user_id' => $provider->id,
            'role_details_id' => 3
        ]);
        return response()->json([
            'data' => $provider,
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
                if ($request->file('avatar') == null) {
                    $validatorUpdate = Validator::make($request->all(), [
                        'email' => 'string|email|max:255|unique:users',
                        'full_name' => 'string|min:2|max:255',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $providerUpdate->email = $request->email;
                    $providerUpdate->full_name = $request->full_name;
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
                    $providerUpdate->is_valid_flag = $request->is_valid_flag;
                    $providerUpdate->save();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Customer updated successfully!',
                    ]);
                }
                if ($request->hasFile('avatar')) {
                    $validatorUpdate = Validator::make($request->all(), [
                        'email' => 'string|email|max:255|unique:users',
                        'full_name' => 'string|min:2|max:255',
                        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $destination = 'uploads/avatar/' . $providerUpdate->avatar;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }
                    $image = $request->file('avatar');
                    $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
                    $image->move('uploads/avatar/', $fileName);
                    $providerUpdate->email = $request->email;
                    $providerUpdate->full_name = $request->full_name;
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
                    $providerUpdate->is_valid_flag = $request->is_valid_flag;
                    $providerUpdate->avatar = $fileName;
                    $providerUpdate->save();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Provider updated successfully!',
                    ]);
                }
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
            $checkProvider = User::where('id', $request->id)->first();
            if ($checkProvider) {
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
        dd(request('q'));
    }
}
