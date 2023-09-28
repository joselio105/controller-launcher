<?php

namespace Plugse\Ctrl\errors;

use Exception;

class ClassNotFoundError extends Exception
{
    public function __construct(string $className)
    {
        http_response_code(404);
        parent::__construct("Erro: A classe '{$className}' não existe");
    }
}
