<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

use App\Models\Message;

class MessageController extends Controller
{
    // Create message
    public function createMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
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
        $favorite = Message::create([
            'provider_id' => $request->provider_id,
            'customer_id' => $request->customer_id,
            'content' => $request->content,
        ]);
        return response()->json([
            'data' => $favorite,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Get all messages by provider_id and customer_id
    public function getMessages(Request $request)
    {
        if (!$request->customer_id || !$request->provider_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing customer_id or provider_id parameter!',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $messages = Message::where('customer_id', '=', $request->customer_id)->where('provider_id', '=', $request->provider_id)->get();
        return response()->json([
            'data' => $messages,
            'statusCode' => 200,
            'message' => 'Get all messages successful!',
        ]);
    }
    // Hard delete message
    public function hardDeleteMessage(Request $request)
    {
        if ($request->id) {
            $checkMessage = Message::find($request->id);
            if ($checkMessage) {
                Message::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted message successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the message you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing message id parameter!',
        ]);
    }
}
