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
        $customers = User::whereHas('user_role_details', function ($query) {
            return $query->where('role_id', '=', 3);
        })->get();
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
                ->join('role_details', 'role_details.id', '=', 'role_detail_users.role_id')
                ->where('users.id', $request->id)
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
            'role_id' => 3
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
                    $customerUpdate->email = $request->email;
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
                    $destination = 'uploads/avatar/' . $customerUpdate->avatar;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }
                    $image = $request->file('avatar');
                    $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
                    $image->move('uploads/avatar/', $fileName);
                    $customerUpdate->email = $request->email;
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
            $checkCustomer = User::where('id', $request->id)->first();
            if ($checkCustomer) {
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
}
