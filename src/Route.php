<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use RobertWesner\SimpleMvcPhp\Exception\TwigException;
use RobertWesner\SimpleMvcPhp\Routing\RouterFactory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

final class Route
{
    public const string FALLBACK = '.*';

    private static Environment $twig;

    private static function getTwig(): Environment
    {
        if (!isset(self::$twig)) {
            self::$twig = new Environment(
                new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/views'),
            );
        }

        return self::$twig;
    }

    public static function get(string $uri, callable|array $controller): void
    {
        RouterFactory::getRouter()->register('GET', $uri, $controller);
    }

    public static function post(string $uri, callable|array $controller): void
    {
        RouterFactory::getRouter()->register('POST', $uri, $controller);
    }

    public static function put(string $uri, callable|array $controller): void
    {
        RouterFactory::getRouter()->register('PUT', $uri, $controller);
    }

    public static function patch(string $uri, callable|array $controller): void
    {
        RouterFactory::getRouter()->register('PATCH', $uri, $controller);
    }

    public static function delete(string $uri, callable|array $controller): void
    {
        RouterFactory::getRouter()->register('DELETE', $uri, $controller);
    }

    public static function response(string $data, int $status = 200, array $headers = []): ResponseInterface
    {
        return new Response($status, $headers, $data);
    }

    public static function json(array $data, int $status = 200, array $headers = []): ResponseInterface
    {
        return self::response(json_encode($data), $status, array_merge([
            'Content-Type' => 'application/json',
        ], $headers));
    }

    /**
     * @throws TwigException
     */
    public static function render(string $template, array $arguments = [], int $status = 200): ResponseInterface
    {
        try {
            return self::response(
                self::getTwig()->render($template, $arguments),
                $status,
                [
                    'Content-Type' => 'text/html',
                ],
            );
        } catch (LoaderError | RuntimeError | SyntaxError $exception) {
            throw new TwigException('Could not render twig template.', previous: $exception);
        }
    }

    public static function redirect(string $target, int $status = 301): ResponseInterface
    {
        return self::response('', $status, [
            'Location' => $target,
        ]);
    }
}
