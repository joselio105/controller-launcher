<?php

namespace Plugse\Ctrl\errors;

use Exception;

class TokenDecodeError extends Exception
{
    public function __construct()
    {
        http_response_code(406);
        parent::__construct('Erro: Falha em decodificar BASE_64 - Caracteres inválidos');
    }
}
