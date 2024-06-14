<?php

/** @noinspection HttpUrlsUsage */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

exit(call_user_func(function (string $command, string ...$arguments): int {
    try {
        if ([] !== array_intersect(['-h', '--help'], $arguments)) {
            $name = basename($command);
            echo implode(PHP_EOL, [
                "$name - Eclipxe's Local Captcha Resolver",
                'Syntax:',
                "    $name [[ip-address]:[port-number]]",
                'Arguments:',
                '    ip-address: default to 127.0.0.1, use 0.0.0.0 to open on all ip addresses.',
                '    port-number: default to 80.',
                'Usage:',
                "    $ php $command 192.168.100.10:8001",
                '    Server running at http://192.168.100.10:8001',
                'Information:',
                '    License: MIT',
                '    Author: Carlos C Soto (eclipxe13) and contributors',
                '    Homepage: https://github.com/eclipxe13/captcha-local-resolver',
                '',
                '',
            ]);
            return 0;
        }

        $serverPortArgument = $arguments[0] ?? '';
        $serverPortParts = explode(':', $serverPortArgument, 2);
        $address = ($serverPortParts[0] ?? '') ?: '127.0.0.1';
        $port = intval($serverPortParts[1] ?? '') ?: 80;

        $app = new CaptchaLocalResolver\Application();
        $server = new React\Http\HttpServer($app);

        $socket = new React\Socket\SocketServer("$address:$port");
        $server->listen($socket);
        echo "Server running at http://$address:$port\n";

        return 0;
    } catch (Throwable $exception) {
        file_put_contents('php://stdout', $exception->getMessage() . PHP_EOL, FILE_APPEND);
        return 1;
    }
}, ...$argv));
