<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Routing;

final readonly class Request
{
    private array $parameters;
    private array $uriParameters;

    public function __construct(
        array $parameters,
        array $uriParameters,
    ) {
        $this->parameters = $this->processParameters($parameters);
        $this->uriParameters = $this->processParameters($uriParameters);
    }

    /**
     * @deprecated since v0.4.0
     */
    public function get(string $parameter, mixed $default = null): mixed
    {
        return $this->getUriParameter($parameter) ?? $this->getParameter($parameter) ?? $default;
    }

    public function getParameter(string $parameter, mixed $default = null): mixed
    {
        return $this->parameters[$parameter] ?? $default;
    }

    public function getUriParameter(string $parameter, mixed $default = null): mixed
    {
        return $this->uriParameters[$parameter] ?? $default;
    }

    private function processParameters(array $parameters): array
    {
        foreach ($parameters as &$parameter) {
            // GET or POST
            if (is_string($parameter)) {
                if (strtolower($parameter) === 'null') {
                    $parameter = null;
                } elseif (filter_var($parameter, FILTER_VALIDATE_INT)) {
                    $parameter = (int)$parameter;
                } elseif (filter_var($parameter, FILTER_VALIDATE_FLOAT)) {
                    $parameter = (float)$parameter;
                } elseif (filter_var($parameter, FILTER_VALIDATE_BOOL)) {
                    $parameter = ['true' => true, 'false' => false][$parameter];
                }
            }
        }

        return $parameters;
    }
}
