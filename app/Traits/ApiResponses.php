<?php

namespace App\Traits;

Trait ApiResponses
{
    protected function ok($message, $data = [])
    {
        return $this->success($data, $message, 200);
    }

    protected function success($data = [], $message, $statusCode)
    {
        return response()->json([
            'data' => $data, // data parameter added for showing the fetched data
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }

    protected function error($message, $statusCode)
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
    
}
