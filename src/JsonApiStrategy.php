<?php

namespace InThere\Route\JsonApi;

use \Exception;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Route;
use League\Route\Strategy\StrategyInterface;
use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Encoder\Encoder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class JsonApiStrategy implements StrategyInterface
{
    /**
     * Returns the error response for the JSONApi standard.
     * @param ResponseInterface $response
     * @param Exception $exception
     * @param Error $error
     * @return ResponseInterface
     */
    private function getJsonApiErrorResponse(ResponseInterface $response, Exception $exception, Error $error)
    {
        $response->getBody()->write(
            Encoder::instance()->encodeError($error)
        );

        $response = $response->withAddedHeader('content-type', 'application/json');
        return $response->withStatus($error->getStatus(), $exception->getMessage());
    }

    /**
     * @inheritdoc
     */
    public function getCallable(Route $route, array $vars)
    {
        return function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use (
            $route,
            $vars
        ) {
            $return = call_user_func_array($route->getCallable(), [$request, $response, $vars]);

            if (! $return instanceof ResponseInterface) {
                throw new RuntimeException(
                    'Route callables must return an instance of (Psr\Http\Message\ResponseInterface)'
                );
            }

            $response = $return;
            $response = $next($request, $response);

            return $response->withAddedHeader('content-type', 'application/json');
        };
    }

    /**
     * @inheritdoc
     */
    public function getNotFoundDecorator(NotFoundException $exception)
    {
        $strategy = $this;
        return function (ServerRequestInterface $request, ResponseInterface $response) use ($strategy, $exception) {
            $error = new Error(
                null,
                null,
                '404',
                $exception->getCode(),
                get_class($exception),
                $exception->getMessage()
            );

            return $strategy->getJsonApiErrorResponse($response, $exception, $error);
        };
    }

    /**
     * @inheritdoc
     */
    public function getMethodNotAllowedDecorator(MethodNotAllowedException $exception)
    {
        $strategy = $this;
        return function (ServerRequestInterface $request, ResponseInterface $response) use ($strategy, $exception) {
            $error = new Error(
                null,
                null,
                '405',
                $exception->getCode(),
                get_class($exception),
                $exception->getMessage()
            );

            return $strategy->getJsonApiErrorResponse($response, $exception, $error);
        };
    }

    /**
     * @inheritdoc
     */
    public function getExceptionDecorator(Exception $exception)
    {
        $strategy = $this;
        return function (ServerRequestInterface $request, ResponseInterface $response) use ($strategy, $exception) {
            $error = new Error(
                null,
                null,
                '500',
                $exception->getCode(),
                get_class($exception),
                $exception->getMessage()
            );

            return $strategy->getJsonApiErrorResponse($response, $exception, $error);
        };
    }
}
