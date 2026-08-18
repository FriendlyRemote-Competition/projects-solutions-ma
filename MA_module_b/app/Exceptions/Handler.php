<?php

protected function invalidJson($request, \Illuminate\Validation\ValidationException $exception)
{
    return response()->json([
        'message' => 'Validation failed',
        'errors' => $exception->errors(),
    ], 422);
}