<?php

namespace App\Http\Controllers\api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Validator;

class CategoryController extends Controller
{
    // Get all categories
    public function getAllCategories()
    {
        $categories = Category::get();
        return response()->json([
            'data' => $categories,
            'statusCode' => 200,
            'message' => 'Get all categories successful!',
        ]);
    }

    // Get category by Id
    public function getCategoryById(Request $request)
    {
        if ($request->id) {
            $categoryInfo = Category::with('logo')->find($request->id);
            if (!$categoryInfo) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $categoryInfo,
                'statusCode' => 200,
                'message' => 'Get category info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing category id parameter!',
        ]);
    }

    // Create a new category
    public function createNewCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'name' => 'required|string|min:2|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'view_priority' => 'numeric|integer',
            'is_valid' => 'integer|between:0,1',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "statusCode" => 400,
                "message" => "Validation error",
                "errors" => $validator->errors()
            ]);
        }
        if ($request->has('logo')) {
            $image = $request->file('logo');
            $category = Category::create([
                'name' => $request->name,
                'total_provider' => 0,
                'view_priority' => $request->view_priority,
                'is_valid' => true,
            ]);
            $service = new ImageService();
            $service->uploadImage($image, $category->id, 'category');
            $category = Category::find($category->id);
            return response()->json([
                'data' => $category,
                'statusCode' => 201,
                'message' => 'Category created successfully!',
            ]);
        }
        return response()->json([
            "statusCode" => 400,
            "message" => "Missing logo for category",
        ]);
    }

    // Update category
    public function updateCategory(Request $request)
    {
        if ($request->id) {
            $categoryUpdate = Category::find($request->id);
            if ($categoryUpdate) {
                if ($request->file('logo') == null) {
                    $validatorUpdate = Validator::make($request->all(), [
                        'name' => 'string|min:2|max:255',
                        'total_provider' => 'numeric|integer',
                        // 'view_priority' => 'numeric|integer',
                        'is_valid' => 'integer|between:0,1'
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $categoryUpdate->name = $request->name;
                    $categoryUpdate->view_priority = $request->view_priority;
                    $categoryUpdate->is_valid = $request->is_valid;
                    $categoryUpdate->save();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Category updated successfully!',
                    ]);
                }
                if ($request->hasFile('logo')) {
                    $validatorUpdate = Validator::make($request->all(), [
                        'name' => 'string|min:2|max:255',
                        'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        'total_provider' => 'numeric|integer',
                        // 'view_priority' => 'numeric|integer',
                        'is_valid' => 'integer|between:0,1'
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }

                    $image = $request->file('logo');
                    $categoryUpdate->name = $request->name;
                    $categoryUpdate->view_priority = $request->view_priority;
                    $categoryUpdate->is_valid = $request->is_valid;
                    $categoryUpdate->save();
                    $image = $categoryUpdate->image;
                    $service = new ImageService();
                    if ($image) {
                        $service->deleteImage($image->id);
                        $image->delete();
                    }
                    $service->uploadImage($request->file('logo'), $categoryUpdate->id, 'category');
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Category updated successfully!',
                    ]);
                }
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the category you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing category id parameter!',
        ]);
    }

    // Hard delete category
    public function hardDeleteCategory(Request $request)
    {
        if ($request->id) {
            $checkCategory = Category::find($request->id);
            if ($checkCategory) {
                $image = $checkCategory->image;
                Category::where('id', $request->id)->delete();
                // (new ImageService())->deleteImage($image->id);
                // $image->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted category successfully!',
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

    // Searching, paginating and sorting categories
    public function searchPaginationCategories(Request $request)
    {
        $sort = $request->sort;
        $filter = $request->filter;
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;
        $categories = Category::with('logo');
        if ($filter) {
            $categories = $this->_filterCategories($categories, $filter);
        }
        if ($sort) {
            foreach ($sort as $sortArray) {
                $categories->orderBy($sortArray['sort_by'], $sortArray['sort_dir']);
            }
        }
        return $categories->paginate($limit, ['*'], 'page', $page);
    }

    private function _filterCategories(&$categories, $filter)
    {
        if (isset($filter['name'])) {
            $categories->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        if (isset($filter['is_valid'])) {
            $categories->where('is_valid', $filter['is_valid']);
        }
        return $categories;
    }
    // Get category that provider doesn't have
    public function getCategoriesProviderDoNotHave(Request $request)
    {
        $providerId = $request->provider_id;
        $categories = DB::select('SELECT *
                                    FROM categories
                                    WHERE categories.id NOT IN (
                                        SELECT category_id
                                        FROM services
                                        WHERE provider_id = ' . $providerId . ')');
        return response()->json([
            'data' => $categories,
            'statusCode' => 200,
            'message' => 'successfully!',
        ]);
    }
    public function getCategoriesForProvider(Request $request)
    {
        $dataCategoryReturn = array();
        $providerId = $request->provider_id;
        $serviceInfo = DB::select('SELECT * FROM services where provider_id = ? ', [$providerId]);

        foreach ($serviceInfo as $item) {
            $dataCategory = DB::select('SELECT * FROM categories WHERE id = ?', [$item->category_id]);
            $countPackage = Package::where('service_id', '=', $item->id)->count();
            $object = (object) [
                'dataCategory' => $dataCategory,
                'countPackage' => $countPackage,
            ];
            array_push($dataCategoryReturn, $object);
        }
        return response()->json([
            'data' => $dataCategoryReturn,
            'statusCode' => 200,
            'message' => 'successfully!',
        ]);
    }
}
