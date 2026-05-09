<?php

namespace App\Http\Controllers\Traits;

trait ApiResponses
{
    protected function successJson(mixed $data = null, string $message = '', int $code = 200)
    {
        return response()->json([ 'success' => true, 'message' => $message, 'data' => $data ], $code);
    }

    protected function errorJson(string $message = 'An error occurred', int $code = 400, mixed $data = null)
    {
        return response()->json([ 'success' => false, 'message' => $message, 'data' => $data ], $code);
    }
}
