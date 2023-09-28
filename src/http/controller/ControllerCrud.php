<?php

namespace Plugse\Ctrl\http\controller;

use Plugse\Ctrl\http\Request;
use Plugse\Ctrl\http\Response;

interface ControllerCrud
{
    public function index(Request $request): Response;

    public function create(Request $request): Response;

    public function update(Request $request): Response;

    public function cancel(Request $request): Response;

    public function erase(Request $request): Response;
}
