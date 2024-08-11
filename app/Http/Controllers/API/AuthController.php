<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

class AuthController extends BaseController
{
  public function register(Request $request): JsonResponse
  {
    try {
      $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'image' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
        'bio' => 'nullable|string',
        'interests' => 'nullable|string'
      ]);
      $uploadFolder = 'users';
      if ($request->file('image')) {
        $image = $request->file('image');
        $image_uploaded_path = $image->store($uploadFolder, 'public');
      }
      
      if ($validator->fails()) {
        return $this->sendError($validator->errors()->first() ?? "Validation error", $validator->errors());
      }
  
      $input = $request->all();
      $user = User::create([
        'name'=> $input['name'],
        'email'=> $input['email'],
        'password'=> bcrypt($input['password']),
        'image'=> Storage::disk('public')->url($image_uploaded_path) ?? null,
        'bio' => $input['bio'] ?? null,
        'interests'=> $input['interests'] ?? null,
      ]);
      $success['token'] = $user->createToken('auth_token')->plainTextToken;
      $success['user'] = $user;
  
      return $this->sendResponse($success, 'Success');  
    } catch (\Throwable $th) {
      return $this->sendError($th->getMessage(), $th->getCode());
    }
  }

  /**
   * Login api
   *
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request): JsonResponse
  {
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
      $user = Auth::user();
      $success['token'] = $user->createToken('auth_token')->plainTextToken;
      $success['user'] = $user;

      return $this->sendResponse($success, 'Success');
    } else {
      $message = 'Unauthorised';
      return $this->sendError($message, ['error' => 'Unauthorized']);
    }
  }

}
