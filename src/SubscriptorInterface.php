<?php

declare(strict_types=1);

namespace CaptchaLocalResolver;

interface SubscriptorInterface
{
    public function onAppend(Captcha $captcha): void;

    public function onRemove(Captcha $captcha): void;

    public function onAnswer(Captcha $captcha): void;
}
