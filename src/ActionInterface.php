<?php

declare(strict_types=1);

namespace CaptchaLocalResolver;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;

interface ActionInterface
{
    /**
     * @param string[] $arguments
     * @return ResponseInterface|JsonSerializable
     * @throws Exceptions\ExecuteException
     */
    public function execute(array $arguments);
}
