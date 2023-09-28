<?php

namespace Plugse\Ctrl\errors;

use Exception;

class PermitionIncorrectError extends Exception
{
    public function __construct()
    {
        http_response_code(401);
        parent::__construct('Erro: Tipo de autenticação incorreto');
    }
}
