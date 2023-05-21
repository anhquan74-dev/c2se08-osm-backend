<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Get total post
    public function getTotalPost()
    {
        $postsCount = Post::count();
        return response()->json([
            'data' => $postsCount,
            'statusCode' => 200,
            'message' => 'Count all posts successfully!',
        ]);
    }
    // Get all posts
    public function getAllPosts()
    {
        $posts = Post::all();
        return response()->json([
            'data' => $posts,
            'statusCode' => 200,
            'message' => 'Get all posts successful!',
        ]);
    }
    // Get post by Id
    public function getPostById(Request $request)
    {
        if ($request->id) {
            $postInfo = Post::with(['image', 'category:categories.id,name'])->find($request->id);
            if (!$postInfo) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Not found!',
                ]);
            }
            return response()->json([
                'data' => $postInfo,
                'statusCode' => 200,
                'message' => 'Get post info successfully!',
            ]);
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing post id parameter!',
        ]);
    }
    // Get all posts by category_id
    public function getAllPostsByCategoryId(Request $request)
    {
        if (!$request->category_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing category_id parameter!',
            ]);
        }
        $posts = Post::with(['image', 'category:categories.id,name'])->where('category_id', '=', $request->category_id)->get();
        return response()->json([
            'data' => $posts,
            'statusCode' => 200,
            'message' => 'Get all posts successful!',
        ]);
    }
    // Create a new post
    public function createNewPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'title' => 'required|string|min:2|max:255',
            // 'post_content' => 'string|max:500',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'author_id' => 'required|numeric|integer',
            // 'date' => 'date_format:Y-m-d H:i:s',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "statusCode" => 400,
                "message" => "Validation error",
                "errors" => $validator->errors()
            ]);
        }
        // $checkExistAuthor = User::find($request->author_id);
        // if (!$checkExistAuthor) {
        //     return response()->json([
        //         'statusCode' => 404,
        //         'message' => 'Can not find the corresponding author!',
        //     ]);
        // }
        if ($request->has('image')) {

            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'date' => $request->date,
                'category_id' => $request->category_id,
                'is_valid' => $request->is_valid,
            ]);
            $image = $request->file('image');
            $service = new ImageService();
            $service->uploadImage($image, $post->id, 'post');
            return response()->json([
                'data' => $post,
                'statusCode' => 201,
                'message' => 'Successful created!',
            ]);
        }
        return response()->json([
            "statusCode" => 400,
            "message" => "Missing image for post",
        ]);
    }
    // Update post
    public function updatePost(Request $request)
    {
        if ($request->id) {
            $postUpdate = Post::find($request->id);
            if ($postUpdate) {

                if ($request->file('image') == null) {
                    $validatorUpdate = Validator::make($request->all(), [
                        // 'title' => 'string|min:2|max:255',
                        // 'post_content' => 'string|max:500',
                        // 'date' => 'date_format:Y-m-d H:i:s',
                        // 'is_valid' => 'integer|between:0,1'
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $postUpdate->category_id = $request->category_id;
                    $postUpdate->title = $request->title;
                    $postUpdate->content = $request->content;
                    $postUpdate->date = $request->date;
                    $postUpdate->is_valid = $request->is_valid;
                    $postUpdate->save();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Post updated successfully!',
                    ]);
                }
                if ($request->hasFile('image')) {
                    $validatorUpdate = Validator::make($request->all(), []);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $postUpdate->category_id = $request->category_id;
                    $postUpdate->title = $request->title;
                    $postUpdate->content = $request->content;
                    $postUpdate->date = $request->date;
                    $postUpdate->is_valid = $request->is_valid;
                    $postUpdate->save();
                    $image = $postUpdate->image;
                    $imageService = new ImageService();
                    $imageService->deleteImage($image->id);
                    $image->delete();
                    $imageService->uploadImage($request->file('image'), $postUpdate->id, 'post');
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Post updated successfully!',
                    ]);
                }
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the post you want to update!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing post id parameter!',
        ]);
    }
    // Hard delete post
    public function hardDeletePost(Request $request)
    {
        if ($request->id) {
            $checkPost = Post::find($request->id);
            if ($checkPost) {
                $image = $checkPost->image;
                (new ImageService())->deleteImage($image->id);
                $image->delete();
                Post::where('id', $request->id)->delete();
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Deleted post successfully!',
                ]);
            } else {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "Can't find the post you want to delete!"
                ]);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Missing post id parameter!',
        ]);
    }
    // Searching, paginating and sorting posts
    public function searchPaginationPosts(Request $request)
    {
        $sort   = $request->sort;
        $filter = $request->filter;
        $limit  = $request->limit ?? 10;
        $page   = $request->page ?? 1;
        $posts = Post::with(['image', 'category:categories.id,name']);
        if ($filter) {
            $posts = $this->_filterPost($posts, $filter);
        }
        if ($sort) {
            foreach ($sort as $sortArray) {
                $posts->orderBy($sortArray['sort_by'], $sortArray['sort_dir']);
            }
        }
        return $posts->paginate($limit, ['*'], 'page', $page);
    }

    private function _filterPost(&$posts, $filter)
    {
        if (isset($filter['title'])) {
            $posts->where('title', 'LIKE', '%' . $filter['title'] . '%');
        }

        if (isset($filter['category_id'])) {
            $posts->where('category_id', $filter['category_id']);
        }

        if (isset($filter['is_valid'])) {
            $posts->where('is_valid', $filter['is_valid']);
        }
        return $posts;
    }
}
