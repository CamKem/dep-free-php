<?php

namespace App\Core\Routing;

use InvalidArgumentException;

class UrlResolver
{

    public function resolve(Route $route, $params = []): string
    {
        if (count($params) > 0) {
            return $this->addParamsToUrl($route, $params);
        }
        return $route->getUri();
    }

    private function addParamsToUrl(Route $route, mixed $params): string
    {
        $uri = $route->getUri();
        // if params are empty, return the URI as is
        if (!empty($params) && count($params) === count($route->getParameters())) {
            // Replace the placeholders with their corresponding values
            foreach ($route->getParameters() as $key => $name) {
                if (!isset($params[$key])) {
                    throw new InvalidArgumentException("Missing parameter: {$key}");
                }
                $uri = str_replace('{' . $key . '}', $params[$key], $uri);
            }
        }
        return $uri;
    }

}