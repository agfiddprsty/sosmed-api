<?php

namespace App\Http\Controllers\API;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends BaseController
{
  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'post_id' => 'required|integer',
          'content' => 'required|string',
      ]);
      $comment = Comment::create([
        'post_id'=> $validated['post_id'],
        'content'=> $validated['content'],
        'user_id'=> auth()->id(),
      ]);
      return $this->sendResponse($comment, 'Success comment');
    } catch (\Throwable $th) {
      return $this->sendError($th->getMessage(), $th->getCode());
    }
  }

  public function reply(Request $request, $commentId)
  {
    $reply = Comment::create([
      'post_id' => $request->post_id,
      'user_id' => auth()->id(),
      'comment' => $request->comment,
      'parent_id' => $commentId,
    ]);
    return response()->json($reply);
  }
}
