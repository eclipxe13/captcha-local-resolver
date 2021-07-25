<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Exceptions;

use Exception;

class ExecuteException extends Exception
{
    public static function invalidArgument(string $argument, string $explanation = ''): self
    {
        $message = sprintf(
            'Invalid argument %s received%s',
            $argument,
            ($explanation) ? ": $explanation" : ''
        );
        return new self($message, 400);
    }

    public static function codeNotFound(string $code): self
    {
        return new self(sprintf('Code %s is not found', $code), 404);
    }

    public static function pathNotFound(string $path): self
    {
        return new self(sprintf('Requested path %s is not found', $path), 404);
    }
}
