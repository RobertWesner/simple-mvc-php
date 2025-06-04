<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Configuration\Container;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\SimpleMvcPhp\Configuration\Container;
use RobertWesner\SimpleMvcPhp\Routing\ContainerFactory;
use RobertWesner\SimpleMvcPhp\Tests\Configuration\Container\Fixtures\Bar;
use RobertWesner\SimpleMvcPhp\Tests\Configuration\Container\Fixtures\Foo;
use RobertWesner\SimpleMvcPhp\Tests\Configuration\Container\Fixtures\FooInterface;

#[CoversClass(Container::class)]
#[UsesClass(ContainerFactory::class)]
final class Test extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function testMissingBar(): void
    {
        ContainerFactory::createContainer();

        $this->expectException(ContainerExceptionInterface::class);
        Container::instantiate(FooInterface::class, Foo::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function test(): void
    {
        $container = ContainerFactory::createContainer();

        Container
            ::register(Bar::class, new Bar(':^)'))
            ::instantiate(FooInterface::class, Foo::class);

        $foo = $container->get(FooInterface::class);
        self::assertInstanceOf(FooInterface::class, $foo);
        self::assertSame(':^)', $foo->test());
    }
}
