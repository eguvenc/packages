<?php

namespace Obullo\Application\Annotations;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Annotations Methods
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Methods
{
    /**
     * When counter
     * 
     * @var array
     */
    protected $when = [];

    /**
     * Container
     * 
     * @var object
     */
    protected $count = 0;

    /**
     * Http request
     * 
     * @var string
     */
    protected $request;

    /**
     * Container dependency
     * 
     * @var object
     */
    protected $dependency;

    /**
     * Middleware stack
     * 
     * @var object
     */
    protected $middlewareStack;

    /**
     * Constructor
     * 
     * @param ServerRequestInterface         $request    request
     * @param \Obullo\Container\Dependency   $dependency manager
     * @param \Obullo\Application\Middleware $middleware stack object
     */
    public function __construct(Request $request, $dependency, $middleware)
    {
        $this->request = $request;
        $this->dependency = $dependency;
        $this->middlewareStack = $middleware;
    }

    /**
     * Add new middleware(s)
     * 
     * @param mixed $middleware name
     * 
     * @return object
     */
    public function add($middleware)
    {
        if (! is_array($middleware)) {      // Do we have any possible parameters ?
            $middleware = array($middleware);
        }
        $this->doWhen($middleware, 'add');
        return $this;
    }

    /**
     * Remove middleware(s)
     * 
     * @param mixed $middleware name
     * 
     * @return object
     */
    public function remove($middleware)
    {
        if (! is_array($middleware)) {      // Do we have any possible parameters ?
            $middleware = array($middleware);
        }
        $this->doWhen($middleware, 'remove');
        return $this;
    }

    /**
     * Initialize to after filters
     * 
     * @param string|array $params http method(s): ( post, get, put, delete )
     * 
     * @return object
     */
    public function when($params)
    {
        if (is_string($params)) {
            $params = array($params);
        }
        $this->when[] = $params;
        return $this;
    }

    /**
     * Initialize to allowed methods filters
     * 
     * @param string|array $params parameters
     * 
     * @return void
     */
    public function method($params = null)
    {
        if (is_string($params)) {
            $params = array($params);
        }
        $this->middlewareStack
            ->add('NotAllowed')
            ->setParams($params);
        return;
    }

    /**
     * Do when filter
     *
     * @param array  $middlewares names
     * @param string $method      operation name
     * 
     * @return void
     */
    protected function doWhen(array $middlewares, $method = 'add')
    {
        $when = count($this->when);

        if ($when == 0) {
            $this->callMiddlewareStack($middlewares, $method);
            return;
        }
        $allowedMethods = array_map(
            function ($value) { 
                return strtoupper($value);
            },
            end($this->when)
        );
        if (in_array($this->request->getMethod(), $allowedMethods)) {

            $this->callMiddlewareStack($middlewares, $method);
            $this->when = array();  // reset when
        }
    }

    /**
     * Add middlewares to application
     * 
     * @param array  $middlewares names
     * @param string $method      add / remove
     * 
     * @return void
     */
    protected function callMiddlewareStack(array $middlewares, $method = 'add')
    {
        foreach ($middlewares as $name) {
            $this->middlewareStack->$method(ucfirst($name));
        }
    }
    
}