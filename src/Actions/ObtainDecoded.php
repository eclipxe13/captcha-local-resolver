<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Actions;

use CaptchaLocalResolver\ActionInterface;
use CaptchaLocalResolver\Captchas;
use CaptchaLocalResolver\Exceptions\ExecuteException;
use JsonSerializable;

class ObtainDecoded implements ActionInterface
{
    private Captchas $captchas;

    public function __construct(Captchas $captchas)
    {
        $this->captchas = $captchas;
    }

    public function execute(array $arguments): JsonSerializable
    {
        // validate code
        $code = $arguments['code'] ?? '';
        if ('' === $code) {
            throw ExecuteException::invalidArgument('code');
        }
        $captcha = $this->captchas->findByCode($code);
        if (null === $captcha) {
            throw ExecuteException::codeNotFound($code);
        }
        if ($captcha->hasAnswer()) {
            $this->captchas->remove($code);
        }
        return $captcha;
    }
}
