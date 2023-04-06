<?php

namespace App\Http\Controllers\api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Validator;

class CategoryController extends Controller
{
    // Get all categories
    public function getAllCategories()
    {
        $categories = Category::all();
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
            $categoryInfo = Category::find($request->id);
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
            'name' => 'required|string|min:2|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/category-logo/', $fileName);
            $category = Category::create([
                'name' => $request->name,
                'logo' => $fileName,
                'total_provider' => 0,
                'view_priority' => 0,
                'is_valid_flag' => false,
            ]);
            return response()->json([
                'data' => $category,
                'statusCode' => 201,
                'message' => 'Category created successfully!',
            ]);
        }
        return response()->json('Please try again');
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
                        'total_provider' => 'numeric',
                        'view_priority' => 'numeric',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $categoryUpdate->name = $request->name;
                    $categoryUpdate->total_provider = $request->total_provider;
                    $categoryUpdate->view_priority = $request->view_priority;
                    $categoryUpdate->is_valid_flag = $request->is_valid_flag;
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
                        'total_provider' => 'numeric',
                        'view_priority' => 'numeric',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $destination = 'uploads/category-logo/' . $categoryUpdate->logo;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }
                    $image = $request->file('logo');
                    $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
                    $image->move('uploads/category-logo/', $fileName);
                    $categoryUpdate->name = $request->name;
                    $categoryUpdate->logo = $fileName;
                    $categoryUpdate->total_provider = $request->total_provider;
                    $categoryUpdate->view_priority = $request->view_priority;
                    $categoryUpdate->is_valid_flag = $request->is_valid_flag;
                    $categoryUpdate->save();
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
            $checkCategory = Category::where('id', $request->id)->first();
            if ($checkCategory) {
                Category::where('id', $request->id)->delete();
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
        dd(request('q'));
    }
}
