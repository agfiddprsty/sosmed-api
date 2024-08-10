<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware("auth:sanctum")->group(function () {
  Route::prefix('user')->group(function () {
    Route::post('/{id}/update', [UserController::class, 'update']);
  });

  Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::post('/create', [PostController::class, 'store']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
    Route::post('/{id}/like', [PostController::class, 'like']);
  });
  Route::prefix('comments')->group(function () {
    Route::post('/', [CommentController::class, 'store']);
    Route::post('/{commentId}/reply', [CommentController::class, 'reply']);
  });
});