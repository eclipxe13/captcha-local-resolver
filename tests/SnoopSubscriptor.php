<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Tests;

use CaptchaLocalResolver\Captcha;
use CaptchaLocalResolver\SubscriptorInterface;

class SnoopSubscriptor implements SubscriptorInterface
{
    /** @var array<string, array<Captcha>> */
    private $events;

    public function onAppend(Captcha $captcha): void
    {
        $this->log('append', $captcha);
    }

    public function onRemove(Captcha $captcha): void
    {
        $this->log('remove', $captcha);
    }

    public function onAnswer(Captcha $captcha): void
    {
        $this->log('answer', $captcha);
    }

    public function log(string $eventName, Captcha $captcha): void
    {
        $this->events[$eventName][] = $captcha;
    }

    /**
     * @param string $eventName
     * @return array<Captcha>|Captcha[]
     */
    public function getEvents(string $eventName): array
    {
        return $this->events[$eventName] ?? [];
    }

    public function countAll(): int
    {
        return array_reduce(
            $this->events,
            function (int $carry, array $events): int {
                return $carry + count($events);
            },
            0
        );
    }
}
