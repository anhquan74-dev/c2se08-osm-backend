<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use Illuminate\Support\Str;

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
    // Get all posts by author_id
    public function getAllPostsByAuthorId(Request $request)
    {
        if (!$request->author_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing author_id parameter!',
            ]);
        }
        $posts = Post::where('author_id', '=', $request->author_id)->get();
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
            'title' => 'required|string|min:2|max:255',
            'author_id' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "statusCode" => 400,
                "message" => "Validation error",
                "errors" => $validator->errors()
            ]);
        }
        $checkExistAuthor = User::find($request->author_id);
        if (!$checkExistAuthor) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Can not find the corresponding author!',
            ]);
        }
        if ($request->has('image')) {
            $image = $request->file('image');
            $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/post-image/', $fileName);
            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'image' => $fileName,
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
                        'title' => 'string|min:2|max:255',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $postUpdate->title = $request->title;
                    $postUpdate->content = $request->content;
                    $postUpdate->date = $request->date;
                    $postUpdate->tags = $request->tags;
                    $postUpdate->is_valid_flag = $request->is_valid_flag;
                    $postUpdate->save();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Post updated successfully!',
                    ]);
                }
                if ($request->hasFile('image')) {
                    $validatorUpdate = Validator::make($request->all(), [
                        'title' => 'string|min:2|max:255',
                        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    ]);
                    if ($validatorUpdate->fails()) {
                        return response()->json([
                            "statusCode" => 400,
                            "message" => "Validation update error",
                            "errors" => $validatorUpdate->errors()
                        ]);
                    }
                    $destination = 'uploads/post-image/' . $postUpdate->image;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }
                    $image = $request->file('image');
                    $fileName = Str::random(5) . date('YmdHis') . '.' . $image->getClientOriginalExtension();
                    $image->move('uploads/post-image/', $fileName);
                    $postUpdate->title = $request->title;
                    $postUpdate->content = $request->content;
                    $postUpdate->date = $request->date;
                    $postUpdate->tags = $request->tags;
                    $postUpdate->image = $fileName;
                    $postUpdate->is_valid_flag = $request->is_valid_flag;
                    $postUpdate->save();
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
                $destination = 'uploads/post-image/' . $checkPost->image;
                if (File::exists($destination)) {
                    File::delete($destination);
                }
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
