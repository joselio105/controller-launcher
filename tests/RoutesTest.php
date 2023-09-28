<?php

use PHPUnit\Framework\TestCase;
use Plugse\Ctrl\http\routing\Routes;
use Plugse\Ctrl\http\routing\RoutesManager;

class RoutesTest extends TestCase
{
    private $routes;

    protected function setUp(): void
    {
        $routing = new RoutesManager();
        $this->routes = $routing->getRoutes();
    }

    public function testCreatedRoutes()
    {
        $this->assertInstanceOf(
            Routes::class,
            $this->routes
        );

        return $this->routes;
    }

    /**
     * @depends testCreatedRoutes
     */
    public function testCountRoutesCreated(Routes $routes)
    {
        $this->assertCount(10, $routes);
    }
}
