<?php

 declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Routing\Router;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;
use RobertWesner\SimpleMvcPhp\Routing\Router;

/**
 * Tests access of RegEx-Groups inside Request.
 *
 * @since v0.4.0
 */
#[CoversClass(Router::class)]
#[UsesClass(Request::class)]
#[UsesClass(Route::class)]
class URIParameterTest extends TestCase
{
    public function test(): void
    {
        $router = new Router();

        $router->register('GET', '/users/(?<userId>\d+)/?', function (Request $request) {
            TestCase::assertSame(1234, $request->getUriParameter('userId'));

            return Route::response('');
        });
        $router->route('GET', '/users/1234');

        $router->register('GET', '/demo/(?<test>[^/]+)', function (Request $request) {
            TestCase::assertSame('Foo Bar 1337 Hello//World', $request->getUriParameter('test'));

            return Route::response('');
        });
        $router->route('GET', '/demo/Foo%20Bar%201337%20Hello%2F%2FWorld');
    }
}
