<?php

namespace Obullo\Router\Route;

use Closure;
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
     * @return object
     */
    public function add($uri, Closure $closure, $options = array())
    {
        if (! empty($uri) && ! $this->match($uri)) {
            return $this;
        }
        if (! $this->domain->match($options)) {  // When groups run, if domain not match with regex don't continue.
            return $this;                        // Forexample we define a sub domain but group domain does not match
        }                                        // so we need to stop the propagation.
        $this->options = $options;
        $closure = Closure::bind(
            $closure,
            $this->router,
            get_class($this->router)
        );
        $subname = $this->getSubDomainValue($options);
        $closure(['subname' => $subname]);

        $this->reset();  // Reset group variable after foreach group definition
    }

    /**
     * Before regex check natural uri match 
     * 
     * @param string $match match url or regex
     * 
     * @return boolean
     */
    protected function match($match)
    {
        $exp = explode('/', trim($this->uri->getPath(), "/"));
        return in_array(trim($match, "/"), $exp, true);
    }

    /**
     * Get subdomain value
     *
     * @param array $options group data
     * 
     * @return string
     */
    protected function getSubDomainValue(array $options)
    {
        $matches = $this->domain->getMatches();
        $domainName = (isset($options['domain'])) ? $options['domain'] : null;

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
    public function reset()
    {
        $this->options = array('name' => 'UNNAMED', 'domain' => null);
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