<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Routing;

use Psr\Http\Message\ResponseInterface;
use RobertWesner\SimpleMvcPhp\Route;

final class Router
{
    private array $routes = [];

    /**
     * This was added to prevent Route ".*" from blocking Routes defined afterward.
     *
     * This way a 404 Route defined in app.php doesn't block routes in backend.php,
     * since a... gets loaded before b... and thus the 404 Page would match first.
     *
     * @var callable|null
     * @since v0.7.0
     */
    private $fallbackController = null;

    private function getFiles(string $directory): array
    {
        $files = @scandir($directory);
        if (!$files) {
            return [];
        }

        $result = [];
        foreach (array_diff($files, ['.', '..']) as $file) {
            $file = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($file)) {
                $result = array_merge($result, $this->getFiles($file));
            } else {
                $result[] = $file;
            }
        }

        return $result;
    }

    public function setUp(string $routesDirectory): void
    {
        foreach ($this->getFiles($routesDirectory) as $file) {
            (function ($_file) {
                require $_file;
            })($file);
        }
    }

    public function register(string $method, string $route, callable $controller): void
    {
        if ($route === '.+') {
            @trigger_error(
                'Using Route ".+" is highly discouraged. Please use Route::FALLBACK instead.',
                E_USER_WARNING,
            );

            // .+ was most likely supposed to be .* anyway
            $route = Route::FALLBACK;
        }

        if ($route === Route::FALLBACK) {
            $this->fallbackController = $controller;

            return;
        }

        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][] = ['route' => $route, 'controller' => $controller];
    }

    public function route(string $method, string $uri, ?string $body = null): string
    {
        if ($body === null) {
            $body = file_get_contents('php://input');
        }

        if (json_validate($body)) {
            $parameters = json_decode($body, true);
        } elseif ($method === 'POST') {
            $parameters = $_POST;
        } else {
            $parameters = $_GET;
        }

        $router = null;
        foreach ($this->routes[$method] ?? [] as ['route' => $route, 'controller' => $controller]) {
            if (preg_match('/^' . str_replace('/', '\/', $route) . '$/', $uri, $matches, PREG_UNMATCHED_AS_NULL)) {
                $router = $controller;

                break;
            }
        }

        if ($router === null) {
            $router = $this->fallbackController;
        }

        if ($router === null) {
            http_response_code(404);

            return 'Not found';
        }

        /**
         * @var $router callable(Request $request): ResponseInterface
         */
        $response = $router(
            new Request(
                $parameters,
                array_map(
                    urldecode(...),
                    array_filter($matches ?? [], fn ($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY),
                ),
            ),
        );

        if ($response === null) {
            http_response_code(500);
            die('No Response provided.');
        }

        http_response_code($response->getStatusCode());

        foreach ($response->getHeaders() as $header => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $header, $value));
            }
        }

        return (string)$response->getBody();
    }
}
