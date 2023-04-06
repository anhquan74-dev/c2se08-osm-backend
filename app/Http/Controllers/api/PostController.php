<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class PostController extends Controller
{
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
            $postInfo = Post::find($request->id);
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
    // Create a new post
    public function createNewPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:2|max:255',
            'author_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $checkExistAuthor = User::find($request->author_id);
        if (!$checkExistAuthor) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding author!',
            ]);
        }
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'description' => $request->description,
            'author_id' => $request->author_id,
            'date' => $request->date,
            'tags' => $request->tags,
            'is_valid_flag' => false,
        ]);
        return response()->json([
            'data' => $post,
            'statusCode' => 201,
            'message' => 'Successful created!',
        ]);
    }
    // Update post
    public function updatePost(Request $request)
    {
        if ($request->id) {
            $postUpdate = Post::find($request->id);
            if ($postUpdate) {
                $validator = Validator::make($request->all(), [
                    'title' => 'string|min:2|max:255',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "statusCode" => 400,
                        "message" => "Validation error!",
                        "errors" => $validator->errors()
                    ]);
                }
                Post::where('id', $request->id)->update([
                    'title' => $request->title,
                    'content' => $request->content,
                    'description' => $request->description,
                    'date' => $request->date,
                    'tags' => $request->tags,
                    'is_valid_flag' => $request->is_valid_flag,
                ]);
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Post updated successfully!',
                ]);
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
            $checkPost = Post::where('id', $request->id)->first();
            if ($checkPost) {
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
        dd(request('q'));
    }
}
