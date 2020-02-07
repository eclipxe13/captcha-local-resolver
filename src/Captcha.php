<?php

declare(strict_types=1);

namespace CaptchaLocalResolver;

use JsonSerializable;

class Captcha implements JsonSerializable
{
    /** @var string */
    private $code;

    /** @var string */
    private $image;

    /** @var string */
    private $answer;

    public function __construct(string $code, string $image, string $answer = '')
    {
        $this->code = $code;
        $this->image = $image;
        $this->answer = $answer;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function hasAnswer(): bool
    {
        return ('' !== $this->answer);
    }

    public function withAnswer(string $answer): self
    {
        $cloned = clone $this;
        $cloned->answer = $answer;
        return $cloned;
    }

    /** @return array<string, string> */
    public function jsonSerialize(): array
    {
        return array_filter([
            'code' => $this->code,
            'answer' => $this->answer,
        ]);
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this));
    }
}
