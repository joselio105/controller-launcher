<?php

use Plugse\Ctrl\http\Request;
use PHPUnit\Framework\TestCase;
use Plugse\Ctrl\helpers\Crypto;
use Plugse\Ctrl\http\routing\Route;
use Plugse\Ctrl\http\routing\Routes;
use Plugse\Ctrl\errors\FileNotFoundError;
use Plugse\Ctrl\errors\TokenExpiredError;
use Plugse\Ctrl\errors\ClassNotFoundError;
use Plugse\Ctrl\errors\PermitionDeniedError;
use Plugse\Ctrl\http\routing\RouteCollection;
use Plugse\Ctrl\errors\PermitionIncorrectError;
use Plugse\Ctrl\http\routing\ControllerStarter;
use Plugse\Ctrl\errors\RouteNotImplementedError;

class ControllerStarterTest extends TestCase
{
    private $publicRoutes;
    private $privateRoutes;

    protected function setUp(): void
    {
        $this->publicRoutes = [
            'testPublic' => [
                [
                    'method' => 'GET',
                    'expectation' => [
                        'statusCode' => 204,
                        'message' => 'Rota pública GET implementada',
                    ],
                ],
                [
                    'method' => 'POST',
                    'expectation' => [
                        'statusCode' => 201,
                        'message' => 'Rota pública POST implementada',
                    ],
                ],
                [
                    'method' => 'PUT',
                    'expectation' => [
                        'statusCode' => 202,
                        'message' => 'Rota pública PUT implementada',
                    ],
                ],
                [
                    'method' => 'PATCH',
                    'expectation' => [
                        'statusCode' => 206,
                        'message' => 'Rota pública PATCH implementada',
                    ],
                ],
                [
                    'method' => 'DELETE',
                    'expectation' => [
                        'statusCode' => 205,
                        'message' => 'Rota pública DELETE implementada',
                    ],
                ],
            ],
        ];
        $this->privateRoutes = [
            'testPrivate' => [
                [
                    'method' => 'GET',
                    'expectation' => [
                        'statusCode' => 204,
                        'message' => 'Rota privada GET implementada',
                    ],
                ],
                [
                    'method' => 'POST',
                    'expectation' => [
                        'statusCode' => 201,
                        'message' => 'Rota privada POST implementada',
                    ],
                ],
                [
                    'method' => 'PUT',
                    'expectation' => [
                        'statusCode' => 202,
                        'message' => 'Rota privada PUT implementada',
                    ],
                ],
                [
                    'method' => 'PATCH',
                    'expectation' => [
                        'statusCode' => 206,
                        'message' => 'Rota privada PATCH implementada',
                    ],
                ],
                [
                    'method' => 'DELETE',
                    'expectation' => [
                        'statusCode' => 205,
                        'message' => 'Rota privada DELETE implementada',
                    ],
                ],
            ],
        ];
    }

    private function getRequest(array $override)
    {
        $request = new Request();

        $request->controller = 'index';
        $request->method = 'GET';
        $request->params = [];
        $request->id = false;
        $request->pagination = false;
        $request->page = 1;
        $request->pageLimit = 12;
        $request->order = 'nome';
        $request->orderDescending = false;
        $request->body = [];
        $request->header = [];

        foreach ($override as $key => $value) {
            $request->$key = $value;
        }

        return $request;
    }

    public function testStartPublicRoutes()
    {
        foreach ($this->publicRoutes as $controller => $methods) {
            $routesCollection = new RouteCollection($controller);
            foreach ($methods as $method) {
                $starter = new ControllerStarter(
                    $this->getRequest([
                        'controller' => $controller,
                        'method' => $method['method'],
                    ]),
                    $routesCollection->routes
                );

                $result = $starter->execute();
                $resultObject = json_decode($result);

                $this->assertJson($result);
                $this->assertEquals($method['expectation']['message'], $resultObject->message);
                $this->assertEquals($method['expectation']['statusCode'], http_response_code());
            }
        }
    }

