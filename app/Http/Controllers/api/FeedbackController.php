<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    // Get all feedbacks
    public function getAllFeedbacks()
    {
        $feedbacks = Feedback::all();
        return response()->json([
            'data' => $feedbacks,
            'statusCode' => 200,
            'message' => 'Get all feedbacks successful!',
        ]);
    }
    // Get feedback by Id
    public function getFeedbackById(Request $request)
    {
        if ($request->id) {
            $feedbackInfo = Feedback::find($request->id);
            if ($feedbackInfo->isEmpty()) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $feedbackInfo,
                'statusCode' => 200,
                'message' => 'Get feedback info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing feedback id parameter!',
        ]);
    }
    // Get all feedbacks by appointment_id
    public function getAllFeedbacksByAppointmentId(Request $request)
    {
        if (!$request->appointment_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing appointment_id parameter!',
            ]);
        }
        $feedbacks = Feedback::where('appointment_id', '=', $request->appointment_id)->get();
        return response()->json([
            'data' => $feedbacks,
            'statusCode' => 200,
            'message' => 'Get all feedbacks successful!',
        ]);
    }
    // Create a new feedback
    public function createNewFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|numeric',
            'comment' => 'string|min:2|max:255',
            'reply' => 'string|min:2|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $checkExistAppointment = Appointment::find($request->appointment_id);
        if (!$checkExistAppointment) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding appointment!',
            ]);
        }
        $feedback = Feedback::create([
            'appointment_id' => $request->appointment_id,
            'comment' => $request->comment,
            'reply' => $request->reply,
            'star' => 0,
        ]);
        return response()->json([
            'data' => $feedback,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Update feedback
    public function updateFeedback(Request $request)
    {
        if ($request->id) {
            $feedbackUpdate = Feedback::find($request->id);
            if ($feedbackUpdate) {
                $validator = Validator::make($request->all(), [
                    'comment' => 'string|min:2|max:255',
                    'reply' => 'string|min:2|max:255',
                    'star' => 'numeric',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "statusCode" => 400,
                        "message" => "Validation error!",
                        "errors" => $validator->errors()
                    ]);
                }
                Feedback::where('id', $request->id)->update([
                    'comment' => $request->comment,
                    'reply' => $request->reply,
                    'star' => $request->star,
                    'reply_at' => $request->reply_at,
                ]);
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Feedback updated successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the feedback you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing feedback id parameter!',
        ]);
    }
    // Hard delete feedback
    public function hardDeleteFeedback(Request $request)
    {
        if ($request->id) {
            $checkFeedback = Feedback::where('id', $request->id)->first();
            if ($checkFeedback) {
                Feedback::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted feedback successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the feedback you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing feedback id parameter!',
        ]);
    }
}
