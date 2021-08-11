<?php

namespace App\Response;

class Response
{
    public static function success($message)
    {
        return response()->json([
            'message' => $message,
            'status' => 'success'
        ]);
    }

    public static function error($message, $code = 500)
    {
        return response()->json([
            'message' => $message,
            'status' => 'error'
        ], $code);
    }

    public static function unauthorized()
    {
        return static::error('unauthorized user', 401);
    }

    public static function invalidField()
    {
        return static::error('invalid field', 422);
    }

    public static function withData($data, $message = null)
    {
        $response = [
            'data' => $data,
            'message' => $message,
        ];
        if (!$message) unset($response['message']);

        return response()->json($response);
    }
}
