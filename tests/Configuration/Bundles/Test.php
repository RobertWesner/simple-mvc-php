<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Configuration\Bundles;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RobertWesner\SimpleMvcPhp\Configuration\Bundles;
use RobertWesner\SimpleMvcPhp\Routing\ContainerFactory;
use RobertWesner\SimpleMvcPhp\Tests\Configuration\Bundles\Fixtures\DemoBundle;

#[CoversClass(Bundles::class)]
#[UsesClass(ContainerFactory::class)]
final class Test extends TestCase
{
    public function test(): void
    {
        ContainerFactory::createContainer();

        ob_start();
        Bundles::load(DemoBundle::class);
        self::assertSame('DemoBundle was loaded... wow!', ob_get_clean());
    }
}
