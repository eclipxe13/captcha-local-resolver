<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

exit(call_user_func(function (...$arguments): int {
    $address = strval($arguments[1] ?? '') ?: '127.0.0.1';
    $port = intval($arguments[2] ?? 0) ?: random_int(9000, 9999);

    $app = new CaptchaLocalResolver\Application();
    $loop = React\EventLoop\Factory::create();
    $server = new React\Http\Server($app);

    $server->listen(new React\Socket\Server("{$address}:{$port}", $loop));
    echo "Server running at http://{$address}:{$port}\n";

    $loop->run();

    return 0;
}, ...$argv));
