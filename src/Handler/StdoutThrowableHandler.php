<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Handler;

use Throwable;

class StdoutThrowableHandler implements ThrowableHandlerInterface
{
    public function handle(Throwable $throwable): void
    {
        file_put_contents('php://stdout', sprintf(
            "[%s] Uncaught %s\n",
            date('Y-m-d h:i:s'),
            $throwable,
        ));

        echo 'Internal Server Error';
    }
}
