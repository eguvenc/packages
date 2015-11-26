<?php

namespace Obullo\Router\Route;

use Obullo\Router\RouterInterface as Router;

/**
 * Attach elements to route
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Attach
{
    /**
     * Router
     *
     * @var object
     */
    protected $router;

    /**
     * Domain
     * 
     * @var string
     */
    protected $domain;

    /**
     * Current domain name
     * 
     * @var string
     */
    protected $domainName;

    /**
     * Group data
     * 
     * @var array
     */
    protected $group = array();

    /**
     * Attached middleware data
     * 
     * @var array
     */
    protected $attach = array();

    /**
     * Host
     * 
     * @var string
     */
    protected $host;

    /**
     * Constructor
     * 
     * @param Router $router router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->domain = $router->getDomain();
        $this->group  = $router->getGroup()->getArray();
        $this->host   = $this->domain->getHost();
        $this->domainName = $this->domain->getName();
    }

    /**
     * Add middleware to current route
     * 
     * @param string $route current uri
     *
     * @return void
     */
    public function add($route)
    {
        $match = $this->domain->match($this->group);
                                                          // Domain Regex Support, if we have defined domain and not match with host don't run the middleware.
        if (isset($this->group['domain']) && ! $match) {  // If we have defined domain and not match with host don't run the middleware.
            return;
        }
        // Attach Regex Support

        $host = str_replace(
            $this->domain->getSubName($this->domainName),
            '',
            $this->host
        );

        if (! $this->domain->isSub($this->domainName) && $this->domain->isSub($this->host)) {
            $host = $this->host;  // We have a problem when the host is subdomain but config domain not. This fix the isssue.
        }
        
        if ($this->domainName != $host) {
            return;
        }

        if (! isset($this->group['domain'])) {
            $this->group['domain'] = $this->domain->getImmutable();
        }

        if (isset($this->group['middleware'])) {

            $this->toAttach(
                $this->group['middleware'],
                $route,
                $this->group
            );
        }
    }

    /**
     * Add middleware to current route
     * 
     * @param mixed $middlewares string|array
     * 
     * @return void
     */
    public function toRoute($middlewares)
    {
        $routes = $this->router->getRoute()->getArray();
        $lastRoute = end($routes);
        $route = $lastRoute['match'];

        $this->toAttach($middlewares, $route);
    }

    /**
     * Configure attached middleware
     * 
     * @param string|array $middlewares arguments
     * @param string       $route       route
     * @param array        $options     arguments
     * 
     * @return void
     */
    public function toAttach($middlewares, $route, $options = array())
    {
        $middlewares = (array)$middlewares;

        foreach ($middlewares as $middleware) {

            $this->attach[$this->domainName][] = array(
                'name' => $middleware,
                'options' => $options,
                'route' => trim($route, '/'), 
                'attachedRoute' => trim($route)
            );
        }
    }

    /**
     * Get middlewares
     * 
     * @return array
     */
    public function getArray()
    {
        if (! isset($this->attach[$this->domainName])) {  // Check first
            return array();
        }
        return $this->attach[$this->domainName];
    }

}