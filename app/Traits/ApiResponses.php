<?php

namespace App\Traits;

Trait ApiResponses
{
    protected function ok($message)
    {
        return $this->success($message, 200);
    }

    protected function success ($message, $statusCode)
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
    
}
