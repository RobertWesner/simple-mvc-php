<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Routing;

use RobertWesner\DependencyInjection\Container;

final class ContainerFactory
{
    private static ?Container $container = null;

    public static function createContainer(): Container
    {
        if (class_exists(Container::class)) {
            self::$container = new Container();
        }

        return self::$container;
    }

    public static function getContainer(): Container
    {
        if (!isset(self::$container)) {
            die('Container was not created.');
        }

        return self::$container;
    }
}
