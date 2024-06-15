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
                // if the parameter is not expected, we should add it to the query string
                $uri = str_replace('{' . $key . '}', $params[$key], $uri);
            }
            // else if there is more params than expected, we should add the rest to the query string
        } else if (count($params) > count($route->getParameters())) {
            $params = array_diff_key($params, $route->getParameters());
            $query = http_build_query($params);
            $uri .= '?' . $query;
        }
        return $uri;
    }

}