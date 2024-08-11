<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class UserController extends BaseController
{
  public function update(Request $request, $id)
  {
    try {
      $user = User::findOrFail($id);
      $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'password' => 'required|string|min:8',
        'image' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
        'bio' => 'nullable|string',
        'interests' => 'nullable|string'
      ]);
      $uploadFolder = 'users';
      $image_uploaded_path = '';
      if ($request->file('image')) {
        $image = $request->file('image');
        $image_uploaded_path = $image->store($uploadFolder, 'public');
      }
      
      if ($validator->fails()) {
        return $this->sendError($validator->errors()->first() ?? "Validation error", $validator->errors());
      }
      $input = $request->all();
      $user->update([
        "name"=> $input['name'],
        "password"=> $input['password'],
        "image"=> $image_uploaded_path != '' ? Storage::disk('public')->url($image_uploaded_path) : $user['image'],
        'bio'=> $input['bio'] ?? $user['bio'],
        'interests'=> $input['interests'] ?? $user['interests'],
      ]);
      $success['user'] = $user;
      return $this->sendResponse($success, 'Success');
    } catch (\Throwable $th) {
      return $this->sendError($th->getMessage(), $th->getCode());
    }
  }
}
