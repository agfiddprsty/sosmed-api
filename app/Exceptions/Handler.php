<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
  /**
   * The list of the exception types that are not reported.
   *
   * @var array<int, class-string<Throwable>>
   */
  protected $dontReport = [
    //
  ];

  /**
   * The list of the inputs that are never flashed to the session on validation exceptions.
   *
   * @var array<int, string>
   */
  protected $dontFlash = [
    'current_password',
    'password',
    'password_confirmation',
  ];

  /**
   * Register the exception handling callbacks for the application.
   */
  // public function register(): void
  // {
  //   $this->reportable(function (Throwable $e) {
  //     //
  //   });
  // }

  /**
   * Convert an authentication exception into an unauthenticated response.
   */
  protected function unauthenticated($request, AuthenticationException $exception)
  {
    return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
  }
}
