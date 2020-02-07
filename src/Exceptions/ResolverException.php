<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Exceptions;

use Exception;

class ResolverException extends Exception
{
    public static function unsupportedMethod(string $method, string $path): self
    {
        return new self(sprintf('Method %s for %s is not supported', $method, $path), 405);
    }

    public static function unknownAction(string $method, string $path): self
    {
        return new self(sprintf('Cannot determine action for [%s] %s', $method, $path), 404);
    }
}
