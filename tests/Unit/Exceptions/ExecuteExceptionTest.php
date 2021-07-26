<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Tests\Unit\Exceptions;

use CaptchaLocalResolver\Exceptions\ExecuteException;
use CaptchaLocalResolver\Tests\TestCase;

final class ExecuteExceptionTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $exception = ExecuteException::invalidArgument('x-argument');
        $this->assertSame('Invalid argument x-argument received', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
    }

    public function testInvalidArgumentWithExplanation(): void
    {
        $exception = ExecuteException::invalidArgument('x-argument', 'x-explanation');
        $this->assertSame('Invalid argument x-argument received: x-explanation', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
    }

    public function testCodeNotFound(): void
    {
        $exception = ExecuteException::codeNotFound('x-code');
        $this->assertSame('Code x-code is not found', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
    }

    public function testPathNotFound(): void
    {
        $exception = ExecuteException::pathNotFound('x-path');
        $this->assertSame('Requested path x-path is not found', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
    }
}
