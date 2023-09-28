<?php

namespace Plugse\Ctrl\errors;

use Exception;
use Plugse\Ctrl\http\Request;

class RouteNotImplementedError extends Exception
{
    public function __construct(Request $request)
    {
        http_response_code(412);
        parent::__construct("Erro: A rota requisitada '({$request->method}){$request->controller}' não existe");
    }
}
