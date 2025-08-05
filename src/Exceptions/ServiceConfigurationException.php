<?php

namespace Ebilet\Common\Exceptions;

use Exception;

class ServiceConfigurationException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 