<?php

/** @noinspection HttpUrlsUsage */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

exit(call_user_func(function (string $command, string $serverPortArgument = ''): int {
    try {
        $parts = explode(':', $serverPortArgument, 2);
        $address = strval($parts[0] ?? '') ?: '127.0.0.1';
        $port = intval($parts[1] ?? 0) ?: 80;

        $app = new CaptchaLocalResolver\Application();
        $server = new React\Http\Server($app);

        $socket = new React\Socket\Server("$address:$port");
        $server->listen($socket);
        echo "Server running at http://$address:$port\n";

        return 0;
    } catch (Throwable $exception) {
        file_put_contents('php://stdout', $exception->getMessage() . PHP_EOL, FILE_APPEND);
        return 1;
    }
}, ...$argv));
