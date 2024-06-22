<?php

namespace App\Traits;

trait ApiResponses
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

    protected function error($errors = [], $statusCode = null)
    {
        if (is_string($errors)) {
            return response()->json([
                'message' => $errors,
                'status' => $statusCode
            ], $statusCode);
        }
        return response()->json([
            'errors' => $errors
        ]);
    }

    protected function notAuthorized($message)
    {
        return $this->error([
            'status' => 401,
            'message' => $message,
            'source' => ''
        ]);
    }
}
