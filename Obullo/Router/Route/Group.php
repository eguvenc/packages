<?php

namespace Obullo\Router\Route;

use Closure;
use Obullo\Router\Route\Attach;
use Psr\Http\Message\UriInterface as Uri;
use Obullo\Router\RouterInterface as Router;

/**
 * Group functionality
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Group
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
     * @var object
     */
    protected $domain;

    /**
     * Route group data
     * 
     * @var array
     */
    protected $options = array();

    /**
     * Constructor
     * 
     * @param Router $router router
     * @param Uri    $uri    uri
     */
    public function __construct(Router $router, Uri $uri)
    {
        $this->router = $router;
        $this->uri    = $uri;
        $this->domain = $router->getDomain();
    }

    /**
     * Create grouped routes
     * 
     * @param route  $uri     route
     * @param object $closure which contains $this->attach(); methods
     * @param array  $options domain, directions and middleware name
     * 
     * @return bool|void
     */
    public function addGroup($uri, Closure $closure, $options = array())
    {
        if (! empty($uri) && ! $this->uriMatch($uri)) {
            return;
        }
        if (! $this->domain->match()) {  // When groups run, if domain not match with regex don't continue.
            return false;                        // Forexample we define a sub domain but group 
                                                 // domain doesn't match we need to stop the propagation.
        }
        $this->options = $options;
        $closure = Closure::bind(
            $closure,
            $this->router,
            get_class($this->router)
        );
        $subname = $this->getSubDomainValue();
        $closure(['subname' => $subname]);
    }

    /**
     * Add middleware
     * 
     * @param array $middleware middleware
     *
     * @return void
     */
    public function add($middleware)
    {
        if (is_string($middleware)) {
            $this->options['middleware'] = (array)$middleware;
            return $this;
        }
        $this->options['middleware'] = $middleware;
        return $this;
    }

    /**
     * Attach route to middleware
     * 
     * @param string $route middleware route
     * 
     * @return object
     */
    public function attach($route = "*")
    {
        $this->router->getAttach()->toGroup($route);
        return $this;
    }

    /**
     * Attach route to middleware with regex
     * 
     * @param string $route middleware route
     * 
     * @return [type] [description]
     */
    public function attachRegexp($route = ".*")
    {
        $this->router->getAttach()->toGroup($route);
        return $this;
    }

    /**
     * Check uri match 
     * 
     * @param string $match match url or regex
     * 
     * @return boolean
     */
    protected function uriMatch($match)
    {
        $exp = explode('/', trim($this->uri->getPath(), "/"));
        return in_array(trim($match, "/"), $exp, true);
    }

    /**
     * Get subdomain value
     * 
     * @return string
     */
    protected function getSubDomainValue()
    {
        $matches = $this->domain->getMatches();
        $domainName =  $this->domain->getName(); // (empty($options['domain'])) ? null : $options['domain'];

        $sub = false;
        if (isset($matches[$domainName])) {
            $sub = strstr($matches[$domainName], '.', true);
        }
        return $sub;
    }

    /**
     * Reset group options
     * 
     * @return void
     */
    public function end()
    {
        $this->domain->setName($this->domain->getImmutable());
        $this->options = array();
    }

    /**
     * Returns to current group value
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

}