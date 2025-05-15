<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Route\Dummy;

final readonly class DummyService
{
    public function __construct(
        private SomethingService $somethingService,
    ) {}

    public function getSomething(): int
    {
        return $this->somethingService->get();
    }
}
