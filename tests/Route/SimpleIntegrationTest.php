<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Route;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Routing\ContainerFactory;
use RobertWesner\SimpleMvcPhp\Routing\Request;
use RobertWesner\SimpleMvcPhp\Routing\Router;
use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;
use RobertWesner\SimpleMvcPhp\Tests\Route\Dummy\DummyService;

#[CoversClass(Route::class)]
#[UsesClass(Request::class)]
#[UsesClass(Router::class)]
#[UsesClass(ContainerFactory::class)]
#[UsesClass(RouterFactory::class)]
class SimpleIntegrationTest extends TestCase
{
    public function test(): void
    {
        define('__BASE_DIR__', __DIR__);

        $router = RouterFactory::createRouter('/dev/null');

        self::assertSame('Not found', $router->route('GET', '/test'));

        Route::get('/test', function () {
            return Route::response('FUN!');
        });
        self::assertSame('FUN!', $router->route('GET', '/test'));

        // Method is GET so POST fails with 404
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

        // Test custom 404 page via all-matching RegEx
        self::assertSame('Not found', $router->route('GET', '/random-whatever'));
        Route::get(Route::FALLBACK, function () {
            return Route::response('Whoops, this page isn\'t here!', 404);
        });
        self::assertSame('Whoops, this page isn\'t here!', $router->route('GET', '/random-whatever'));
        // make sure previous routes still match
        self::assertSame('ok', $router->route('GET', '/something/foo'));

        // Since v0.7.0 the Route ".*" is stored separately and no longer blocks Routes defined after it
        Route::get('/still-callable', function () {
            return Route::response('yes');
        });
        self::assertSame('yes', $router->route('GET', '/still-callable'));

        // Since v0.8.0 Routes now can have dependencies that automatically get resolved
        Route::get('/this-route-has-dependencies', function (DummyService $dummyService) {
            return Route::response((string)$dummyService->getSomething());
        });
        self::assertSame('1337', $router->route('GET', '/this-route-has-dependencies'));

        Route::get('/demopage', function () {
            return Route::render('foo.twig', [
                'world' => 'Earth',
            ]);
        });
        self::assertSame("Hello Earth!\n", $router->route('GET', '/demopage'));
    }
}
