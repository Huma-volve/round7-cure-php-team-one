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
        string $message = 'تمت العملية بنجاح',
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'status' => $statusCode,
            'message' => $message,
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
        string $message = 'حدث خطأ',
        int $statusCode = 500,
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'status' => $statusCode,
            'message' => $message,
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
        string $message = 'البيانات المرسلة غير صحيحة'
    ): JsonResponse {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Return unauthorized response
     */
    protected function unauthorizedResponse(
        string $message = 'غير مصرح لك بالوصول'
    ): JsonResponse {
        return $this->errorResponse($message, 403);
    }

    /**
     * Return not found response
     */
    protected function notFoundResponse(
        string $message = 'لم يتم العثور على البيانات'
    ): JsonResponse {
        return $this->errorResponse($message, 404);
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
            'message' => $message,
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
        string $message = 'حدث خطأ في السيرفر',
        string $error = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'status' => 500,
            'message' => $message,
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
        string $message = 'تم جلب البيانات بنجاح'
    ): JsonResponse {
        return $this->successResponse($data, $message);
    }

    /**
     * Return created response
     */
    protected function createdResponse(
        mixed $data = null,
        string $message = 'تم الإنشاء بنجاح'
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

