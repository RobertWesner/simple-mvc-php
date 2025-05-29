<?php

/**
 * @version v0.9.0
 */

declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\ErrorRenderer;
use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;

require __DIR__ . '/vendor/autoload.php';

const __BASE_DIR__ = __DIR__;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    echo RouterFactory::createRouter()->route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $exception) {
    echo ErrorRenderer::render($exception);
}
