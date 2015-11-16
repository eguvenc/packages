<?php

namespace Obullo\Application\Annotations;

use Obullo\Event\EventInterface as Event;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Annotations Middleware Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Middleware
{
    /**
     * Event
     * 
     * @var object
     */
    protected $event;

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
     * Application middleware
     * 
     * @var object
     */
    protected $middleware;

    /**
     * Constructor
     * 
     * @param EventInterface                 $event      event
     * @param ServerRequestInterface         $request    request
     * @param \Obullo\Container\Dependency   $dependency object
     * @param \Obullo\Application\Middleware $middleware object
     */
    public function __construct(Event $event, Request $request, $dependency, $middleware)
    {
        $this->event = $event;
        $this->request = $request;
        $this->dependency = $dependency;
        $this->middleware = $middleware;
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
        $allowedMethods = end($this->when);  // Get the last used when method values
        $when = count($this->when);

        if ($when > 0 && in_array($this->request->getMethod(), $allowedMethods)) {
            $this->addMiddleware($middleware);
            $this->when = array();  // reset when
            return $this;
        } elseif ($when == 0) {
            $this->addMiddleware($middleware);
        }
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
        foreach ($middleware as $name) {
            $this->middleware->remove(ucfirst($name));
        }
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
        $this->middleware->queue('NotAllowed')->setParams($params);
        return;
    }

    /**
     * Subscribe to events
     *
     * @param string $namespace event subscribe listener
     * 
     * @return void
     */
    public function subscribe($namespace)
    {
        $Class = '\\'.ltrim($namespace, '\\');
        $allowedMethods = end($this->when);  // Get the last used when method values
        $when = count($this->when);

        if ($when > 0 && in_array($this->request->getMethod(), $allowedMethods)) {
            $event = new $Class;
            $this->dependency->resolveDependencies($Class);
            $this->event->subscribe($event);
            $this->when = array();  // Reset when
            return $this;
        } elseif ($when == 0) {
            $this->event->subscribe(new $Class);
        }
    }

    /**
     * Add middlewares to application
     * 
     * @param array $middlewares names
     * 
     * @return void
     */
    protected function addMiddleware(array $middlewares)
    {
        foreach ($middlewares as $name) {
            $this->middleware->queue(ucfirst($name));
        }
    }
    
}