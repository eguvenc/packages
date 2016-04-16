<?php

namespace Obullo\Http\Zend\Stratigility;

use Obullo\Http\Middleware\MiddlewareInterface;
use Obullo\Http\Middleware\ErrorMiddlewareInterface;

use Throwable;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Dispatch middleware
 *
 * This class is an implementation detail of Next.
 *
 * @internal
 */
class Dispatch
{
    /**
     * Dispatch middleware
     *
     * Given a route (which contains the handler for given middleware),
     * the $err value passed to $next, $next, and the request and response
     * objects, dispatch a middleware handler.
     *
     * If $err is non-falsy, and the current handler has an arity of 4,
     * it will be dispatched.
     *
     * If $err is falsy, and the current handler has an arity of < 4,
     * it will be dispatched.
     *
     * In all other cases, the handler will be ignored, and $next will be
     * invoked with the current $err value.
     *
     * If an exception is raised when executing the handler, the exception
     * will be assigned as the value of $err, and $next will be invoked
     * with it.
     *
     * @param callable               $value    middleware data
     * @param mixed                  $err      error
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response respone
     * @param callable               $next     callable next
     */
    public function __invoke(
        $value,
        $err,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        global $testEnvironment;

        $hasError = (null !== $err);
        $handler  = $value['callable'];

        switch (true) {
        case ($handler instanceof ErrorMiddlewareInterface):
            $arity = 4;
            break;
        case ($handler instanceof MiddlewareInterface):
            $arity = 3;
            break;
        default:
            $arity = Utils::getArity($handler);
            break;
        }

        // @todo Trigger event with Route, original URL from request?

        try {

            if ($hasError && $arity === 4) {

                if ($testEnvironment != null) {
                    return $this->unitTestError($exception, $response);
                }

                return $handler($err, $request, $response, $next);
            }
            if (! $hasError && $arity < 4) {
                return $handler($request, $response, $next, $value['params']);
            }

        } catch (Throwable $throwable) { // PHP 7 + throwable error support
            
            if ($testEnvironment != null) {
                return $this->unitTestError($exception, $response);
            }

            return $next($request, $response, $throwable);

        } catch (Exception $exception) {

            if ($testEnvironment != null) {
                return $this->unitTestError($exception, $response);
            }

            return $next($request, $response, $exception);
        }

        return $next($request, $response, $err, $value['params']);
    }

    /**
     * Display unit test errors
     * 
     * @param mixed  $error    error
     * @param object $response response
     * 
     * @return void
     */
    protected function unitTestError($error, $response)
    {
        if (is_object($error) && $error instanceof Exception) {
            return $response->json(
                [
                    'message' => $error->getMessage(),
                    'file' => $error->getFile(),
                    'line' => $error->getLine()
                ],
                500
            );
        }
        return $response->json(['message' => $error, 'file' => 0, 'line' => 0], 500);
    }

}
