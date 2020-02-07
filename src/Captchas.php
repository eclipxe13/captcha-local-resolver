<?php

declare(strict_types=1);

namespace CaptchaLocalResolver;

use ArrayObject;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use LogicException;
use Traversable;

/**
 * @implements IteratorAggregate<Captcha>
 */
class Captchas implements JsonSerializable, IteratorAggregate, Countable
{
    /** @var ArrayObject<string, Captcha>|Captcha[] */
    private $captchas;

    /** @var Subscriptors */
    private $subscriptors;

    public function __construct()
    {
        $this->captchas = new ArrayObject();
        $this->subscriptors = new Subscriptors();
    }

    public function push(string $image): Captcha
    {
        $code = md5($image);
        $captcha = new Captcha($code, $image);
        $this->captchas[$code] = $captcha;
        $this->subscriptors->onAppend($captcha);
        return $captcha;
    }

    public function findByCode(string $code): ?Captcha
    {
        return $this->captchas[$code] ?? null;
    }

    public function findByCodeOrFail(string $code): Captcha
    {
        $captcha = $this->findByCode($code);
        if (null === $captcha) {
            throw new LogicException(sprintf('Captcha with code %s does not exists', $code));
        }
        return $captcha;
    }

    public function remove(string $code): void
    {
        if (! isset($this->captchas[$code])) {
            return;
        }
        $captcha = $this->captchas[$code];
        unset($this->captchas[$code]);
        $this->subscriptors->onRemove($captcha);
    }

    public function answer(string $code, string $answer): void
    {
        if (! isset($this->captchas[$code])) {
            return;
        }
        $captcha = $this->captchas[$code]->withAnswer($answer);
        $this->captchas[$code] = $captcha;
        $this->subscriptors->onAnswer($captcha);
    }

    public function subscribe(SubscriptorInterface $subscriptor): void
    {
        $this->subscriptors->subscribe($subscriptor);
    }

    /**
     * @return array<array<string, string>>
     */
    public function jsonSerialize(): array
    {
        // convert captcha to array, remove codes as keys
        return array_values(
            array_map(
                function (Captcha $captcha): array {
                    return $captcha->toArray();
                },
                $this->captchas->getArrayCopy()
            )
        );
    }

    public function count(): int
    {
        return $this->captchas->count();
    }

    /** @return Traversable<Captcha>|Captcha[] */
    public function getIterator(): Traversable
    {
        return $this->captchas->getIterator();
    }
}
