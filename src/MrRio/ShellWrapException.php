<?php

declare(strict_types=1);

namespace MrRio;

use Exception;
use Throwable;

class ShellWrapException extends Exception
{
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        $error = "[{$this->code}]: {$this->message}\n";

        if (defined('SHELL_WRAP_INTERACTIVE')) {
            echo $error;
            return '';
        }

        return $error;
    }
}
