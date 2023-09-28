<?php

namespace Plugse\Ctrl\errors;

use Exception;

class MethodNotImplementedError extends Exception
{
    public function __construct(string $method)
    {
        http_response_code(405);
        parent::__construct("Erro: O método '{$method}' não foi implementado");
    }
}
