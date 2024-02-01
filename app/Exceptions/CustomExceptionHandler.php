<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class CustomExceptionHandler extends ExceptionHandler
{
//    public function render($request, Exception|UserException|\Throwable $exception)
//    {
//        if ($exception instanceof QueryException) {
//            return response()->json(['error' => 'Database error: ' . $exception->getMessage()], 500);
//        }
//        if ($exception instanceof UserException) {
//            return response()->json(['error' => 'Server-error message: ' . $exception->getMessage()], 500);
//        }
//        else {
//            return response()->json(['error' => $exception->getMessage()], 500);
//        }
//    }
}
