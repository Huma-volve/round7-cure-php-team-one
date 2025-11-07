<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return success response
     */
    protected function successResponse(
        mixed $data = null,
        string $message = null,
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'status' => $statusCode,
            'message' => $message ? __($message) : __('messages.success'),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return error response
     */
    protected function errorResponse(
        string $message = null,
        int $statusCode = 500,
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'status' => $statusCode,
            'message' => $message ? __($message) : __('messages.error'),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return validation error response
     */
    protected function validationErrorResponse(
        array $errors,
        string $message = null
    ): JsonResponse {
        return $this->errorResponse($message ?: 'messages.validation_error', 422, $errors);
    }

    /**
     * Return unauthorized response
     */
    protected function unauthorizedResponse(
        string $message = null
    ): JsonResponse {
        return $this->errorResponse($message ?: 'messages.unauthorized', 403);
    }

    /**
     * Return not found response
     */
    protected function notFoundResponse(
        string $message = null
    ): JsonResponse {
        return $this->errorResponse($message ?: 'messages.not_found', 404);
    }

    /**
     * Return conflict response
     */
    protected function conflictResponse(
        string $message,
        mixed $additionalData = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'status' => 409,
            'message' => __($message),
        ];

        if ($additionalData !== null) {
            $response['data'] = $additionalData;
        }

        return response()->json($response, 409);
    }

    /**
     * Return server error response
     */
    protected function serverErrorResponse(
        string $message = null,
        string $error = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'status' => 500,
            'message' => $message ? __($message) : __('messages.server_error'),
        ];

        if ($error && app()->environment('local')) {
            $response['error'] = $error;
        }

        return response()->json($response, 500);
    }

    /**
     * Return paginated response
     */
    protected function paginatedResponse(
        mixed $data,
        string $message = null
    ): JsonResponse {
        return $this->successResponse($data, $message);
    }

    /**
     * Return created response
     */
    protected function createdResponse(
        mixed $data = null,
        string $message = null
    ): JsonResponse {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Return no content response
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }
}

