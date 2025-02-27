<?php

 declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;

require __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo RouterFactory::createRouter()->route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
