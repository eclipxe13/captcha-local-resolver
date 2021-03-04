<?php

declare(strict_types=1);

namespace CaptchaLocalResolver;

class ActionResolver
{
    private string $method;

    private string $path;

    private string $webroot;

    public function __construct(string $method, string $path, string $webroot)
    {
        $this->method = $method;
        $this->path = ('/' === $path) ? '/index.html' : $path;
        $this->webroot = $webroot;
    }

    /**
     * @param Application $app
     * @return ActionInterface
     * @throws Exceptions\ResolverException
     */
    public function resolve(Application $app): ActionInterface
    {
        if ($this->checkPathMethod('GET', '/captchas')) {
            return new Actions\GetCurrentCaptchas($app->getRepository());
        }
        if ($this->checkPathMethod('GET', '/events')) {
            return new Actions\Events($app->getRepository());
        }
        if ($this->checkPathMethod('POST', '/set-code-answer')) {
            return new Actions\SetCodeAnswer($app->getRepository());
        }
        if ($this->checkPathMethod('POST', '/send-image')) {
            return new Actions\SendImage($app->getRepository());
        }
        if ($this->checkPathMethod('POST', '/obtain-decoded')) {
            return new Actions\ObtainDecoded($app->getRepository());
        }
        if ($this->checkPathMethod('GET', '/index.html') || 'GET' === $this->method) {
            return new Actions\LocalResource($this->webroot, $this->path);
        }

        throw Exceptions\ResolverException::unknownAction($this->method, $this->path);
    }

    /**
     * @param string $expectedMethod
     * @param string $expectedPath
     * @return bool
     * @throws Exceptions\ResolverException
     */
    private function checkPathMethod(string $expectedMethod, string $expectedPath): bool
    {
        if ($expectedPath !== $this->path) {
            return false;
        }
        if ($expectedMethod !== $this->method) {
            throw Exceptions\ResolverException::unsupportedMethod($this->path, $this->method);
        }
        return true;
    }
}
