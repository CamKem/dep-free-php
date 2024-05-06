<?php

namespace App\Core\Routing;

use RouteNotFoundException;

class UrlResolver
{
    private array $routes;

    /**
     * @param $name
     * @param array $params
     * @param bool $absolute
     * @return String
     */
    public function route($name, $params = [], $absolute = true): string
    {
        if (!is_null($route = $this->getRoute($name))) {
            return $this->getRouteUrl($name, $params, $absolute);
        }

        throw new RouteNotFoundException("Route not found: {$name}");

    }

    public function getRouteUrl(Route $route, $params = [], $absolute = true)
    {
        $url = $route->getUrl();

        if (count($params) > 0) {
            $url = $this->addParamsToUrl($url, $params);
        }

        if ($absolute) {
            $url = $this->getAbsoluteUrl($url);
        }

        return $url;
    }



}