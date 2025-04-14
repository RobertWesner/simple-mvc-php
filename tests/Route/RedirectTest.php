<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Route;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RobertWesner\SimpleMvcPhp\Route;

#[CoversClass(Route::class)]
final class RedirectTest extends TestCase
{
    public function test(): void
    {
        $response = Route::redirect('/foo/bar');
        self::assertEmpty($response->getBody()->getContents());
        self::assertSame(301, $response->getStatusCode());
        self::assertArrayHasKey('Location', $response->getHeaders());
        self::assertSame('/foo/bar', $response->getHeader('Location')[0]);

        $response = Route::redirect('/foo/bar', 302);
        self::assertSame(302, $response->getStatusCode());
    }
}
