<?php

namespace Plugse\Ctrl\errors;

use Exception;

class EntityNotFoundError extends Exception
{
    public function __construct(string $className)
    {
        http_response_code(404);
        parent::__construct("Nenhuma entidade '{$className}' foi encontrada");
    }
}
