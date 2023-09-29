<?php

namespace Plugse\Ctrl\http\routing;

/**
 * Cria uma coleção de cinco rotas: index, create, update, cancel e erase
 * @param string $controllerName - O nome do controller (sem o sufixo Controller).
 * @param array $omit - A lista de rotas que devem alterar o seu parâmetro $isPrivate. Valor padrão [].
 */
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
