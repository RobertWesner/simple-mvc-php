<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Configuration;

use RobertWesner\SimpleMvcPhp\Bundles\BundleInterface;

final class Bundles
{
    /**
     * @param class-string<BundleInterface> $bundle
     * @return class-string<Bundles>
     */
    public static function load(string $bundle, mixed $configuration = null): string
    {
        $bundle::load($configuration);

        return self::class;
    }
}
