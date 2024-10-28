<?php

namespace RobertWesner\SimpleMvcPhp\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\Request;
use RobertWesner\SimpleMvcPhp\Routing\Router;
use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;

#[CoversClass(Route::class)]
#[UsesClass(Request::class)]
#[UsesClass(Router::class)]
#[UsesClass(RouterFactory::class)]
class RouteTest extends TestCase
{
    public function test(): void
    {
        $router = RouterFactory::createRouter('/dev/null');

        self::assertSame('Not found', $router->route('GET', '/test'));

        Route::get('/test', function () {
            return Route::response('FUN!');
        });
        self::assertSame('FUN!', $router->route('GET', '/test'));

        self::assertSame('Not found', $router->route('POST', '/test'));

        Route::post('/api/does-this-work', function (Request $request) {
            return Route::json([
                'working' => $request->get('working', false),
            ]);
        });

        self::assertSame(
            '{"working":false}',
            $router->route('POST', '/api/does-this-work'),
        );

        self::assertSame(
            '{"working":true}',
            $router->route('POST', '/api/does-this-work', <<<'JSON'
            {
                "working": true
            }
            JSON
            ),
        );

        self::assertSame('Not found', $router->route('GET', '/something/foo'));
        Route::get('/something/foo.*', function () {
            return Route::response('ok');
        });
        self::assertSame('ok', $router->route('GET', '/something/foo'));
        self::assertSame('ok', $router->route('GET', '/something/foo-1234'));

        self::assertSame('Not found', $router->route('GET', '/random-whatever'));
        Route::get('.*', function () {
            return Route::response('Whoops, this page isn\'t here!');
        });
        self::assertSame('Whoops, this page isn\'t here!', $router->route('GET', '/random-whatever'));
    }
}
