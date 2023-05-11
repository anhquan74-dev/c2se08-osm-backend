<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Validator;
use App\Models\Appointment;
use App\Models\Package;
use App\Models\User;

class AppointmentController extends Controller
{
    // Get total appointment 12 months ago from now
    public function getTotalAppointmentByMonthsFromNow()
    {
        $dataByMonths = [];
        $currentMonth = date('n');
        for ($i = $currentMonth; $i <= $currentMonth + 11; $i++) {
            $monthNumber = $i < 12 ? $i + 1 : $i - 11;
            $yearNumber = $i < 12 ? date('Y') - 1 : date('Y');
            $data = Appointment::whereMonth('created_at', $monthNumber)
                ->whereYear('created_at', $yearNumber)
                ->where('status', '=', 'done')
                ->count();
            $object = (object) [
                'month' => $monthNumber . '-' . $yearNumber,
                'total' => $data,
            ];
            array_push($dataByMonths, $object);
        }
        return response()->json(['data_by_months' => $dataByMonths]);
    }
    // Get all appointments
    public function getAllAppointments()
    {
        $appointments = Appointment::with('attachPhoto')->get();

        return response()->json([
            'data' => $appointments,
            'statusCode' => 200,
            'message' => 'Get all appointments successful!',
        ]);
    }
    // Get appointment by Id
    public function getAppointmentById(Request $request)
    {
        if ($request->id) {
            $appointmentInfo = Appointment::with('attachPhoto')->find($request->id);
            if (!$appointmentInfo) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $appointmentInfo,
                'statusCode' => 200,
                'message' => 'Get appointment info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing appointment id parameter!',
        ]);
    }
    // Get all appointments by customer_id
    public function getAllAppointmentsByCustomerId(Request $request)
    {
        if (!$request->customer_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing customer_id parameter!',
            ]);
        }
        $appointments = Appointment::where('customer_id', '=', $request->customer_id)->with('attachPhoto')->get();
        return response()->json([
            'data' => $appointments,
            'statusCode' => 200,
            'message' => 'Get all appointments successful!',
        ]);
    }
    // Get all appointments by package_id
    public function getAllAppointmentsByPackageId(Request $request)
    {
        if (!$request->package_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing package_id parameter!',
            ]);
        }
        $appointments = Appointment::where('package_id', '=', $request->package_id)->with('attachPhoto')->get();
        return response()->json([
            'data' => $appointments,
            'statusCode' => 200,
            'message' => 'Get all appointments successful!',
        ]);
    }
    // Get all appointments by status
    public function getAllAppointmentsByStatus(Request $request)
    {
        if (!$request->status) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing status parameter!',
            ]);
        }
        $appointments = Appointment::where('status', '=', $request->status)->with('attachPhoto')->get();
        return response()->json([
            'data' => $appointments,
            'statusCode' => 200,
            'message' => 'Get all appointments successful!',
        ]);
    }
    // Create a new appointment
    public function createNewAppointment(Request $request)
    {
        $input_data = $request->all();
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'note_for_provider' => 'string|min:2|max:255',
            'location' => 'string|min:2|max:255',
            'price' => 'required|numeric',
            'price_unit' => 'string|min:2|max:255',
            'date' => 'date_format:Y-m-d H:i:s',
            'status' => 'string|min:2|max:255',
            $input_data, [
                'attach_photos.*' => 'mimes:jpg,jpeg,png,bmp,gif,svg|max:2048'
            ], [
                'attach_photos.*.mimes' => 'Only jpeg,png,jpg,gif and svg images are allowed',
                'attach_photos.*.max' => 'Sorry! Maximum allowed size for an image is 2MB',
            ]
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        if ($request->file('attach_photos') == null) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing attach photos!',
            ]);
        }
        $checkExistPackage = Package::find($request->package_id);
        if (!$checkExistPackage) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding package!',
            ]);
        }
        $checkExistCustomer = User::find($request->customer_id);
        if (!$checkExistCustomer) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding customer!',
            ]);
        }
        // create appointment
        $appointment = Appointment::create([
            'package_id' => $request->package_id,
            'customer_id' => $request->customer_id,
            'note_for_provider' => $request->note_for_provider,
            'location' => $request->location,
            'date' => $request->date,
            'price' => $request->price,
            'price_unit' => $request->price_unit,
            'status' => $request->status,
            'offer_date' => $request->offer_date,
        ]);
        $imageService = new ImageService();
        // create attach_photos
        if ($request->has('attach_photos')) {
            $photos = $request->attach_photos;
            foreach ($photos as $photo) {
                $imageService->uploadImage($attach_photo, $appointment->id, 'appointment');
            }
        }
        $appointmentCurrent = Appointment::with('attachPhoto')->find($appointment->id);
        return response()->json([
            'data' => $appointmentCurrent,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Update appointment
    public function updateAppointment(Request $request)
    {
        if ($request->id) {
            $appointmentUpdate = Appointment::find($request->id);
            if ($appointmentUpdate) {
                $validator = Validator::make($request->all(), [
                    'note_for_provider' => 'string|min:2|max:255',
                    'location' => 'string|min:2|max:255',
                    'price' => 'numeric',
                    'price_unit' => 'string|max:255',
                    'status' => 'string|max:255',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "statusCode" => 400,
                        "message" => "Validation error!",
                        "errors" => $validator->errors()
                    ]);
                }
                Appointment::where('id', $request->id)->update([
                    'note_for_provider' => $request->note_for_provider,
                    'location' => $request->location,
                    'date' => $request->date,
                    'price' => $request->price,
                    'price_unit' => $request->price_unit,
                    'status' => $request->status,
                    'offer_date' => $request->offer_date,
                    'complete_date' => $request->complete_date,
                    'cancel_date' => $request->cancel_date,
                ]);
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Appointment updated successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the appointment you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing appointment id parameter!',
        ]);
    }
    // Hard delete appointment
    public function hardDeleteAppointment(Request $request)
    {
        if ($request->id) {
            $checkAppointment = Appointment::where('id', $request->id)->first();
            if ($checkAppointment) {
                // Delete file in server
                $attachPhotos = $checkAppointment->attachPhotos;
                $service = new ImageService();
                foreach ($attachPhotos as $attachPhoto) {
                    $service->deleteImage($attachPhoto->id);
                }
                // Delete appointment
                Appointment::where('id', $request->id)->delete();
                // Delete attach_photo of appointment
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted appointment successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the appointment you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing appointment id parameter!',
        ]);
    }
}
