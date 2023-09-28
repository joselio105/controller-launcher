<?php

namespace Plugse\Ctrl\errors;

use Exception;

class PermitionDeniedError extends Exception
{
    public function __construct(string $controller, string $action)
    {
        http_response_code(401);
        parent::__construct("Erro: É necessário estar autenticado para acessar a função '{$action}' na classe '{$controller}'");
    }
}
