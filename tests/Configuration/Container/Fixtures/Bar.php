<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Configuration\Container\Fixtures;

readonly class Bar
{
    public function __construct(
        public string $hello,
    ) {}
}
