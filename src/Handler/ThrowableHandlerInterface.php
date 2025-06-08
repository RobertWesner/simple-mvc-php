<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Handler;

use Throwable;

interface ThrowableHandlerInterface
{
    public function handle(Throwable $throwable): void;
}
