<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Tests\Integration;

use CaptchaLocalResolver\Application;
use CaptchaLocalResolver\Captcha;
use CaptchaLocalResolver\Tests\SnoopSubscriptor;

class SimulateFlowTest extends TestCase
{
    public function testSimulateFlow(): void
    {
        $subscriptor = new SnoopSubscriptor();
        $application = new Application();
        $captchas = $application->getRepository();
        $captchas->subscribe($subscriptor);

        // send image
        $encodedImage = base64_encode('image-1');
        $sendImageResponse = $application->__invoke(
            $this->createRequest('POST', 'http://localhost/send-image', ['image' => $encodedImage])
        );
        // send image assertions
        $this->assertSame(200, $sendImageResponse->getStatusCode());
        $sendImageData = json_decode($sendImageResponse->getBody()->getContents(), true);
        $code = $sendImageData['code'] ?: '';
        $this->assertNotEmpty($code, 'The captcha code was received on the response');
        $this->assertCount(1, $subscriptor->getEvents('append'));
        // send image event assertions
        $captchaFromEvent = $subscriptor->getEvents('append')[0];
        $this->assertEquals(new Captcha($code, $encodedImage), $captchaFromEvent);

        // obtain-decoded (not answer yet)
        $obtainDecodedNotAnswer = $application->__invoke(
            $this->createRequest('POST', 'http://localhost/obtain-decoded', ['code' => $code])
        );
        $this->assertSame(200, $obtainDecodedNotAnswer->getStatusCode());
        $obtainDecodedNotAnswerData = json_decode($obtainDecodedNotAnswer->getBody()->getContents(), true);
        $this->assertSame($code, $obtainDecodedNotAnswerData['code']);
        $this->assertEmpty($obtainDecodedNotAnswerData['answer'] ?? '');
        $this->assertSame(1, $subscriptor->countAll(), 'obtain-decoded must not create an event');

        // set-code-answer
        $answer = 'x-answer';
        $setCodeAnswerResponse = $application->__invoke(
            $this->createRequest('POST', 'http://localhost/set-code-answer', ['code' => $code, 'answer' => $answer])
        );
        $this->assertSame(200, $setCodeAnswerResponse->getStatusCode());
        $this->assertEmpty($setCodeAnswerResponse->getBody()->getContents());
        $captchaFromEvent = $subscriptor->getEvents('answer')[0];
        $this->assertEquals(new Captcha($code, $encodedImage, $answer), $captchaFromEvent);

        // captchas
        $captchasRequest = $application->__invoke(
            $this->createRequest('GET', 'http://localhost/captchas')
        );
        $this->assertSame(200, $captchasRequest->getStatusCode());
        $captchasData = json_decode($captchasRequest->getBody()->getContents(), true);
        $this->assertTrue(is_array($captchasData));
        $this->assertCount(1, $captchasData);
        $this->assertSame($code, $captchasData[0]['code']);
        $this->assertSame($encodedImage, $captchasData[0]['image']);
        $this->assertSame($answer, $captchasData[0]['answer']);

        // obtain-decoded (with answer)
        $obtainDecodedWithAnswer = $application->__invoke(
            $this->createRequest('POST', 'http://localhost/obtain-decoded', ['code' => $code])
        );
        $this->assertSame(200, $obtainDecodedWithAnswer->getStatusCode());
        $obtainDecodedWithAnswerData = json_decode($obtainDecodedWithAnswer->getBody()->getContents(), true);
        $this->assertSame($code, $obtainDecodedWithAnswerData['code']);
        $this->assertSame($answer, $obtainDecodedWithAnswerData['answer']);
        $captchaFromEvent = $subscriptor->getEvents('remove')[0];
        $this->assertEquals(new Captcha($code, $encodedImage, $answer), $captchaFromEvent);
    }
}
