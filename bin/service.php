<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

exit(call_user_func(function (...$arguments): int {
    try {
        $address = strval($arguments[1] ?? '') ?: '127.0.0.1';
        $port = intval($arguments[2] ?? 0) ?: random_int(9000, 9999);

        $app = new CaptchaLocalResolver\Application();
        $loop = React\EventLoop\Factory::create();
        $server = new React\Http\Server($loop, $app);

        $socket = new React\Socket\Server("{$address}:{$port}", $loop);
        $server->listen($socket);
        echo "Server running at http://{$address}:{$port}\n";

        $loop->run();

        return 0;
    } catch (Throwable $exception) {
        file_put_contents('php://stdout', $exception->getMessage() . PHP_EOL, FILE_APPEND);
        return 0;
    }
}, ...$argv));
