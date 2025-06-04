<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp;

use RobertWesner\SimpleMvcPhp\Routing\ContainerFactory;
use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;
use Throwable;

final class MVC
{
    public static function route(): void
    {
        self::configure();

        try {
            echo RouterFactory::createRouter()->route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        } catch (Throwable $exception) {
            echo ErrorRenderer::render($exception);
        }
    }

    private static function configure(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Creates Container if dependency was included
        ContainerFactory::createContainer();

        if (file_exists(__BASE_DIR__ . '/configuration/container.php')) {
            require __BASE_DIR__ . '/configuration/container.php';
        }

        if (file_exists(__BASE_DIR__ . '/configuration/bundles.php')) {
            require __BASE_DIR__ . '/configuration/bundles.php';
        }
    }
}
