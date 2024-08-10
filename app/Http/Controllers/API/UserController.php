<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function update(Request $request, $id)
  {
    $user = User::findOrFail($id);
    $user->update($request->all());
    return response()->json($user);
  }
}
