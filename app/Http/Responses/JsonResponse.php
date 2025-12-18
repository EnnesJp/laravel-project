<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse as BaseJsonResponse;

class JsonResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ): BaseJsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    public static function error(
        string $message = 'Error',
        mixed $errors = null,
        int $statusCode = 400
    ): BaseJsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }

    public static function created(
        mixed $data = null,
        string $message = 'Resource created successfully'
    ): BaseJsonResponse {
        return self::success($data, $message, 201);
    }

    public static function validationError(
        mixed $errors,
        string $message = 'Validation failed'
    ): BaseJsonResponse {
        return self::error($message, $errors, 422);
    }

    public static function notFound(
        string $message = 'Resource not found'
    ): BaseJsonResponse {
        return self::error($message, null, 404);
    }

    public static function unauthorized(
        string $message = 'Unauthorized'
    ): BaseJsonResponse {
        return self::error($message, null, 401);
    }

    public static function forbidden(
        string $message = 'Forbidden'
    ): BaseJsonResponse {
        return self::error($message, null, 403);
    }
}
