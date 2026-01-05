<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse(mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json($data, $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $statusCode);
    }
}
