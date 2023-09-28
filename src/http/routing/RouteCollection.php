<?php

namespace Plugse\Ctrl\http\routing;

class RouteCollection
{
    public $routes;

    public function __construct(string $controllerName, array $omit = [])
    {
        $this->routes = new Routes(
            new Route($controllerName),
            new Route($controllerName, 'POST', 'create'),
            new Route($controllerName, 'PUT', 'update'),
            new Route($controllerName, 'PATCH', 'cancel'),
            new Route($controllerName, 'DELETE', 'erase'),
        );

        foreach ($this->routes as $route) {
            if (in_array($route->action, $omit)) {
                $route->toggleProtect();
            }
        }
    }
}
