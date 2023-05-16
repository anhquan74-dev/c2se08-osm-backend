<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class ServiceController extends Controller
{
    // Get total services
    public function getTotalService()
    {
        $servicesCount = Service::count();
        return response()->json([
            'data' => $servicesCount,
            'statusCode' => 200,
            'message' => 'Count all services successfully!',
        ]);
    }
    // Get all services
    public function getAllServices()
    {
        $services = Service::all();
        return response()->json([
            'data' => $services,
            'statusCode' => 200,
            'message' => 'Get all services successful!',
        ]);
    }
    // Get service by Id
    public function getServiceById(Request $request)
    {
        if ($request->id) {
            $serviceInfo = Service::find($request->id);
            if (!$serviceInfo) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $serviceInfo,
                'statusCode' => 200,
                'message' => 'Get service info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing service id parameter!',
        ]);
    }
    // Get all services by provider_id
    public function getAllServicesByProviderId(Request $request)
    {
        if (!$request->provider_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing provider_id parameter!',
            ]);
        }
        $services = Service::where('provider_id', '=', $request->provider_id)->get();
        if (count($services) == 0) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Service not found!',
            ]);
        }
        $result = [];
        foreach ($services as $service) {
            $totalPackages = Package::where('service_id', '=', $service->id)->count();
            $object = (object) [
                'service' => $service,
                'totalPackages' => $totalPackages,
            ];
            array_push($result, $object);
        }
        return response()->json([
            'data' => $result,
            'statusCode' => 200,
            'message' => 'Get all services successful!',
        ]);
    }
    // Get all services by category_id
    public function getAllServicesByCategoryId(Request $request)
    {
        if (!$request->category_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing category_id parameter!',
            ]);
        }
        $services = Service::where('category_id', '=', $request->category_id)->get();

        if (count($services) == 0) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Service not found!',
            ]);
        }

        return response()->json([
            'data' => $services,
            'statusCode' => 200,
            'message' => 'Get all services successful!',
        ]);
    }
    // Create a new service
    public function createNewService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric|integer',
            'provider_id' => 'required|numeric|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $checkExistUser = User::find($request->provider_id);
        if (!$checkExistUser) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding user!',
            ]);
        }
        $checkExistCategory = Category::find($request->category_id);
        if (!$checkExistCategory) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding category!',
            ]);
        }
        $service = Service::create([
            'category_id' => $request->category_id,
            'provider_id' => $request->provider_id,
            'avg_price' => 0,
            'max_price' => 0,
            'min_price' => 0,
            // 'is_negotiable' => false,
            'total_rate' => 0,
            'total_star' => 0,
            'avg_star' => 0,
            'number_of_packages' => 0,
            'is_valid' => true,
            'name' => $checkExistCategory->name
        ]);
        return response()->json([
            'data' => $service,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Update service
    public function updateService(Request $request)
    {
        if ($request->id) {
            $serviceUpdate = Service::find($request->id);
            if ($serviceUpdate) {
                $validator = Validator::make($request->all(), [
                    'avg_price' => 'numeric|integer',
                    'max_price' => 'numeric|integer',
                    'min_price' => 'numeric|integer',
                    'is_negotiable' => 'integer|between:0,1',
                    'total_rate' => 'numeric|integer',
                    'total_star' => 'numeric|integer',
                    'avg_star' => 'numeric',
                    'number_of_packages' => 'numeric|integer',
                    'is_valid' => 'integer|between:0,1'
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "statusCode" => 400,
                        "message" => "Validation error!",
                        "errors" => $validator->errors()
                    ]);
                }
                Service::where('id', $request->id)->update([
                    'avg_price' => $request->avg_price,
                    'max_price' => $request->max_price,
                    'min_price' => $request->min_price,
                    'total_rate' => $request->total_rate,
                    'total_star' => $request->total_star,
                    'avg_star' => $request->avg_star,
                    'number_of_packages' => $request->number_of_packages,
                    'is_negotiable' => $request->is_negotiable,
                    'is_valid' => $request->is_valid,
                ]);
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Service updated successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the service you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing service id parameter!',
        ]);
    }
    // Hard delete service
    public function hardDeleteService(Request $request)
    {
        if ($request->id) {
            $checkService = Service::where('id', $request->id)->first();
            if ($checkService) {
                Package::where('service_id', '=', $checkService->id)->delete();
                Service::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted service successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the service you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing service id parameter!',
        ]);
    }
    // Hard delete service by category id
    public function hardDeleteServiceByCategory(Request $request)
    {
        if ($request->category_id) {
            $categoryId = $request->category_id;
            $checkCategory = Category::where('id', $categoryId)->first();
            if ($checkCategory) {
                $checkService = Service::where('category_id', $categoryId)->get();
                foreach ($checkService as $item) {
                    Package::where('service_id', '=', $item->id)->delete();
                }
                Service::where('category_id', $categoryId)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted services successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the category you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing category id parameter!',
        ]);
    }

    // Get service by provider_id & category_id
    public function getServicesByProviderAndCategory(Request $request)
    {
        if (!$request->provider_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing provider_id parameter!',
            ]);
        }
        if (!$request->category_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing category_id parameter!',
            ]);
        }
        $service = Service::where('provider_id', '=', $request->provider_id)->where('category_id', '=', $request->category_id)->get();

        if (
            count($service) == 0
        ) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Service not found!',
            ]);
        }
        return response()->json([
            'data' => $service,
            'statusCode' => 200,
            'message' => 'Get service successful!',
        ]);
    }
}
