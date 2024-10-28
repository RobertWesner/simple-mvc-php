<?php

namespace RobertWesner\SimpleMvcPhp\Routing;

use Psr\Http\Message\ResponseInterface;

final class Router
{
    private array $routes = [];

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
            if (preg_match('/^' . str_replace('/', '\/', $route) . '$/', $uri)) {
                $router = $controller;
            }
        }

        if ($router === null) {
            http_response_code(404);

            return 'Not found';
        }

        /**
         * @var $router callable(Request $request): ResponseInterface
         */
        $response = $router(new Request($parameters));

        http_response_code($response->getStatusCode());

        foreach ($response->getHeaders() as $header => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $header, $value));
            }
        }

        return $response->getBody();
    }
}
