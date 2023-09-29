<?php

namespace Plugse\Ctrl;

use Plugse\Ctrl\http\Request;
use Plugse\Ctrl\http\routing\ControllerStarter;
use Plugse\Ctrl\http\routing\RoutesManager;

class Bootstrap
{
    private $request;

    public function __construct()
    {
        $this->request = new Request();
        $this->corsPolicy();
    }

    private function corsPolicy()
    {
        header('Access-Control-Allow-Origin: http://localhost:5173');
        header('Access-Control-Allow-Headers: content-type');
        header('Access-Control-Allow-Methods: POST, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    /**
     * Retorna o resultado do método executado na classe controller definida na rota.
     * @return string
     */
    public function getResponse(): string
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                http_response_code(200);

                return json_encode(['message' => 'OK']);
            }
            $routing = new RoutesManager();
            $controller = new ControllerStarter(
                $this->request,
                $routing->getRoutes()
            );

            return $controller->execute();
        } catch (\Throwable $th) {
            return json_encode([
                'error' => $th->getMessage(),
            ]);
        }
    }
}
