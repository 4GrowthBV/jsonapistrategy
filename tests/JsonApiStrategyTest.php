<?php

use InThere\Route\JsonApi\JsonApiStrategy;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JsonApiStrategyTest extends TestCase
{
    /**
     * Asserts that the strategy builds a json response for a controller that does not return a repsonse.
     *
     * @return void
     */
    public function testStrategyBuildsJsonErrorResponseWhenNoResponseReturned()
    {
        $this->expectException('RuntimeException');

        $route = $this->getMockBuilder(League\Route\Route::class)->getMock();
        $callable = function (ServerRequestInterface $request, ResponseInterface $response, array $args = []) {

        };

        $route->expects($this->once())->method('getCallable')->will($this->returnValue($callable));

        $strategy = new JsonApiStrategy();
        $callable = $strategy->getCallable($route, []);

        $request = $this->getMockBuilder(Psr\Http\Message\ServerRequestInterface::class)->getMock();
        $response = $this->getMockBuilder(Psr\Http\Message\ResponseInterface::class)->getMock();

        $next = function ($request, $response) {
            return $response;
        };

        $callable($request, $response, $next);
    }
}
