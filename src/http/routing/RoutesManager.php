<?php

namespace Plugse\Ctrl\http\routing;

use Plugse\Ctrl\errors\FileNotFoundError;

class RoutesManager
{
    private $routes;
    private $routesPath;

    public function __construct(string $routesPath = './src/infra/http/routes/')
    {
        $this->routesPath = $routesPath;
        $this->routes = new Routes();
        $this->setRoutes('public');
        $this->setRoutes('private');
    }

    public function getRoutes(): Routes
    {
        return $this->routes;
    }

    public function setRoutes(string $type)
    {
        $routesFile = $this->routesPath . "{$type}.php";

        if (!file_exists($routesFile)) {
            throw new FileNotFoundError($routesFile);
        }

        $routes = (array)require_once $routesFile;
        foreach ($routes as $i => $object) {
            if (!is_object($object)) {
                continue;
            }

            $objectName = get_class($object) ?? '';

            if ($objectName === __NAMESPACE__ . '\RouteCollection') {
                $this->setRoutesByCollection($object, $type);
            }
            if ($objectName === __NAMESPACE__ . '\Route') {
                $this->setRoute($object, $type);
            }
        }
    }

    private function setRoutesByCollection(RouteCollection $collection, string $type)
    {
        foreach ($collection->routes as $route) {
            $this->setRoute($route, $type);
        }
    }

    private function setRoute(Route $route, $type)
    {
        if ($type === 'private') {
            $route->toggleProtect();
        }

        $this->routes->append($route);
    }
}
