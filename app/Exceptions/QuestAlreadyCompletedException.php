<?php

namespace App\Exceptions;

use Exception;

class QuestAlreadyCompletedException extends Exception
{
    public function __construct($message = "This quest has already been completed by the user.", $code = 400)
    {
        parent::__construct($message, $code);
    }
}
