<?php

namespace App\Exceptions;

use Exception;

class InvalidDataException extends Exception
{
    public static function withMessage(?string $message = 'Invalid Data')
    {
        $exception = app(InvalidDataException::class);
        $exception->message = $message;

        return $exception;
    }
}
