<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class CustomExceptionHandler extends ExceptionHandler
{
    public function render($request, Exception|UserException|\Throwable $exception)
    {
        if ($exception instanceof QueryException) {
            return response()->json(['error' => 'Database error: ' . $exception->getMessage()], $exception->getCode());
        }
        if ($exception instanceof UserException) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
        else {
            return response()->json(['Server error message' => $exception->getMessage()], 500);
        }
    }
}
