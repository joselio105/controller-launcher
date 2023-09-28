<?php

namespace Plugse\Ctrl\http\routing;

use Plugse\Ctrl\errors\ClassNotFoundError;
use Plugse\Ctrl\errors\FileNotFoundError;
use Plugse\Ctrl\errors\PermitionDeniedError;
use Plugse\Ctrl\errors\PermitionIncorrectError;
use Plugse\Ctrl\errors\RouteNotImplementedError;
use Plugse\Ctrl\errors\TokenExpiredError;
use Plugse\Ctrl\helpers\Crypto;
use Plugse\Ctrl\http\Request;
use Plugse\Fp\Json;

class ControllerStarter
{
    public $routes;
    private $request;
    private $controllerPath;
    private $route;
    private $controllerName;
    private $controllerClass;
    private $controllerAction;

    public function __construct(
        Request $request,
        Routes $routes,
        $controllersPath = './src/infra/http/controllers/'
    ) {
        $this->controllerPath = $controllersPath;
        $this->routes = $routes;
        $this->request = $request;
    }

    public function execute(string $namespace = ''): string
    {
        $this->setControllerName();
        $this->setControllerFile();
        $this->setControllerClass(
            $this->getNamespace($namespace)
        );

        if ($this->canExecute()) {
            return $this->getResult();
        }
    }

    private function getNamespace(string $namespace): string
    {
        if (strlen($namespace) > 0) {
            return $namespace;
        }

        $nsArray = Json::read('composer.json')['autoload']['psr-4'];

        return array_keys($nsArray)[0] . 'infra\\http\\controllers';
    }

    private function setControllerName()
    {
        foreach ($this->routes as $route) {
            if (
                ($route->controllerName === $this->request->controller) and
                ($route->method === $this->request->method)
            ) {
                $this->controllerName = ucfirst($route->controllerName);
                $this->route = $route;
                $this->controllerAction = $route->action;
            }
        }

        if (!isset($this->controllerName)) {
            throw new RouteNotImplementedError($this->request);
        }
    }

    private function setControllerFile()
    {
        $filename = "{$this->controllerPath}{$this->controllerName}Controller.php";

        if (!file_exists($filename)) {
            throw new FileNotFoundError($filename);
        }
    }

    private function setControllerClass(string $namespace)
    {
        $className = "{$namespace}\\{$this->controllerName}Controller";

        if (!class_exists($className)) {
            throw new ClassNotFoundError($className);
        }

        $this->controllerClass = $className;
    }

    private function canExecute(): bool
    {
        if (!$this->route->isPrivate) {
            return true;
        }

        if (!key_exists('Authorization', $this->request->header)) {
            throw new PermitionDeniedError($this->controllerName, $this->route->action);
        }

        if (!preg_match('/Bearer\s(\S+)/', $this->request->header['Authorization'], $matches)) {
            throw new PermitionIncorrectError();
        }

        $tokenDecoded = Crypto::DecodeToken($matches[1]);

        if ($tokenDecoded['exp'] <= Crypto::getTimestamp()) {
            throw new TokenExpiredError();
        }

        return true;
    }

    private function getResult(): string
    {
        $controller = new $this->controllerClass();
        $action = $this->controllerAction;

        return $controller->$action($this->request);
    }
}
