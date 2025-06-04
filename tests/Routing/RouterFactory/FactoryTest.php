<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Routing\RouterFactory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\ContainerFactory;
use RobertWesner\SimpleMvcPhp\Routing\Request;
use RobertWesner\SimpleMvcPhp\Routing\Router;
use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;

#[CoversClass(RouterFactory::class)]
#[UsesClass(Router::class)]
#[UsesClass(Route::class)]
#[UsesClass(Request::class)]
class FactoryTest extends TestCase
{
    public function test(): void
    {
        ContainerFactory::createContainer();
        $router = RouterFactory::createRouter(__DIR__ . '/routes');
        self::assertInstanceOf(Router::class, $router);
        self::assertSame('bar', $router->route('GET', '/foo'));
    }
}
