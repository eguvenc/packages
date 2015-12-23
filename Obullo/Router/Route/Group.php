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
     * Route groups
     * 
     * @var array
     */
    protected $group = array();

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
     * @param array  $group   domain, directions and middleware name
     * @param object $closure which contains $this->attach(); methods
     * 
     * @return object
     */
    public function add(array $group, Closure $closure)
    {
        if (isset($group['match']) && ! $this->match($group)) {
            return $this;
        }
        if (! $this->domain->match($group)) {  // When groups run, if domain not match with regex don't continue.
            return $this;                      // Forexample we define a sub domain but group domain does not match
        }                                      // so we need to stop the propagation.
        $this->group = $group;

        $closure = Closure::bind(
            $closure,
            $this->router,
            get_class($this->router)
        );

        $subDomain = $this->getSubDomainValue($group);
        $closure($subDomain);

        $this->reset();  // Reset group variable after foreach group definition
    }

    /**
     * Detect class namespace
     * 
     * @param array $group array
     * 
     * @return bool
     */
    protected function match(array $group)
    {
        if ($this->hasMatch($group['match'])) {
            return true;
        }
        return false;
    }

    /**
     * Does group has match ? ( ['match' => $regex] )
     *
     * @param string $match class namespace
     * 
     * @return bool
     */
    protected function hasMatch($match)
    {
        $uri = ltrim($this->uri->getPath(), '/');

        if ($this->hasNaturalMatch($match, $uri)) {
            return true;
        }
        if ($this->router->hasRegexMatch($match, $uri)) {
            return true;
        }
        return false;

    }

    /**
     * Before regex check natural uri match 
     * 
     * @param string $match match url or regex
     * @param string $uri   uri string
     * 
     * @return boolean
     */
    protected function hasNaturalMatch($match, $uri)
    {
        $exp = explode('/', $uri);
        $uriStr = '';
        $keyValues = array_keys(explode('/', $match));
        foreach ($keyValues as $k) {
            if (isset($exp[$k])) {
                $uriStr.= strtolower($exp[$k]).'/';
            }
        }
        if ($match == rtrim($uriStr, '/')) {
            return true;
        }
        return false;
    }

    /**
     * Get subdomain value
     *
     * @param array $group data
     * 
     * @return string
     */
    protected function getSubDomainValue(array $group)
    {
        $matches = $this->domain->getMatches();
        $domainName = (isset($group['domain'])) ? $group['domain'] : null;

        $sub = false;
        if (isset($matches[$domainName])) {
            $sub = strstr($matches[$domainName], '.', true);
        }
        return $sub;
    }

    /**
     * Reset group variable
     * 
     * @return void
     */
    public function reset()
    {
        $this->group = array('name' => 'UNNAMED', 'domain' => null);
    }

    /**
     * Returns to current group value
     * 
     * @return array
     */
    public function getArray()
    {
        return $this->group;
    }

}