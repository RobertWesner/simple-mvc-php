<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Configuration\Container\Fixtures;

readonly class Foo implements FooInterface
{
    public function __construct(
        // Should be autowired but itself cannot be autowired
        private Bar $bar,
    ) {}

    public function test(): string
    {
        return $this->bar->hello;
    }
}
