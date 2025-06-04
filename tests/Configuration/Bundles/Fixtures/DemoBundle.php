<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Configuration\Bundles\Fixtures;

use RobertWesner\SimpleMvcPhp\Bundles\BundleInterface;

final class DemoBundle implements BundleInterface
{
    /**
     * In reality this would set up container instances and other things instead of simply printing a text.
     */
    public static function load(mixed $configuration = null): void
    {
        echo "DemoBundle was loaded... wow!";
    }
}
