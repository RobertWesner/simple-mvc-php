<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Routing;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use ReflectionFunction;
use RobertWesner\SimpleMvcPhp\Exception\RouterException;
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
     * @var callable|array|null
     * @since v0.7.0
     */
    private $fallbackController = null;

    public function __construct(
        private readonly ?ContainerInterface $container = null,
    ) {}

    public function setUp(string $routesDirectory): void
    {
        foreach ($this->getFiles($routesDirectory) as $file) {
            (function ($_file) {
                require $_file;
            })($file);
        }
    }

    public function register(string $method, string $route, callable|array $controller): void
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

    /**
     * @throws RouterException
     */
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

        $router ??= $this->fallbackController;
        if ($router === null) {
            http_response_code(404);

            return 'Not found';
        }

        $request = new Request(
            $parameters,
            array_map(
                urldecode(...),
                array_filter($matches ?? [], fn ($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY),
            ),
        );

        if (!is_callable($router)) {
            if (class_exists($router[0])) {
                if ($this->container === null) {
                    throw new RouterException(sprintf(
                        'Could not autowire controller "%s". Please use robertwenser/dependency-injection.',
                        $router[0],
                    ));
                }

                try {
                    $router[0] = $this->container->get($router[0]);
                    $router = $router(...);
                } catch (ContainerExceptionInterface $exception) {
                    throw new RouterException(sprintf(
                        'Autowired controller "%s" could not be loaded from container.',
                        $router[0],
                    ));
                }
            } else {
                throw new (sprintf(
                    'Invalid router class "%s".',
                    $router[0],
                ));
            }
        }

        try {
            $function = new ReflectionFunction($router);
        } catch (ReflectionException $exception) {
            throw new RouterException('Could not reflect function.', previous: $exception);
        }

        $routerParameters = [];
        foreach ($function->getParameters() as $parameter) {
            if (is_a($parameter->getType()->getName(), Request::class, true)) {
                $routerParameters[] = $request;

                continue;
            }

            if ($this->container === null) {
                throw new RouterException(sprintf(
                    'Could not autowire parameter "%s" of type "%s". Please use robertwenser/dependency-injection.',
                    $parameter->getName(),
                    $parameter->getType()->getName(),
                ));
            }

            try {
                $routerParameters[] = $this->container->get($parameter->getType()->getName());
            } catch (ContainerExceptionInterface $exception) {
                throw new RouterException(sprintf(
                    'Autowired class "%s" of type "%s" could not be loaded from container.',
                    $parameter->getName(),
                    $parameter->getType()->getName(),
                ));
            }
        }

        /**
         * @var callable(): ResponseInterface $router
         */
        $response = $router(...$routerParameters);

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
}
