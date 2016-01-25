<?php

namespace Obullo\Http\Relay;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Exception;
use Relay\RelayBuilder;
use Obullo\Http\Middleware\MiddlewareInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Relay wrapper
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
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
     * Constructor
     * 
     * @param container $container container
     */
    public function __construct(Container $container)
    {   
        $this->container = $container;
    }

    /**
     * Get app request
     * 
     * @return object
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * Returns to final handler class
     *
     * @param Response $response response
     * 
     * @return object
     */
    public function getFinalHandler($response)
    {
        $class = '\\Http\Middlewares\FinalHandler\\Relay';
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
     * Creates relay application
     * 
     * @param Psr\Http\Message\ServerRequestInterface $request  request
     * @param Psr\Http\Message\ResponseInterface      $response response
     * @param callable                                $out      final handler
     * 
     * @return response object
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $out = $err = null;
        $done = $this->getFinalHandler($response);

        try {
                    
            $dispatcher = $this->pipe(
                $this->container->get('middleware')->getQueue(),
                $response
            );
            $response = $dispatcher($request, $response);

        } catch (Exception $e) {
        
            $err = $e;
        }
        return $done($request, $response, $err);
    }

    /**
     * Returns to relayBuilder
     * 
     * @param array $queue middleware queue
     * 
     * @return object
     */
    public function pipe(array $queue)
    {
        $relay = new RelayBuilder;
        return $relay->newInstance(
            $queue
        );
    }

}
