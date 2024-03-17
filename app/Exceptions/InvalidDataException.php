<?php

namespace App\Exceptions;

use Exception;

class InvalidDataException extends Exception
{
    public function __construct($message = "Invalid data provided", $code = 0, Exception $previous = null)
    {
        // Вызов конструктора базового класса
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
