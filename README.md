# eclipxe/captcha-local-resolver

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

This is a project to create a human captcha resolver that emulates how online resolvers work.

It is based on [ReactPHP](https://reactphp.org/) and exists to emulate an online captcha resolver service
where you human are the one that provide the captcha solution using your amazing brains.

## Install the server

Using git:

```shell
git clone https://github.com/eclipxe13/captcha-local-resolver
cd captcha-local-resolver
composer install --no-dev --optimize
```

## Run the server

```shell
php bin/service.php 127.0.0.1 9595
Server running at http://127.0.0.1:9595
```

Parameters are `host` (default 127.0.0.1) and `port` (random int between 9000 and 9999) to listen.

## How it works (eagle view)

You are scraping and the target website does not have an API, even worst, they are using a captcha and you need
to solve it to make your program work (*hello Mexico Goverment, how's your day?*).

You need to resolve the captcha, and there are a lot of services that solve this problem, like *Decaptcher*,
*AntiCaptcha*, etc. But that services that have a cost (very cheap). But for any reason, you don't want to use that.
Maybe you don't want to provide your credit card information, maybe you are running functional tests and cannot
access the real captcha resolver account.

Then, you can use this project.

- Your scraper will find that require to solve a captcha and get the image.
- Send the image to your instance of captcha-local-resolver
- The captcha will appears on your browser, you solve it using your brain's amazing OCR
- Check that the captcha has been resolved
- Set the answer into the correct field and send the form.

## How it technically works

You run captcha-local-resolver and it will open a web server with specific actions (routes):

### Route `send-image`

The route `POST /send-image` receives an image and create a code to track it.

- Parameter: `(string) image` PNG image base64 encoded.
- Response: `application/json {code: '...example'}` JSON with `code`.
- Event: `{eventName: 'append', code: '...', image: '...'}`.

### Route `obtain-decoded`

The route `POST /obtain-decoded` receives a code and return its current answer.
If it already has an answer then the captcha is droped from the server.

- Parameter: `(string) code` the code given when you post the captcha on `send-image`.
- Response: `application/json {answer: '...example'}` JSON with `answer`.
- Event: `{eventName: 'remove', code: '...'}`.

If there is no answer yet then the answer will be an empty string.

If the code does not exists you will receive a `404` HTTP Status Code.

### Route `/`

The route `GET /` will return an HTML 5 will current captchas and *subscribes* to `/events`.
This page gives you the inputs to solve the captcha and send the answer to `/set-code-answer`.

- Output: `text/html`.

### Route `set-code-answer`

The route `POST set-code-answer` receives a code and answer.

- Output: `text/plain (empty)`.
- Parameter: `(string) code` the code given when you post the captcha on `send-image`.
- Parameter: `(string) answer` the captcha's solution.
- Event: `{eventName: 'answer', code: '...', answer: '...'}`.

### Route `events`

The route `GET /events` is used to subscribe to the server events, it is a vent stream that can be consumed
using [`EventSource`](https://developer.mozilla.org/en-US/docs/Web/API/EventSource).

- Output: `text/event-stream`.

There are only 3 events:

- `append`: when a new captcha has been inserted to be solved.
- `answer`: when a captcha solution has been posted.
- `remove`: when a captcha solution has been removed.

This is what you can implement inside your own application to resolve captchas.

## Known limitations

- Work with PNG image captchas.
- Does not have a token, pass phrase, user/password or any other method to block unwanted access.

## Security

This project is not intented to be outside your local network.
Please, fill a new issue if you find any security issues.

## Compatibility

This library is compatible with *latest* supported PHP version. See <https://www.php.net/supported-versions.php>.
If you are going to contribute, try to use the full potential of the language.

## License

The eclipxe/captcha-local-resolver project is copyright by [Carlos C Soto](https://eclipxe.com.mx/) and licensed for
use under the MIT License (MIT). Please see [LICENSE] for more information.

[contributing]: https://github.com/eclipxe13/captcha-local-resolver/blob/master/CONTRIBUTING.md
[changelog]: https://github.com/eclipxe13/captcha-local-resolver/blob/master/docs/CHANGELOG.md
[todo]: https://github.com/eclipxe13/captcha-local-resolver/blob/master/docs/TODO.md

[source]: https://github.com/phpcfdi/scfdi-sat-scraper
[release]: https://github.com/eclipxe13/captcha-local-resolver/releases
[license]: https://github.com/eclipxe13/captcha-local-resolver/blob/master/LICENSE
[build]: https://travis-ci.com/eclipxe13/captcha-local-resolver?branch=master
[quality]: https://scrutinizer-ci.com/g/eclipxe13/captcha-local-resolver/
[coverage]: https://scrutinizer-ci.com/g/eclipxe13/captcha-local-resolver/code-structure/master/code-coverage/src/
[downloads]: https://packagist.org/packages/eclipxe/captcha-local-resolver

[badge-source]: https://img.shields.io/badge/source-eclipxe/captcha--local--resolver-blue?style=flat-square
[badge-release]: https://img.shields.io/github/release/eclipxe13/captcha-local-resolver?style=flat-square
[badge-license]: https://img.shields.io/github/license/eclipxe13/captcha-local-resolver?style=flat-square
[badge-build]: https://img.shields.io/travis/com/eclipxe13/captcha-local-resolver/master?style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/eclipxe13/captcha-local-resolver/master?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/eclipxe13/captcha-local-resolver/master?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/eclipxe/captcha-local-resolver?style=flat-square
