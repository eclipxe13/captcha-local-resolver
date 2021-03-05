<?php

declare(strict_types=1);

namespace CaptchaLocalResolver;

use ArrayObject;
use Closure;
use Countable;
use LogicException;
use Throwable;

class Subscriptors implements SubscriptorInterface, Countable
{
    /** @var ArrayObject<int, SubscriptorInterface>|SubscriptorInterface[] */
    private ArrayObject $subscriptors;

    public function __construct()
    {
        $this->subscriptors = new ArrayObject();
    }

    public function subscribe(SubscriptorInterface $subscriptor): void
    {
        if ($this === $subscriptor) {
            throw new LogicException('You are creating an infinite loop by adding the subscriptors collection to itself');
        }
        $this->subscriptors->append($subscriptor);
    }

    public function onAppend(Captcha $captcha): void
    {
        $this->eachSubscriptor(
            function (SubscriptorInterface $subscriptor) use ($captcha): void {
                $subscriptor->onAppend($captcha);
            }
        );
    }

    public function onRemove(Captcha $captcha): void
    {
        $this->eachSubscriptor(
            function (SubscriptorInterface $subscriptor) use ($captcha): void {
                $subscriptor->onRemove($captcha);
            }
        );
    }

    public function onAnswer(Captcha $captcha): void
    {
        $this->eachSubscriptor(
            function (SubscriptorInterface $subscriptor) use ($captcha): void {
                $subscriptor->onAnswer($captcha);
            }
        );
    }

    private function eachSubscriptor(Closure $function): void
    {
        /** @var SubscriptorInterface $subscriptor */
        foreach ($this->subscriptors as $subscriptor) {
            try {
                // do not generate any error!
                $function($subscriptor);
            } catch (Throwable $exception) {
                unset($exception);
            }
        }
    }

    public function count(): int
    {
        return $this->subscriptors->count();
    }
}
