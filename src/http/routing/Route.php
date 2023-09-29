<?php

namespace Plugse\Ctrl\http\routing;

/**
     * Cria uma nova rota
     * @param string $controllerName - O nome do controller (sem o sufixo Controller).
     * @param string $method - O método http que será usado para acessar a action. Valor padrão GET.
     * @param string $action - A função a ser executada na classe controller. Valor padrão index.
     * @param bool $isPrivate - Essa rota é privada? Valor padrão false.
     */
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
