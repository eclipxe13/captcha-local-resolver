<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Actions;

use CaptchaLocalResolver\ActionInterface;
use CaptchaLocalResolver\Captcha;
use CaptchaLocalResolver\Captchas;
use CaptchaLocalResolver\SubscriptorInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Response;
use React\Stream\ThroughStream;

class Events implements ActionInterface, SubscriptorInterface
{
    /** @var Captchas */
    private $captchas;

    /** @var ThroughStream */
    private $stream;

    public function __construct(Captchas $captchas)
    {
        $this->captchas = $captchas;
        $this->stream = new ThroughStream();
        $this->captchas->subscribe($this);
    }

    public function execute(array $arguments): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'text/event-stream; charset=utf-8', 'Cache-Control' => 'no-cache'], $this->stream);
    }

    public function onAppend(Captcha $captcha): void
    {
        $this->writeToStream(['eventName' => 'append', 'code' => $captcha->getCode(), 'image' => $captcha->getImage()]);
    }

    public function onAnswer(Captcha $captcha): void
    {
        $this->writeToStream(['eventName' => 'answer', 'code' => $captcha->getCode(), 'answer' => $captcha->getAnswer()]);
    }

    public function onRemove(Captcha $captcha): void
    {
        $this->writeToStream(['eventName' => 'remove', 'code' => $captcha->getCode()]);
    }

    /**
     * @param array<string, string> $data
     */
    public function writeToStream(array $data): void
    {
        $message = json_encode($data + ['on' => date('c')], JSON_UNESCAPED_SLASHES);
        // $message = implode(PHP_EOL . 'data: ', explode(PHP_EOL, $message));
        // $message = str_replace(PHP_EOL, PHP_EOL . 'data: ', $message);
        $this->stream->write('data: ' . $message . PHP_EOL . PHP_EOL);
    }
}
