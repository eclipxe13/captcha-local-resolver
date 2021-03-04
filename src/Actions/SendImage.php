<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Actions;

use CaptchaLocalResolver\ActionInterface;
use CaptchaLocalResolver\Captchas;
use CaptchaLocalResolver\Exceptions\ExecuteException;
use JsonSerializable;

class SendImage implements ActionInterface
{
    private Captchas $captchas;

    public function __construct(Captchas $captchas)
    {
        $this->captchas = $captchas;
    }

    public function execute(array $arguments): JsonSerializable
    {
        // validate image
        $image = $arguments['image'] ?? '';
        if ('' === $image) {
            throw ExecuteException::invalidArgument('image');
        }
        if (base64_encode(base64_decode($image, true) ?: '') !== $image) {
            throw ExecuteException::invalidArgument('image');
        }

        return $this->captchas->push($image);
    }
}
