<?php

declare(strict_types=1);

namespace CaptchaLocalResolver;

use JsonException;
use JsonSerializable;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Throwable;

class Application
{
    private Captchas $repository;

    private string $webroot;

    public function __construct(?Captchas $repository = null, string $webroot = '')
    {
        $this->repository = $repository ?? new Captchas();
        $this->webroot = $webroot ?: __DIR__ . '/../public/';
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return $this->serve($request);
        } catch (Throwable $exception) {
            return new Response(
                500,
                ['Content-Type' => 'text/plain; charset=utf-8'],
                implode(PHP_EOL, [
                    sprintf('ERROR: (%s) %s', get_class($exception), $exception->getMessage()),
                    sprintf('-- %s:%s', $exception->getFile(), $exception->getLine()),
                    $exception->getTraceAsString(),
                ])
            );
        }
    }

    /**
     * This method is acting like a front controller:
     * - resolve request to action
     * - execute action & get a result
     * - transform result to a response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function serve(ServerRequestInterface $request): ResponseInterface
    {
        // route request to action
        try {
            $action = $this->createActionResolver($request)->resolve($this);
        } catch (Exceptions\ResolverException $exception) {
            return new Response($exception->getCode(), ['Content-Type' => 'text/plain; charset=utf-8'], $exception->getMessage());
        }

        // build arguments
        $arguments = $this->extractArgumentsFromRequest($request);

        // execute action
        try {
            /**
             * Redeclared type (add mixed) if action was poorly implemented
             * Static analysis will claim that this method has unreachable code
             * @var ResponseInterface|JsonSerializable|mixed $result
             */
            $result = $action->execute($arguments);
        } catch (Exceptions\ExecuteException $exception) {
            return new Response($exception->getCode(), ['Content-Type' => 'text/plain; charset=utf-8'], $exception->getMessage());
        }

        // transform action result to a response
        if ($result instanceof JsonSerializable) {
            $result = new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS) ?: ''
            );
        }
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        throw new LogicException(sprintf('Unable to create a response for the result on action %s', get_class($action)));
    }

    public function getRepository(): Captchas
    {
        return $this->repository;
    }

    public function getWebroot(): string
    {
        return $this->webroot;
    }

    protected function createActionResolver(ServerRequestInterface $request): ActionResolver
    {
        return new ActionResolver($request->getMethod(), $request->getUri()->getPath(), $this->webroot);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string[]
     */
    protected function extractArgumentsFromRequest(ServerRequestInterface $request): array
    {
        if ('application/json' === implode('', $request->getHeader('Content-Type'))) {
            try {
                $arguments = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                $arguments = null;
            }
        } else {
            $arguments = $request->getParsedBody();
        }
        if (is_object($arguments)) {
            /** @var array<mixed> $arguments */
            $arguments = json_decode(json_encode($arguments) ?: '', true);
        }
        if (! is_array($arguments)) {
            $arguments = [];
        }
        foreach ($arguments as $key => $value) {
            if (! is_scalar($value)) {
                unset($arguments[$key]);
                continue;
            }
            if (! is_string($value)) {
                $arguments[$key] = (string) $value;
            }
        }
        return $arguments;
    }
}
