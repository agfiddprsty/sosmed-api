<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'content' => $this->content,
      'image' => $this->image,
      'video' => $this->video,
      'created_at' => $this->created_at,
      'updated_at'=> $this->updated_at,
      'user' => $this->user,
      'comments' => $this->comments,
      'likes' => $this->likes,
      'likes_count' => count($this->likes),
    ];
  }
}
