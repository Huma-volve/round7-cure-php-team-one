<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use App\Helpers\ApiResponse;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {});
    }

    public function render($request, Throwable $exception)
    {
        // Validation errors
        if ($exception instanceof ValidationException) {
            return ApiResponse::error($exception->errors(), 'Validation error', 422);
        }

        // Authentication errors
        if ($exception instanceof AuthenticationException) {
            return ApiResponse::error(null, 'Unauthenticated', 401);
        }

        // Model not found
        if ($exception instanceof ModelNotFoundException) {
            return ApiResponse::error(null, 'Resource not found', 404);
        }

        // Route not found
        if ($exception instanceof NotFoundHttpException) {
            return ApiResponse::error(null, 'Endpoint not found', 404);
        }

        // Wrong HTTP method
        // if ($exception instanceof MethodNotAllowedHttpException) {
        //     return ApiResponse::error(null, 'Method not allowed', 405);
        // }

        // Catch-all for any other errors (JSON)
        if ($request->expectsJson()) {
            return ApiResponse::error(null, $exception->getMessage() ?: 'Something went wrong', 500);
        }

        return parent::render($request, $exception);
    }
}
