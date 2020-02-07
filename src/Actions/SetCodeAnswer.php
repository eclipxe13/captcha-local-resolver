<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Actions;

use CaptchaLocalResolver\ActionInterface;
use CaptchaLocalResolver\Captchas;
use CaptchaLocalResolver\Exceptions\ExecuteException;
use Psr\Http\Message\ResponseInterface;
use React\Http\Response;

class SetCodeAnswer implements ActionInterface
{
    /** @var Captchas */
    private $captchas;

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
        $answer = $arguments['answer'] ?? '';
        if ('' === $answer) {
            throw ExecuteException::invalidArgument('answer');
        }

        if (null === $this->captchas->findByCode($code)) {
            throw ExecuteException::codeNotFound($code);
        }
        $this->captchas->answer($code, $answer);
        return new Response(200, ['Content-Type' => 'text/plain; charset=utf-8'], '');
    }
}
