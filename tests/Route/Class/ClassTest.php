<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Route\Class;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use RobertWesner\SimpleMvcPhp\Routing\ContainerFactory;
use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;

#[CoversNothing]
class ClassTest extends TestCase
{
    public function test(): void
    {
        ContainerFactory::createContainer();
        $router = RouterFactory::createRouter(__DIR__ . '/routes');

        self::assertSame(
            '[]',
            $router->route('GET', '/api/users'),
        );
        self::assertSame(
            '{"name":"foo"}',
            $router->route('GET', '/api/users/1234'),
        );
        self::assertSame(
            '{"name":"test123"}',
            $router->route('POST', '/api/users', '{"name":"test123"}'),
        );
        self::assertSame(
            '',
            $router->route('DELETE', '/api/users/1337'),
        );
    }
}
