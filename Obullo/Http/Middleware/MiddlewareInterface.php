<?php

namespace Obullo\Http\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Middleware. ( * Borrowed from Zend Stratigility. )
 *
 * Middleware accepts a request and a response, and optionally a
 * callback `$out` (called if the middleware wants to allow further
 * middleware to process the incoming request, or to delegate output to another
 * process).
 *
 * Middleware that does not need or desire further processing should not
 * call `$out`, and should usually instead `return $response->end();`.
 *
 * For the purposes of Conduit, `$out` is typically one of either an instance
 * of `Next` or an instance of `FinalHandler`, and, as such, should follow
 * those calling semantics.
 */
interface MiddlewareInterface
{
    /**
     * Process an incoming request and/or response.
     *
     * @param Request       $request  request
     * @param Response      $response response
     * @param null|callable $out      out
     * 
     * @return null|Response 
     */
    public function __invoke(Request $request, Response $response, callable $out = null);
}
