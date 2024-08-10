<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends BaseController
{
  public function register(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
      return $this->sendError($validator->errors()->first() ?? "Validation error", $validator->errors());
    }

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);
    $success['token'] = $user->createToken('auth_token')->plainTextToken;
    $success['user'] = $user;

    return $this->sendResponse($success, 'User register successfully.');
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

      return $this->sendResponse($success, 'User login successfully.');
    } else {
      $message = 'Unauthorised';
      return $this->sendError($message, ['error' => 'Unauthorized']);
    }
  }

}
