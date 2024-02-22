<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    public function render($request)
    {
        return $request->message();
    }}
