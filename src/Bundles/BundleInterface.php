<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Bundles;

interface BundleInterface
{
    public static function load(mixed $configuration = null): void;
}
