<?php

namespace Packetery\Domain;

class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * InvalidArgumentException constructor.
     * @param string|\Throwable $message
     * @param int               $code
     * @param \Throwable|null   $previous
     */
    public function __construct($message = "", $code = 0, $previous = null)
    {
        if ($message instanceof \Exception) {
            $e = $message;
            $message = $e->getMessage();
            $code = $e->getCode();
            $previous = $e;
        }
        parent::__construct($message, $code, $previous);
    }
}

class InvalidStateException extends \Exception {}

class JsonException extends \Exception {}
