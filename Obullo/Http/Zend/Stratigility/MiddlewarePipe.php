<?php

namespace Obullo\Http\Zend\Stratigility;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use SplQueue;
use Obullo\Utils\Benchmark;
use Obullo\Container\ContainerAwareInterface;
use Obullo\Http\Middleware\MiddlewareInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Pipe middleware like unix pipes.
 *
 * This class implements a pipe-line of middleware, which can be attached using
 * the `pipe()` method, and is itself middleware.
 *
 * The request and response objects are decorated using the Zend\Stratigility\Http
 * variants in this package, ensuring that the request may store arbitrary
 * properties, and the response exposes the convenience `write()`, `end()`, and
 * `isComplete()` methods.
 *
 * It creates an instance of `Next` internally, invoking it with the provided
 * request and response instances; if no `$out` argument is provided, it will
 * create a `FinalHandler` instance and pass that to `Next` as well.
 *
 * Inspired by Sencha Connect.
 *
 * @see https://github.com/sencha/connect
 */
class MiddlewarePipe implements MiddlewareInterface
{
    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * SplQueue
     * 
     * @var object
     */
    protected $pipeline;

    /**
     * Benchmark option
     * 
     * @var boolean
     */
    protected $benchmark = false;

    /**
     * Constructor
     *
     * @param Container $container container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->pipeline  = new SplQueue;
        $middleware      = $container->get('middleware');
        /**
         * Debugger
         */
        if ($container->get('config')->get('config')['extra']['debugger']) {
            $middleware->add('Debugger');
        }
        /**
         * Injection
         */
        foreach ($middleware->getQueue() as $value) {
            if ($value['callable'] instanceof ContainerAwareInterface) {
                $value['callable']->setContainer($container);
            }
            $this->pipeline->enqueue($value);
        }
    }

    /**
     * Start benchmark
     * 
     * @return void
     */
    public function benchmarkStart()
    {
        $this->benchmark = true;
    }

    /**
     * End benchmark
     * 
     * @return void
     */
    public function benchmarkEnd()
    {
        Benchmark::end($this->container->get('request'));
    }

    /**
     * Get app request
     * 
     * @return object
     */
    public function getRequest()
    {
        if ($this->benchmark) {
            return Benchmark::start($this->container->get('request'));
        }
        return $this->container->get('request');
    }

    /**
     * Get app response
     * 
     * @return object
     */
    public function getResponse()
    {
        return $this->container->get('response');
    }

    /**
     * Returns to final handler class
     *
     * @param Response $response response
     * 
     * @return object
     */
    protected function getFinalHandler($response)
    {
        $class = '\\Http\Middlewares\FinalHandler\\Zend';
        $handler = new $class(
            [
                'env' => $this->container->get('env')->getValue()
            ],
            $response
        );
        $handler->setContainer($this->container);
        return $handler;
    }

    /**
     * Handle a request
     *
     * Takes the pipeline, creates a Next handler, and delegates to the
     * Next handler.
     *
     * If $out is a callable, it is used as the "final handler" when
     * $next has exhausted the pipeline; otherwise, a FinalHandler instance
     * is created and passed to $next during initialization.
     *
     * @param Request  $request  request
     * @param Response $response response
     * @param callable $out      callable
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $out = null;
        $done = $this->getFinalHandler($response);

        $next   = new Next($this->pipeline, $done, $this->container);
        $result = $next($request, $response);

        return ($result instanceof Response ? $result : $response);
    }

}
