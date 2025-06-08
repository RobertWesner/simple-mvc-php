<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp;

use Psr\Container\ContainerExceptionInterface;
use RobertWesner\SimpleMvcPhp\Handler\ThrowableHandlerInterface;
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
        } catch (Throwable $throwable) {
            http_response_code(500);

            $container = ContainerFactory::getContainer();
            if ($container !== null && $container->has(ThrowableHandlerInterface::class)) {
                try {
                    $container->get(ThrowableHandlerInterface::class)->handle($throwable);
                } catch (ContainerExceptionInterface $exception) {
                    // should be impossible
                    echo 'Failed to load ThrowableHandler from container: ' . $exception;
                }
            }

            // if no handler is defined in the container, just swallow the exception and don't leak it
        }
    }

    private static function configure(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Creates Container if dependency was included
        ContainerFactory::createContainer();

        if (file_exists(__BASE_DIR__ . '/configurations/container.php')) {
            require __BASE_DIR__ . '/configurations/container.php';
        }

        if (file_exists(__BASE_DIR__ . '/configurations/bundles.php')) {
            require __BASE_DIR__ . '/configurations/bundles.php';
        }
    }
}
