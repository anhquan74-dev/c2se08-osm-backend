<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use App\Models\Notify;

class NotifyController extends Controller
{
    public function createNotify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|numeric|integer',
            'customer_id' => 'required|numeric|integer',
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
        $favorite = Notify::create([
            'provider_id' => $request->provider_id,
            'customer_id' => $request->customer_id,
            'content' => $request->content,
            'is_cus_deleted' => 0,
            'is_prov_deleted' => 0,
        ]);
        return response()->json([
            'data' => $favorite,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Get all notifies by customer_id
    public function getNotifiesByCustomerId(Request $request)
    {
        if (!$request->customer_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing customer_id parameter!',
            ]);
        }
        $notifies = Notify::where('customer_id', '=', $request->customer_id)->get();
        return response()->json([
            'data' => $notifies,
            'statusCode' => 200,
            'message' => 'Get all notifies successful!',
        ]);
    }
    // Get all notifies by provider_id
    public function getNotifiesByProviderId(Request $request)
    {
        if (!$request->provider_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing provider_id parameter!',
            ]);
        }
        $notifies = Notify::where('provider_id', '=', $request->provider_id)->get();
        return response()->json([
            'data' => $notifies,
            'statusCode' => 200,
            'message' => 'Get all notifies successful!',
        ]);
    }
    // When customer delete notify
    public function deleteNotifyByCustomer(Request $request)
    {
        if ($request->id) {
            $checkNotify = Notify::find($request->id);
            if ($checkNotify) {
                $checkNotify->is_cus_deleted = 1;
                $checkNotify->save();
                $getCurrentNotifyInfo = Notify::find($request->id);
                // dd($getCurrentNotifyInfo);
                if ($getCurrentNotifyInfo->is_cus_deleted === 1 && $getCurrentNotifyInfo->is_prov_deleted === 1) {
                    Notify::where('id', $request->id)->delete();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Deleted notify successfully!',
                    ]);
                }
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted notify for customer successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the notify you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing notify id parameter!',
        ]);
    }
    // When provider delete notify
    public function deleteNotifyByProvider(Request $request)
    {
        if ($request->id) {
            $checkNotify = Notify::find($request->id);
            if ($checkNotify) {
                $checkNotify->is_prov_deleted = 1;
                $checkNotify->save();
                $getCurrentNotifyInfo = Notify::find($request->id);
                if ($getCurrentNotifyInfo->is_cus_deleted === 1 && $getCurrentNotifyInfo->is_prov_deleted === 1) {
                    Notify::where('id', $request->id)->delete();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Deleted notify successfully!',
                    ]);
                }
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted notify for provider successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the notify you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing notify id parameter!',
        ]);
    }
}
