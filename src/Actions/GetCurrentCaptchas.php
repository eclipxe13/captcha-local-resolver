<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Actions;

use CaptchaLocalResolver\ActionInterface;
use CaptchaLocalResolver\Captchas;
use JsonSerializable;

class GetCurrentCaptchas implements ActionInterface
{
    /** @var Captchas */
    private $repository;

    public function __construct(Captchas $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $arguments): JsonSerializable
    {
        return $this->repository;
    }
}
