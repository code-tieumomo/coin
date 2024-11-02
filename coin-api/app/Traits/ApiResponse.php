<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    private function response($data = null, $message = null, $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    protected function success($data = null, $message = null, $status = 200): JsonResponse
    {
        is_null($message) && $message = 'Success';

        return $this->response($data, $message, $status);
    }

    protected function error($message = null, $status = 400): JsonResponse
    {
        is_null($message) && $message = 'Oops! Something went wrong';

        return $this->response(null, $message, $status);
    }
}