    public function testStartPrivateRoutes()
    {
        foreach ($this->privateRoutes as $controller => $methods) {
            $routesCollection = new RouteCollection(
                $controller,
                ['index', 'create', 'update', 'cancel', 'delete']
            );

            foreach ($methods as $method) {
                $jwt = Crypto::CreateJWT(123);
                $starter = new ControllerStarter(
                    $this->getRequest([
                        'controller' => $controller,
                        'method' => $method['method'],
                        'header' => [
                            'Authorization' => "Bearer {$jwt['token']}",
                        ],
                    ]),
                    $routesCollection->routes
                );

                $result = $starter->execute();
                $resultObject = json_decode($result);

                $this->assertJson($result);
                $this->assertEquals($method['expectation']['message'], $resultObject->message);
                $this->assertEquals($method['expectation']['statusCode'], http_response_code());
            }
        }
    }

    /**
     * @expectedException RouteNotImplementedError
     */
    public function testFailControllerName()
    {
        $this->expectException(RouteNotImplementedError::class);

        $starter = new ControllerStarter(
            $this->getRequest([
                'controller' => 'flaus',
            ]),
            new Routes(new Route('foo')),
            './src/testers/controllers/'
        );
        $starter->execute();
    }

    /**
     * @expectedException FileNotFoundError
     */
    public function testFailControllerFile()
    {
        $this->expectException(FileNotFoundError::class);

        $controller = 'test';
        $starter = new ControllerStarter(
            $this->getRequest([
                'controller' => $controller,
            ]),
            (new RouteCollection($controller))->routes
        );

        $starter->execute();
    }

    /**
     * @expectedException ClassNotFoundError
     */
    public function testFailControllerClass()
    {
        $this->expectException(ClassNotFoundError::class);

        $controller = 'testNoClass';
        $starter = new ControllerStarter(
            $this->getRequest([
                'controller' => $controller,
            ]),
            (new RouteCollection($controller))->routes
        );

        $starter->execute();
    }

    /**
     * @expectedException PermitionDeniedError
     */
    public function testFailPermitionDenied()
    {
        $this->expectException(PermitionDeniedError::class);

        $controller = 'testPrivate';
        $routesCollection = new RouteCollection(
            $controller,
            ['index', 'create', 'update', 'cancel', 'delete']
        );

        $starter = new ControllerStarter(
            $this->getRequest([
                'controller' => $controller,
            ]),
            $routesCollection->routes
        );

        $starter->execute();
    }

    /**
     * @expectedException PermitionIncorrectError
     */
    public function testFailPermitionIncorrect()
    {
        $this->expectException(PermitionIncorrectError::class);

        $jwt = Crypto::CreateJWT(123);
        $controller = 'testPrivate';
        $routesCollection = new RouteCollection(
            $controller,
            ['index', 'create', 'update', 'cancel', 'delete']
        );

        $starter = new ControllerStarter(
            $this->getRequest([
                'controller' => $controller,
                'header' => [
                    'Authorization' => "Berer {$jwt['token']}",
                ],
            ]),
            $routesCollection->routes
        );

        $starter->execute();
    }

    /**
     * @expectedException TokenExpiredError
     */
    public function testFailTokenExpired()
    {
        $this->expectException(TokenExpiredError::class);

        $expiration = Crypto::getTimestamp('2022-05-10');
        $jwt = Crypto::CreateJWT(123, $expiration);
        $controller = 'testPrivate';
        $routesCollection = new RouteCollection(
            $controller,
            ['index', 'create', 'update', 'cancel', 'delete']
        );

        $starter = new ControllerStarter(
            $this->getRequest([
                'controller' => $controller,
                'header' => [
                    'Authorization' => "Bearer {$jwt['token']}",
                ],
            ]),
            $routesCollection->routes
        );

        $starter->execute();
    }
}
