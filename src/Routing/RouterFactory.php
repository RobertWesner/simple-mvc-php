<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Routing;

final class RouterFactory
{
    private static Router $router;

    public static function createRouter(?string $routesPath = null): Router
    {
        self::$router = new Router(ContainerFactory::getContainer());
        self::$router->setUp($routesPath ?? $_SERVER['DOCUMENT_ROOT'] . '/routes');

        return self::$router;
    }

    public static function getRouter(): Router
    {
        if (!isset(self::$router)) {
            die('Router was not created.');
        }

        return self::$router;
    }
}
