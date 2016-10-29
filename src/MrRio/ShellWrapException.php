<?php

namespace MrRio;

use Exception;

class ShellWrapException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        $error = "[{$this->code}]: {$this->message}\n";

        if (defined('SHELL_WRAP_INTERACTIVE')) {
            echo $error;
        } else {
            return $error;
        }
    }
}
