<?php

namespace Obullo\Router\Route;

use Obullo\Router\RouterInterface as Router;

/**
 * Attach elements to route
 * 
 * @copyright 2009-2016 Obullo
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
     * Constructor
     * 
     * @param Router $router router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->domain = $router->getDomain();
        $this->group  = $router->getGroup();
    }

    /**
     * Add middleware to current route
     * 
     * @param array|string $routes current uri
     *
     * @return void
     */
    public function toGroup($routes)
    {
        $routes  = (array)$routes;
        $options = $this->group->getOptions();
        $domain  = $this->domain->getName();

        // If we have not middlewares or no domain matches stop the run.
        // 
        if (empty($options['middleware']) || ! $this->domain->match()) {
            return;
        }

        $host = $this->domain->getHost();  // We have a problem when the host is subdomain 
                                           // but config domain not. This fix the isssue.

        // Attach Regex Support
        // 
        if ($this->domain->isSub($domain)) {

            $host = str_replace(
                $this->domain->getSubName($domain),
                '',
                $this->domain->getHost()
            );
        }
        if ($domain != $host) {
            return;
        }
        foreach ($routes as $route) {
            $this->toAttach($options['middleware'], $route, $options);
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
        $this->toAttach($middlewares, $lastRoute['match']);
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
        foreach ((array)$middlewares as $middleware) {
            $this->attach[$this->domain->getName()][] = array(
                'name' => $middleware,
                'options' => $options,
                'route' => $route, 
                'attach' => $route
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
        // print_r($this->attach);

        if (! isset($this->attach[$this->domain->getHost()])) {  // Check first
            return array();
        }
        return $this->attach[$this->domain->getHost()];
    }

}