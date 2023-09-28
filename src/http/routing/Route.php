<?php

namespace Plugse\Ctrl\http\routing;

class Route
{
    public $controllerName;
    public $method;
    public $action;
    public $isPrivate;

    public function __construct(
        string $controllerName,
        string $method = 'GET',
        string $action = 'index',
        bool $isPrivate = false
    ) {
        $this->controllerName = $controllerName;
        $this->method = $method;
        $this->action = $action;
        $this->isPrivate = $isPrivate;
    }

    public function toggleProtect()
    {
        $this->isPrivate = !$this->isPrivate;
    }
}
