<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Success response
     */
    public static function success($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $code);
    }

    /**
     * Error response
     */
    public static function error($message = 'Error', $errors = null, $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
        ], $code);
    }

    /**
     * Paginated response
     */
    public static function paginated($paginator, $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }

    /**
     * Not found response
     */
    public static function notFound($message = 'Resource not found'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized($message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    /**
     * Validation error response
     */
    public static function validationError($errors, $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    /**
     * Server error response
     */
    public static function serverError($message = 'Internal server error'): JsonResponse
    {
        return self::error($message, null, 500);
    }
}
