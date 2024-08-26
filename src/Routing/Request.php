<?php

namespace RobertWesner\SimpleMvcPhp\Routing;

final readonly class Request
{
    private array $parameters;

    public function __construct(array $parameters)
    {
        foreach ($parameters as &$parameter) {
            // GET or POST
            if (is_string($parameter)) {
                if (strtolower($parameter) === 'null') {
                    $parameter = null;
                } elseif (filter_var($parameter, FILTER_VALIDATE_INT)) {
                    $parameter = (int)$parameter;
                } elseif (filter_var($parameter, FILTER_VALIDATE_BOOL)) {
                    $parameter = ['true' => true, 'false' => false][$parameter];
                }
            }
        }

        $this->parameters = $parameters;
    }

    public function get(string $parameter, mixed $default = null): mixed
    {
        return $this->parameters[$parameter] ?? $default;
    }
}
