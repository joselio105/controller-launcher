<?php

namespace Plugse\Ctrl\infra\http\controllers;

use Plugse\Ctrl\http\Request;
use Plugse\Ctrl\http\Response;
use Plugse\Ctrl\http\controller\ControllerCrud;

class TestPrivateController implements ControllerCrud
{
    public function index(Request $request): Response
    {
        return new Response('Rota privada GET implementada', 204);
    }

    public function create(Request $request): Response
    {
        return new Response('Rota privada POST implementada', 201);
    }

    public function update(Request $request): Response
    {
        return new Response('Rota privada PUT implementada', 202);
    }

    public function cancel(Request $request): Response
    {
        return new Response('Rota privada PATCH implementada', 206);
    }

    public function erase(Request $request): Response
    {
        return new Response('Rota privada DELETE implementada', 205);
    }
}
