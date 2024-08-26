<?php

namespace RobertWesner\SimpleMvcPhp\Routing;

use Psr\Http\Message\ResponseInterface;

final class Router
{
    private array $routes = [];

    private function getFiles(string $directory): array
    {
        $result = [];
        foreach (array_diff(scandir($directory), ['.', '..']) as $file) {
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

    public function register(string $method, string $uri, callable $controller): void
    {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$uri] = $controller;
    }

    public function route(string $method, string $uri): string
    {
        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            die('Not Found');
        }

        $input = file_get_contents('php://input');
        if (json_validate($input)) {
            $parameters = json_decode($input, true);
        } elseif ($method === 'POST') {
            $parameters = $_POST;
        } else {
            $parameters = $_GET;
        }

        /**
         * @var $router callable(Request $request): ResponseInterface
         */
        $router = $this->routes[$method][$uri];
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
