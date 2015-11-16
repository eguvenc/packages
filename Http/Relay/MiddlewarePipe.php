<?php

namespace Obullo\Http\Relay;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Exception;
use Relay\RelayBuilder;
use Obullo\Http\Middleware\MiddlewareInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * Relay wrapper
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class MiddlewarePipe implements MiddlewareInterface
{
    /**
     * Container
     * 
     * @var object
     */
    protected $c;

    /**
     * Constructor
     * 
     * @param Obullo\Container\ContainerInterface $container container
     */
    public function __construct(Container $container)
    {   
        $this->c = $container;
    }

    /**
     * Get app request
     * 
     * @return object
     */
    public function getRequest()
    {
        return $this->c['request'];
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
                'env' => $this->c['app.env']
            ],
            $response
        );
        $handler->setContainer($this->c);
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
                    
            $dispatcher = $this->pipe($this->c['middleware']->getQueue(), $response);
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
