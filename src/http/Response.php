<?php

namespace Plugse\Ctrl\http;

class Response
{
    private $value;
    private $statusCode;

    public function __construct($value, $statusCode = 200)
    {
        $this->value = $value;
        $this->statusCode = $statusCode;
    }

    public function __toString()
    {
        if (!isset($this->value)) {
            http_response_code(417);

            return json_encode([
                'error' => 'Nenhuma resposta foi enviada',
            ]);
        }

        http_response_code($this->statusCode);

        return json_encode(
            (
                is_string($this->value) ?
                ['message' => $this->value] :
                $this->value
            ),
            JSON_PRETTY_PRINT
        );
    }
}
