<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Tests;

use Psr\Http\Message\ServerRequestInterface;
use RingCentral\Psr7\ServerRequest;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $method
     * @param string $uri
     * @param array<mixed>|object|null $parsedBody
     * @return ServerRequestInterface
     */
    protected function createRequest(string $method, string $uri, $parsedBody = []): ServerRequestInterface
    {
        $request = new ServerRequest($method, $uri);
        $request = $request->withParsedBody($parsedBody);
        return $request;
    }
}
