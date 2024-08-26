<?php

namespace RobertWesner\SimpleMvcPhp\Routing;

final class RouterFactory
{
    private static Router $router;

    public static function createRouter(?string $routesPath = null): Router
    {
        self::$router = new Router();
        self::$router->setUp($routesPath ?? $_SERVER['DOCUMENT_ROOT'] . '/routes');

        return self::$router;
    }

    public static function getRouter(?string $routesPath = null): Router
    {
        if (!isset(self::$router)) {
            // TODO: exceptions and stuff
            die('Router was not created.');
        }

        return self::$router;
    }
}
