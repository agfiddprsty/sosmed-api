<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class PostController extends BaseController
{
  public function index(): JsonResponse
  {
    try {
      $posts = Post::with(['user', 'comments.user', 'likes.user'])->get();
      return $this->sendResponse(PostResource::collection($posts), 'Success');
    } catch (\Throwable $th) {
      return $this->sendError($th->getMessage(), $th->getCode());
    }
  }

  public function store(Request $request)
  {
    try {
      $validated = Validator::make($request->all(), [
        'content' => 'required|string',
        'image' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
        'video' => 'nullable|string',
      ]);
      $uploadFolder = 'users';
      $image_uploaded_path = '';
      if ($request->file('image')) {
        $image = $request->file('image');
        $image_uploaded_path = $image->store($uploadFolder, 'public');
      }
      if ($validated->fails()) {
        return $this->sendError($validated->errors()->first() ?? "Validation error", $validated->errors());
      }
      $input = $request->all();
      $post = Post::create([
        'user_id' => auth()->id(),
        'content' => $input['content'],
        'image' => $image_uploaded_path != '' ? Storage::disk('public')->url($image_uploaded_path) : null,
        'video' => $input['video'] ?? null,
      ]);
      return response()->json($post);
    } catch (\Throwable $th) {
      return $this->sendError($th->getMessage(), $th->getCode());
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $post = Post::findOrFail($id);
      $validated = $request->validate([
        'content' => 'required|string',
        'image' => 'nullable|string',
        'video' => 'nullable|string',
      ]);
      $post->update($validated);
      return $this->sendResponse($post, 'Success');
    } catch (\Throwable $th) {
      return $this->sendError($th->getMessage(), $th->getCode());
    }
  }

  public function destroy($id)
  {
    try {
      $post = Post::findOrFail($id);
      $post->delete();
      return $this->sendResponse(null,'Success');
    } catch (\Throwable $th) {
      return $this->sendError($th->getMessage(), $th->getCode());
    }
  }

  public function like($id)
  {
    try {
      $is_liked = Like::where('user_id', auth()->id())->where('post_id', $id)->first();
      if ($is_liked) {
        $is_liked->delete();
        return $this->sendResponse(null, 'Success');
      } else {
        $like = Like::create(['post_id' => $id, 'user_id' => auth()->id()]);
        return $this->sendResponse($like, 'Success');
      }
    } catch (\Throwable $th) {
      return $this->sendError($th->getMessage(), $th->getCode());
    }
  }

}
