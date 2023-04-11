<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AttachPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Validator;

class AttachPhotoController extends Controller
{
    // Create Attach Photo
    public function createAttachPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "statusCode" => 400,
                "message" => "Validation error",
                "errors" => $validator->errors()
            ]);
        }
        $checkExistAppointment = Appointment::find($request->appointment_id);
        if (!$checkExistAppointment) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding appointment!',
            ]);
        }
        if ($request->has('image')) {
            $image = $request->file('image');
            $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/appointment-attach-photo/', $fileName);
            $attachPhoto = AttachPhoto::create([
                'appointment_id' => $request->appointment_id,
                'image' => $fileName,
            ]);
            return response()->json([
                'data' => $attachPhoto,
                'statusCode' => 201,
                'message' => 'Attach photo created successfully!',
            ]);
        }
        return response()->json([
            "statusCode" => 400,
            "message" => "Missing image for attach photo",
        ]);
    }
    // Hard Delete Attach Photo
    public function hardDeleteAttachPhoto(Request $request)
    {
        if ($request->id) {
            $checkAttachPhoto = AttachPhoto::find($request->id);
            if ($checkAttachPhoto) {
                $destination = 'uploads/appointment-attach-photo/' . $checkAttachPhoto->image;
                if (File::exists($destination)) {
                    File::delete($destination);
                }
                AttachPhoto::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted attach photo successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the attach photo you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing attach photo id parameter!',
        ]);
    }
}