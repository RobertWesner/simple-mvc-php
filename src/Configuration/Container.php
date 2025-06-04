<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Configuration;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\SimpleMvcPhp\Routing\ContainerFactory;

/**
 * Registers a value by the passed key in the container.
 *
 * This is intended for setting up interfaces with actual implementations.
 */
final class Container
{
    /**
     * @return class-string<Container>
     */
    public static function register(string $id, mixed $value): string
    {
        ContainerFactory::getContainer()->set($id, $value);

        return self::class;
    }

    /**
     * @return class-string<Container>
     *
     * @throws AutowireException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function instantiate(string $id, string $class): string
    {
        $container = ContainerFactory::getContainer();
        $container->set($id, $container->get($class));

        return self::class;
    }
}
