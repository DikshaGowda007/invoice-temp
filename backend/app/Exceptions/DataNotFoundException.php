<?php

namespace App\Exceptions;

use Exception;

class DataNotFoundException extends Exception
{
    public static function withMessage(?string $msg = 'Data Not Found')
    {
        $exception = app(DataNotFoundException::class);
        $exception->message = $msg;

        return $exception;
    }
}
