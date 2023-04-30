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
    // Get all posts by category_id
    public function getAllPostsByCategoryId(Request $request)
    {
        if (!$request->category_id) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Missing category_id parameter!',
            ]);
        }
        $posts = Post::where('category_id', '=', $request->category_id)->get();
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
            'content' => 'string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'author_id' => 'required|numeric|integer',
            'date' => 'date_format:Y-m-d H:i:s',
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
                'is_valid' => false,
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
                        'content' => 'string|max:500',
                        'date' => 'date_format:Y-m-d H:i:s',
                        'is_valid' => 'integer|between:0,1'
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
                    $postUpdate->is_valid = $request->is_valid;
                    $postUpdate->save();
                    return response()->json([
                        'statusCode' => 200,
                        'message' => 'Post updated successfully!',
                    ]);
                }
                if ($request->hasFile('image')) {
                    $validatorUpdate = Validator::make($request->all(), [
                        'title' => 'string|min:2|max:255',
                        'content' => 'string|max:500',
                        'date' => 'date_format:Y-m-d H:i:s',
                        'is_valid' => 'integer|between:0,1',
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
                    $postUpdate->is_valid = $request->is_valid;
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
	    $sort   = $request->sort;
	    $filter = $request->filter;
	    $limit  = $request->limit ?? 10;
	    $page   = $request->page ?? 1;
	    $posts = Post::all();
	    if ( $filter ) {
		    $posts = $this->_filterPost( $posts, $filter );
	    }
	    if ( $sort ) {
		    foreach ( $sort as $sortArray ) {
			    $posts->orderBy( $sortArray['sort_by'], $sortArray['sort_dir'] );
		    }
	    }
	    return $posts->paginate( $limit, [ '*' ], 'page', $page );
    }

	private function _filterPost( &$posts, $filter ) {
		if ( isset( $filter['title'] ) ) {
			$posts->where( 'title', 'LIKE', '%' . $filter['title'] . '%' );
		}

		if ( isset( $filter['category_id'] ) ) {
			$posts->where( 'category_id', $filter['category_id'] );
		}

		if ( isset( $filter['is_valid'] ) ) {
			$posts->where( 'is_valid', $filter['is_valid'] );
		}
		return $posts;
	}
}
