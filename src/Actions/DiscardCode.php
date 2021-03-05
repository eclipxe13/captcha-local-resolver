<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Actions;

use CaptchaLocalResolver\ActionInterface;
use CaptchaLocalResolver\Captchas;
use CaptchaLocalResolver\Exceptions\ExecuteException;
use Psr\Http\Message\ResponseInterface;
use React\Http\Response;

class DiscardCode implements ActionInterface
{
    private Captchas $captchas;

    public function __construct(Captchas $captchas)
    {
        $this->captchas = $captchas;
    }

    public function execute(array $arguments): ResponseInterface
    {
        // validate image
        $code = $arguments['code'] ?? '';
        if ('' === $code) {
            throw ExecuteException::invalidArgument('code');
        }

        if (null !== $this->captchas->findByCode($code)) {
            $this->captchas->remove($code);
        }

        return new Response(200, ['Content-Type' => 'text/plain; charset=utf-8'], '');
    }
}
