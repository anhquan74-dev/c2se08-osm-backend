<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Validator;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Package;
use App\Models\Service;
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
        $appointments = Appointment::all();

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
            $appointmentInfo = Appointment::find($request->id);
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
        $appointments = Appointment::where('customer_id', '=', $request->customer_id)->get();
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
        $appointments = Appointment::where('package_id', '=', $request->package_id)->get();
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
        $appointments = Appointment::with('attachPhoto')->where('status', '=', $request->status)->get();
        return response()->json([
            'data' => $appointments,
            'statusCode' => 200,
            'message' => 'Get all appointments successful!',
        ]);
    }
    // Get all appointments by status
    public function getAllAppointmentsByStatusForCustomer(Request $request)
    {
        if (!$request->status) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing status parameter!',
            ]);
        }
        if ($request->status == 'new-or-offered') {
            $appointments = Appointment::with([
                'attachPhoto', 'feedback', 'location' => function ($query) {
                    $query->select(['id', 'address']);
                }, 'package' => function ($query) {
                    $query->select('packages.id', 'packages.name');
                }
            ])->where('customer_id', $request->userId)->where('status', '=', 'new')->orWhere('status', '=', 'offered')->get();
            //             ->join('users', 'users.id', 'appointments.customer_id')->
            // ->join('users', 'users.id', 'appointments.customer_id')->
        } else {
            $appointments = Appointment::with([
                'attachPhoto', 'feedback', 'location' => function ($query) {
                    $query->select(['id', 'address']);
                }, 'package' => function ($query) {
                    $query->select('packages.id', 'packages.name');
                }
            ])->where('customer_id', $request->userId)->where('status', '=', $request->status)->get();
        }
        $appointments->map(function ($appointment) {
            // $customer = User::with('avatar')->join('appointments', 'appointments.customer_id', 'users.id')
            //     ->where('appointments.id', '=', $appointment->id)
            //     ->select('users.id', 'users.full_name', 'users.phone_number')
            //     ->first();
            $provider = User::with('avatar')
                ->join('services', 'services.provider_id', 'users.id')
                ->join('packages', 'packages.service_id', 'services.id')
                ->join('appointments', 'appointments.package_id', 'packages.id')
                ->where('appointments.id', '=', $appointment->id)
                ->select('users.id', 'users.full_name')
                ->first();
            $service = Service::join('packages', 'packages.service_id', 'services.id')
                ->join('appointments', 'appointments.package_id', 'packages.id')
                ->where('appointments.id', '=', $appointment->id)
                ->select('services.id', 'services.name')
                ->first();
            $appointment->provider = $provider;
            $appointment->service = $service;
            // $appointment->customer = $customer;
            return $appointment;
        });
        return response()->json([
            'data' =>  $appointments,
            'statusCode' => 200,
            'message' => 'Get all appointments successful!',
        ]);
    }

    // Get all appointments by status for provider
    public function getAllAppointmentsByStatusForProvider(Request $request)
    {
        if (!$request->status) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing status parameter!',
            ]);
        }

        $appointments = Appointment::with([
            'attachPhoto', 'feedback', 'location' => function ($query) {
                $query->select(['id', 'address']);
            }, 'package' => function ($query) {
                $query->select('packages.id', 'packages.name');
            }
        ])->where('status', '=', $request->status)
            ->get();

        $appointmentsResults = Appointment::join('packages', 'packages.id', 'appointments.package_id')
            ->join('services', 'services.id', 'packages.service_id')
            ->join('users', 'users.id', 'services.provider_id')
            ->where('users.id', '=', $request->userId)
            ->where('status', '=', $request->status)->select('appointments.*')->get();

        $results = [];

        foreach ($appointments as $appointment) {
            foreach ($appointmentsResults as $appointmentResult) {
                if ($appointment->id == $appointmentResult->id) {
                    array_push($results, $appointment);
                }
            }
        };


        // $results->map(function ($appointment) {
        //     $customer = User::with('avatar')->join('appointments', 'appointments.customer_id', 'users.id')
        //         ->where('appointments.id', '=', $appointment->id)
        //         ->select('users.id', 'users.full_name', 'users.phone_number')
        //         ->first();
        //     $service = Service::join('packages', 'packages.service_id', 'services.id')
        //         ->join('appointments', 'appointments.package_id', 'packages.id')
        //         ->where('appointments.id', '=', $appointment->id)
        //         ->select('services.id', 'services.name')
        //         ->first();
        //     $appointment->service = $service;
        //     $appointment->customer = $customer;
        //     return $appointment;
        // });
        return response()->json([
            'data' =>  $results,
            'statusCode' => 200,
            'message' => 'Get all appointments successful!',
        ]);
    }

    // get total appointment by status
    public function getTotalAppointmentsByStatus(Request $request)
    {
        if ($request->status === 'new-or-offered') {
            $appointments = Appointment::where('status', '=', 'new')->orWhere('status', '=', 'offered')->get();
        } else {
            $appointments = Appointment::where('status', '=', $request->status)->get();
        }

        return response()->json([
            'data' => count($appointments),
            'statusCode' => 200,
            'message' => 'Get total appointments by status successful!',
        ]);
    }

    // get total appointment by status
    public function getTotalAppointmentsByUser(Request $request)
    {
        $isCustomer = User::with('roles')->whereHas('roles', function ($query) {
            return $query->where('name', '=', 'customer');
        })->where('id', $request->user_id)->count();

        if ($isCustomer) {
            $appointed = Appointment::join('users', 'users.id', '=', 'appointments.customer_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'appointed')->get();
            $done = Appointment::join('users', 'users.id', '=', 'appointments.customer_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'done')->get();
            $canceled = Appointment::join('users', 'users.id', '=', 'appointments.customer_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'canceled')->get();
            $newOrOffered = Appointment::join('users', 'users.id', '=', 'appointments.customer_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'new')->orWhere('status', '=', 'offered')->get();
            $result = (object) [
                'newOrOffered' => count($newOrOffered),
                'appointed' => count($appointed),
                'done' => count($done),
                'canceled' => count($canceled)
            ];
        } else {
            $new = Appointment::join('packages', 'packages.id', '=', 'appointments.package_id')
                ->join('services', 'services.id', '=', 'packages.service_id')
                ->join('users', 'users.id', '=', 'services.provider_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'new')->get();
            $offered = Appointment::join('packages', 'packages.id', '=', 'appointments.package_id')
                ->join('services', 'services.id', '=', 'packages.service_id')
                ->join('users', 'users.id', '=', 'services.provider_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'offered')->get();
            $appointed = Appointment::join('packages', 'packages.id', '=', 'appointments.package_id')
                ->join('services', 'services.id', '=', 'packages.service_id')
                ->join('users', 'users.id', '=', 'services.provider_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'appointed')->get();
            $done = Appointment::join('packages', 'packages.id', '=', 'appointments.package_id')
                ->join('services', 'services.id', '=', 'packages.service_id')
                ->join('users', 'users.id', '=', 'services.provider_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'done')->get();
            $canceled = Appointment::join('packages', 'packages.id', '=', 'appointments.package_id')
                ->join('services', 'services.id', '=', 'packages.service_id')
                ->join('users', 'users.id', '=', 'services.provider_id')
                ->where('users.id', $request->user_id)
                ->where('status', '=', 'canceled')->get();
            $result = (object) [
                'new' => count($new),
                'offered' => count($offered),
                'appointed' => count($appointed),
                'done' => count($done),
                'canceled' => count($canceled)
            ];
        }

        return response()->json([
            'data' => $result,
            'statusCode' => 200,
            'message' => 'Get total appointments by status successful!',
        ]);
    }

    // Create a new appointment
    public function createNewAppointment(Request $request)
    {
        $input_data = $request->all();
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
            // 'note_for_provider' => 'string|min:2|max:255',
            // 'location' => 'string|min:2|max:255',
            // 'price' => 'required|numeric',
            // 'price_unit' => 'string|min:2|max:255',
            'date' => 'date_format:Y-m-d H:i:s',
            'status' => 'string|min:2|max:255',
            // $input_data, [
            //     'attach_photos.*' => 'mimes:jpg,jpeg,png,bmp,gif,svg|max:2048'
            // ], [
            //     'attach_photos.*.mimes' => 'Only jpeg,png,jpg,gif and svg images are allowed',
            //     'attach_photos.*.max' => 'Sorry! Maximum allowed size for an image is 2MB',
            // ]
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        // if ($request->file('attach_photos') == null) {
        //     return response()->json([
        //         'statusCode' => 400,
        //         'message' => 'Missing attach photos!',
        //     ]);
        // }
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
        $location = Location::create([
            'address' => $request->input('location.address'),
            'province_name' => $request->input('location.province_name'),
            'district_name' => $request->input('location.district_name'),
            'coords_latitude' => $request->input('location.coords_latitude'),
            'coords_longitude' => $request->input('location.coords_longitude'),
            'is_primary' => $request->input('location.is_primary'),
            'type' => $request->type,
        ]);
        // create appointment
        $appointment = Appointment::create([
            'package_id' => $request->package_id,
            'customer_id' => $request->customer_id,
            'note_for_provider' => $request->note_for_provider,
            'location_id' => $location->id,
            'date' => $request->date,
            'price' => $request->price,
            'price_unit' => $request->price_unit,
            'status' => $request->status,
            'job_status' => $request->status,
            'date' => $request->date,
        ]);

        $imageService = new ImageService();
        // create attach_photos
        if ($request->hasFile('attach_photos')) {
            $photo = $request->file('attach_photos');
            $imageService->uploadImage($photo, $appointment->id, 'appointment');
        }
        $appointmentCurrent = Appointment::find($appointment->id);
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
                    // 'note_for_provider' => 'string|min:2|max:255',
                    // 'location' => 'string|min:2|max:255',
                    // 'price' => 'numeric',
                    // 'price_unit' => 'string|max:255',
                    'status' => 'string|max:255',
                    'job_status' => 'string|max:255',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "statusCode" => 400,
                        "message" => "Validation error!",
                        "errors" => $validator->errors()
                    ]);
                }
                Appointment::where('id', $request->id)->update([
                    // 'note_for_provider' => $request->note_for_provider,
                    // 'date' => $request->date,
                    'price' => $request->price,
                    'status' => $request->status,
                    // 'offer_date' => $request->offer_date,
                    'complete_date' => $request->complete_date,
                    'cancel_date' => $request->cancel_date,
                    'job_status' => $request->job_status
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
                $attachPhoto = $checkAppointment->attachPhoto;
                $service = new ImageService();
                $service->deleteImage($attachPhoto->id);
                $attachPhoto->delete();
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
