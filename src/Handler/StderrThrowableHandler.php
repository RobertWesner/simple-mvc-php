<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Handler;

use Throwable;

class StderrThrowableHandler implements ThrowableHandlerInterface
{
    public function handle(Throwable $throwable): void
    {
        file_put_contents('php://stderr', sprintf(
            '[%s] Uncaught %s',
            date('Y-m-d h:i:s'),
            $throwable,
        ));

        echo 'Internal Server Error';
    }
}
