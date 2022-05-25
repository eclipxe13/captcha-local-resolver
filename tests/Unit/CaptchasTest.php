<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Tests\Unit;

use CaptchaLocalResolver\Captchas;
use CaptchaLocalResolver\Tests\SnoopSubscriptor;
use CaptchaLocalResolver\Tests\TestCase;
use LogicException;
use stdClass;

/**
 * @covers \CaptchaLocalResolver\Captchas
 */
class CaptchasTest extends TestCase
{
    public function testCreateInstance(): void
    {
        $captchas = new Captchas();
        $this->assertCount(0, $captchas);
    }

    public function testPushAndFindByCode(): void
    {
        $captchas = new Captchas();
        $subscriber = new SnoopSubscriptor();
        $captchas->subscribe($subscriber);

        $first = $captchas->push(base64_encode('image-1'));
        $second = $captchas->push(base64_encode('image-2'));

        $this->assertCount(2, $captchas);
        $this->assertSame([$first, $second], $subscriber->getEvents('append'));
        $this->assertSame(
            [$first->getCode() => $first, $second->getCode() => $second],
            iterator_to_array($captchas->getIterator())
        );

        $this->assertSame($first, $captchas->findByCode($first->getCode()));
        $this->assertSame($second, $captchas->findByCode($second->getCode()));
        $this->assertNull($captchas->findByCode('foobar'));
    }

    public function testFindByCodeOrFailThrowsAnException(): void
    {
        $captchas = new Captchas();

        $this->expectException(LogicException::class);
        $captchas->findByCodeOrFail('x-code');
    }

    public function testAnswer(): void
    {
        $captchas = new Captchas();
        $subscriber = new SnoopSubscriptor();
        $captchas->subscribe($subscriber);

        $first = $captchas->push(base64_encode('image-1'));
        $captchas->push(base64_encode('image-2'));

        $captchas->answer($first->getCode(), 'answer-1');
        $answered = $captchas->findByCodeOrFail($first->getCode());

        $this->assertCount(2, $captchas, 'It must have the same count of captchas after answer');

        $this->assertSame('answer-1', $answered->getAnswer(), 'Captcha after answer must contain the answer');
        $this->assertNotSame($first, $answered, 'Captchas before and after must be different instances');
        $this->assertSame($first->getCode(), $answered->getCode(), 'Captchas before and after must have same code');
        $this->assertSame($first->getImage(), $answered->getImage(), 'Captchas before and after must have same image');

        $this->assertSame($answered, $subscriber->getEvents('answer')[0]);
    }

    public function testAnswerUnexistentCode(): void
    {
        $captchas = new Captchas();
        $captchas->answer('x-code', 'x-answer');
        $subscriber = new SnoopSubscriptor();
        $captchas->subscribe($subscriber);
        $this->assertCount(0, $subscriber->getEvents('answer'), 'Answer unexistent code did not create any event');
        $this->assertCount(0, $captchas, 'Answer unexistent code did not increase the captchas');
    }

    public function testRemove(): void
    {
        $captchas = new Captchas();
        $subscriber = new SnoopSubscriptor();
        $captchas->subscribe($subscriber);
        $captchas->push(base64_encode('x-image-1'));
        $code = $captchas->push(base64_encode('x-image-2'))->getCode();
        $captchas->push(base64_encode('x-image-3'));

        $captchas->remove($code);
        $this->assertCount(1, $subscriber->getEvents('remove'), 'Remove captcha create an event');
        $this->assertCount(2, $captchas, 'Remove captcha reduce the count');
        $this->assertNull($captchas->findByCode($code), 'The removed captcha does not exist anymore');
    }

    public function testRemoveUnexistentCode(): void
    {
        $captchas = new Captchas();
        $subscriber = new SnoopSubscriptor();
        $captchas->subscribe($subscriber);
        $captchas->push(base64_encode('x-image-1'));
        $captchas->push(base64_encode('x-image-2'));

        $captchas->remove('x-code');
        $this->assertCount(0, $subscriber->getEvents('remove'), 'Remove unexistent code did not create any event');
        $this->assertCount(2, $captchas, 'Answer unexistent code did not reduce the captchas');
    }

    public function testJsonSerialize(): void
    {
        $captchas = new Captchas();
        $items = [
            $captchas->push(base64_encode('x-image-1'))->toArray(),
            $captchas->push(base64_encode('x-image-2'))->toArray(),
            $captchas->push(base64_encode('x-image-3'))->toArray(),
        ];

        $this->assertSame(
            json_encode($items),
            json_encode($captchas),
            'JsonSerialize produces an array of objects'
        );

        /** @var iterable<stdClass> $restored */
        $restored = json_decode(json_encode($items) ?: '', false);
        foreach ($restored as $item) {
            $this->assertInstanceOf(stdClass::class, $item);
        }
    }
}
