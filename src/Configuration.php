<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp;

use RobertWesner\SimpleMvcPhp\Configuration\Bundles;
use RobertWesner\SimpleMvcPhp\Configuration\Container;

final class Configuration
{
    public const string BUNDLES = Bundles::class;
    public const string CONTAINER = Container::class;
}
